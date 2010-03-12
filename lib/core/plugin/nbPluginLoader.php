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
   * Register a new plugin for autoloading.
   * 
   */
  public function addPlugin($pluginName)
  {
    if(in_array($pluginName, $this->plugins))
      return;
    $this->plugins[] = $pluginName;

    nbAutoload::getInstance()->addDirectory(nbConfig::get('nb_plugin_dir').'/'.$pluginName.'Plugin/lib');
    nbAutoload::getInstance()->addDirectory(nbConfig::get('nb_plugin_dir').'/'.$pluginName.'Plugin/command');
    nbAutoload::getInstance()->addDirectory(nbConfig::get('nb_plugin_dir').'/'.$pluginName.'Plugin/vendor');

    if(! file_exists(nbConfig::get('nb_plugin_dir').'/'.$pluginName.'Plugin/config/config.yml'))
      return;

    $yamlParser = new nbYamlConfigParser();
    $yamlParser->parseFile(nbConfig::get('nb_plugin_dir').'/'.$pluginName.'Plugin/config/config.yml',$pluginName);
  }



}