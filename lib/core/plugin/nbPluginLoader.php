<?php
class nbPluginLoader {

  private $plugins = array(),
          $pluginDirs = array();
  static private        $commandLoader;


  public function __construct($pluginDir, nbCommandLoader $commandLoader)
  {
    if(! is_dir($pluginDir))
      throw new InvalidArgumentException('[nbPluginLoader::__construct] '.$pluginDir.' isn\'t a directory.');
    $this->pluginDirs[] = $pluginDir;
    $this->commandLoader = $commandLoader;
  }


  /**
   * Register All plugins in pluginDir.
   *
   */
  public function loadAllPlugins()
  {
    $plugins = nbFileFinder::create('dir')
      ->add('*Plugin')->in($this->pluginDirs);

    foreach($plugins as $plugin)
      $this->addPlugin(basename($plugin));
  }

  /**
   * Register plugins for autoloading.
   *
   */
  public function loadPlugins($plugins = array())
  {
    foreach($plugins as $plugin)
    {
      $this->addPlugin($plugin);
    }
  }

  /**
   * Register a new plugin for autoloading.
   * 
   */
  private function addPlugin($pluginName)
  {
    nbLogger::getInstance()->logLine('Loading Plugin '.$pluginName.'',nbLogger::COMMENT);
    if(key_exists($pluginName, $this->plugins))
      return;

    foreach($this->pluginDirs as $dir)
      if(is_dir($dir.'/'.$pluginName))
        $this->plugins[$pluginName] = $dir.'/'.$pluginName;

    nbAutoload::getInstance()->addDirectory($this->plugins[$pluginName].'/lib','*.php',true);
    nbAutoload::getInstance()->addDirectory($this->plugins[$pluginName].'/command','*Command.php',true);
    nbAutoload::getInstance()->addDirectory($this->plugins[$pluginName].'/vendor');

    $this->commandLoader->addCommandsFromDir($this->plugins[$pluginName].'/command');


    if(! file_exists(nbConfig::get('nb_plugin_dir').'/'.$pluginName.'/config/config.yml'))
      return;

    $yamlParser = new nbYamlConfigParser();
    $yamlParser->parseFile(nbConfig::get('nb_plugin_dir').'/'.$pluginName.'/config/config.yml');
    
  }

  public function getPlugins()
  {
    return $this->plugins;
  }

  public function addDir($dir)
  {
    if(is_dir($dir))
      $this->pluginDirs[] = $dir;
  }

  public function getPluginDirs()
  {
    return $this->pluginDirs;
  }

}