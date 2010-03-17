<?php

class nbCommandLoader
{
  private $commands;

  public function  __construct()
  {
    $this->commands = new nbCommandSet();
    $this->loadCommands();
    $this->loadCommandAliases();
  }

  /**
   * Autodiscovers command classes.
   *
   * @return array An array of command instances
   */
//  public function autodiscoverCommands()
//  {
//    $commands = array();
//    foreach (get_declared_classes() as $class) {
//      $r = new ReflectionClass($class);
//
//      if($r->isSubclassOf('nbCommand') && !$r->isAbstract()) {
//        $commands[] = new $class();
//      }
//    }
//
//    return $commands;
//  }

  public function getCommands()
  {
    return $this->commands;
  }

  public function loadCommands()
  {
    $dirs = array(nbConfig::get('nb_command_dir'));
    foreach( nbPluginLoader::getInstance()->getPlugins() as $pluginName)
      $dirs[] = nbConfig::get('nb_plugin_dir').'/'.$pluginName.'Plugin/command';

    $finder = nbFileFinder::create('file')->add('*Command.php');
    foreach ($finder->in($dirs) as $file)
      $this->commandFiles[basename($file, '.php')] = $file;

    // register local autoloader for tasks
    spl_autoload_register(array($this, 'autoloadCommand'));

    // require tasks
    $commands = array();
    foreach ($this->commandFiles as $command => $file) {
      // forces autoloading of each task class
//      class_exists($command, true);
      $this->commands->addCommand(new $command());
    }

    // unregister local autoloader
    spl_autoload_unregister(array($this, 'autoloadCommand'));
  }

  /**
   * Autoloads a command class
   *
   * @param  string  $class  The command class name
   *
   * @return Boolean true if the command exists
   */
  public function autoloadCommand($class)
  {
    echo "[nbApplication::autoloadCommand] $class\n";
    if (isset($this->commandFiles[$class])) {
      require_once $this->commandFiles[$class];

      return true;
    }

    return false;
  }

  public function loadCommandAliases()
  {
    $namespaces = nbConfig::get('proj_commands');
    foreach ($namespaces as $namespace => $commands)
    {
      foreach ($commands as $alias => $commandName)
      {
        if (is_array($commandName))
        {
          try {
            $command = new nbChainCommand("$namespace:$alias");
            foreach ($commandName as $c)
              $command->addCommand($this->commands->getCommand($c));
          }
          catch (Exception $e)
          {
            echo "$commandName not found (" . $e->getMessage() . ")\n";
          }
        }
        else
          $command = $this->commands->getCommand($commandName);

        try {
          $this->commands->addCommand(
          new nbAliasCommand("$namespace:$alias", $command)
            );
        }
        catch (Exception $e)
        {
          echo "$commandName not found (" . $e->getMessage() . ")\n";
        }
      }
    }
  }

}
