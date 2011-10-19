<?php

class nbEnablePluginCommand extends nbCommand {

  protected function configure() {
    $this->setName('bee:enable-plugin')
      ->setBriefDescription('Enables a plugin')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('plugin-name', nbArgument::REQUIRED, 'plugin name'),
        new nbArgument('project-dir', nbArgument::OPTIONAL, 'Project directory', '.'),
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('force', 'f', nbOption::PARAMETER_NONE, 'Overwrites the existing configuration'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $force = isset($options['force']);

    $beeConfigurationFile = $arguments['project-dir'] . '/.bee/bee.yml';
    
    if (!file_exists(nbConfig::get('nb_plugin_dir') . '/' . $arguments['plugin-name']))
      throw new Exception('plugin ' . $arguments['plugin-name'] . ' not found in ' . nbConfig::get('nb_plugin_dir'));
    
    if (!file_exists($beeConfigurationFile))
      throw new Exception($beeConfigurationFile . ' not found');
    
    $configParser = sfYaml::load($beeConfigurationFile);
    
    if (!isset($configParser['proj']['bee']['plugins_enabled']))
      $configParser['proj']['bee']['plugins_enabled'] = array();

    $plugins = $configParser['proj']['bee']['plugins_enabled'];
    
    if (!in_array($arguments['plugin-name'], $plugins)) {
      array_push($configParser['proj']['bee']['plugins_enabled'], $arguments['plugin-name']);
      $yml = sfYaml::dump($configParser, 10);
      file_put_contents($beeConfigurationFile, $yml);
    } else {
      $this->logLine('Plugin ' . $arguments['plugin-name'] . ' already installed');
    }
    
    if (file_exists(nbConfig::get('nb_plugin_dir') . '/' . $arguments['plugin-name'] . '/config/' . $arguments['plugin-name'] . '.yml'))
      nbFileSystem::getInstance()->copy(
        nbConfig::get('nb_plugin_dir') . '/' . $arguments['plugin-name'] . '/config/' . $arguments['plugin-name'] . '.yml',
        $arguments['project-dir'] . '/.bee/' . $arguments['plugin-name'] . '.yml',
        $force
      );
    
    return true;
  }

}