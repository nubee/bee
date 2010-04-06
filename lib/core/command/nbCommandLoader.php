<?php

class nbCommandLoader
{
  protected $commands,
          $dirs = array();

  public function  __construct()
  {
    $this->commands = new nbCommandSet();
    $this->dirs[nbConfig::get('nb_command_dir')] = false;
  }

  public function addDir($dir)
  {
    if(!is_dir($dir))
      throw new InvalidArgumentException('[nbCommandLoader::addDir] invalid directory:'.$dir);
    if(key_exists($dir, $this->dirs))
      return;
    
    $this->dirs[$dir] = false;
  }

  public function addCommandsFromDir($dir)
  {
    if(!is_dir($dir))
      return;
    if(key_exists($dir, $this->dirs) && $this->dirs[$dir])
      return;

    $finder = nbFileFinder::create('file')->add('*Command.php');
    $this->commandFiles = array();
    foreach ($finder->in($dir) as $file)
      $this->commandFiles[basename($file, '.php')] = $file;
    // register local autoloader for tasks
    spl_autoload_register(array($this, 'autoloadCommand'));

    foreach ($this->commandFiles as $command => $file) {
      // forces autoloading of each command class
      $this->commands->addCommand(new $command());
    }
    // unregister local autoloader
    spl_autoload_unregister(array($this, 'autoloadCommand'));
    
    $this->dirs[$dir]=true;

  }

  public function getCommands()
  {
    return $this->commands;
  }

  public function loadCommands()
  {
    foreach($this->dirs as $dir=>$loaded)
            $this->addCommandsFromDir($dir);
//    $finder = nbFileFinder::create('file')->add('*Command.php');
//    $this->commandFiles = array();
//    foreach ($finder->in($this->dirs) as $file)
//      $this->commandFiles[basename($file, '.php')] = $file;
//
//    // register local autoloader for tasks
//    spl_autoload_register(array($this, 'autoloadCommand'));
//
//    // require tasks
//    $commands = array();
//    foreach ($this->commandFiles as $command => $file) {
//      // forces autoloading of each command class
//      $this->commands->addCommand(new $command());
//    }
//
//    // unregister local autoloader
//    spl_autoload_unregister(array($this, 'autoloadCommand'));
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
//    echo "[nbApplication::autoloadCommand] $class\n";
    if (isset($this->commandFiles[$class])) {
      require_once $this->commandFiles[$class];
      return true;
    }
    return false;
  }

  public function loadCommandAliases()
  {
    if (!nbConfig::has('proj_commands'))
      return;

    $namespaces = nbConfig::get('proj_commands');
    foreach ($namespaces as $namespace => $commands)
    {
      if ($namespace === 'default')
        $namespace = '';
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
