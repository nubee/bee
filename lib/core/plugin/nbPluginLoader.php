<?php
class nbPluginLoader {

  static protected $instance;
  private $plugins = array(),
          $pluginDir;

  private function __construct($pluginDir)
  {
    if(! is_dir($pluginDir))
      throw new InvalidArgumentException('[nbPluginLoader::__construct] '.$pluginDir.' isn\'t a directory.');
    $this->pluginDir = $pluginDir;
  }

  /**
   * Retrieves the singleton instance of this class.
   *
   *
   * @return PluginLoader   A nbPluginLoader implementation instance.
   */
  static public function getInstance()
  {
    if (!isset(self::$instance))
      self::$instance = new nbPluginLoader(nbConfig::get('nb_plugin_dir','plugin'));

    return self::$instance;
  }

  /**
   * Register All plugins in pluginDir.
   *
   */
  public function loadAllPlugins()
  {

    $plugins = nbFileFinder::create('dir')
      ->add('*Plugin')->in($this->pluginDir);
    foreach($plugins as $plugin)
    {
      //remove "Plugin" from the end of $plugin
      $plugin = substr_replace($plugin, '', -6);
      $this->addPlugin(basename($plugin));
    }
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
    nbLogger::getInstance()->logLine('Loading Plugin <info>'.$pluginName.'</info>',nbLogger::COMMENT);
    if(in_array($pluginName, $this->plugins))
      return;
    $this->plugins[] = $pluginName;

    nbAutoload::getInstance()->addDirectory(nbConfig::get('nb_plugin_dir').'/'.$pluginName.'Plugin/lib','*.php',true);
    nbAutoload::getInstance()->addDirectory(nbConfig::get('nb_plugin_dir').'/'.$pluginName.'Plugin/command','*Command.php',true);
    nbAutoload::getInstance()->addDirectory(nbConfig::get('nb_plugin_dir').'/'.$pluginName.'Plugin/vendor');

    if(! file_exists(nbConfig::get('nb_plugin_dir').'/'.$pluginName.'Plugin/config/config.yml'))
      return;

    $yamlParser = new nbYamlConfigParser();
    $yamlParser->parseFile(nbConfig::get('nb_plugin_dir').'/'.$pluginName.'Plugin/config/config.yml');
  }

  public function getPlugins()
  {
    return $this->plugins;
  }

}