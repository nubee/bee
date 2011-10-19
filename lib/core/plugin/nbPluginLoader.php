<?php

class nbPluginLoader
{
  private $plugins = array();
  private $pluginDirs = array();
  static private $commandLoader;

  public function __construct($pluginDir, nbCommandLoader $commandLoader)
  {
    if(!is_dir($pluginDir))
      throw new InvalidArgumentException('[nbPluginLoader::__construct] ' . $pluginDir . ' isn\'t a directory.');
    $this->pluginDirs[] = $pluginDir;
    self::$commandLoader = $commandLoader;
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
      $this->addPlugin($plugin);
  }

  /**
   * Register a new plugin for autoloading.
   * 
   */
  private function addPlugin($pluginName)
  {
//    nbLogger::getInstance()->logLine('Loading Plugin <comment>' . $pluginName . '</comment>...');

    if(key_exists($pluginName, $this->plugins))
      return;

    foreach($this->pluginDirs as $dir) {
      $path = $dir . '/' . $pluginName;
      if(is_dir($path)) {
        $this->plugins[$pluginName] = $path;
      }
    }

    if(!key_exists($pluginName, $this->plugins)) {
      nbLogger::getInstance()->logLine('Plugin not found <comment>' . $pluginName . '</comment>', nbLogger::INFO);
      return;
    }

    nbAutoload::getInstance()->addDirectory($this->plugins[$pluginName] . '/lib', '*.php', true);
    nbAutoload::getInstance()->addDirectory($this->plugins[$pluginName] . '/command', '*Command.php', true);
    nbAutoload::getInstance()->addDirectory($this->plugins[$pluginName] . '/vendor');

    self::$commandLoader->addCommandsFromDir($this->plugins[$pluginName] . '/command');
    
    if(is_dir($this->plugins[$pluginName] . '/test/unit')) {
      $testDirs = nbConfig::get('nb_plugin_test_dirs', array());
      $testDirs[] = $this->plugins[$pluginName] . '/test/unit';
      nbConfig::set('nb_plugin_test_dirs', array_unique($testDirs));
    }

    if(!file_exists($this->plugins[$pluginName] . '/config/config.yml'))
      return;

    $yamlParser = new nbYamlConfigParser();
    $yamlParser->parseFile($this->plugins[$pluginName] . '/config/config.yml');
  }

  public function getPlugins()
  {
    return $this->plugins;
  }

  public function addDir($dirs)
  {
    if(!is_array($dirs))
      $dirs = array($dirs);
    
    foreach($dirs as $dir) {
      if(is_dir($dir))
        $this->pluginDirs[] = $dir;
    }
  }

  public function getPluginDirs()
  {
    return $this->pluginDirs;
  }

}
