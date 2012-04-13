<?php

class nbEnablePluginCommand extends nbCommand
{

  protected function configure()
  {
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
        new nbOption('no-configuration', 'n', nbOption::PARAMETER_NONE, 'If specified, configuration files will not generated'),
        new nbOption('force', 'f', nbOption::PARAMETER_NONE, 'Overwrites the existing configuration'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $allPluginsDir = nbConfig::get('nb_plugins_dir');
    $pluginName = $arguments['plugin-name'];
    $projectDir = $arguments['project-dir'];
    $configDir  = $projectDir . '/.bee';
    $beeConfig  = $configDir . '/bee.yml';
    $force = isset($options['force']);
    $pluginPath = $allPluginsDir . '/' . $pluginName;
    $verbose = isset($options['verbose']);

    if(!file_exists($pluginPath))
      throw new Exception('plugin ' . $pluginName . ' not found in ' . nbConfig::get('nb_plugins_dir'));

    if(!file_exists($beeConfig))
      throw new Exception($beeConfig . ' not found');

    $configParser = sfYaml::load($beeConfig);

    $plugins = $configParser['project']['bee']['enabled_plugins'];
    $plugins = isset($configParser['project']['bee']['enabled_plugins'])
      ? $configParser['project']['bee']['enabled_plugins'] : array();

    if(!is_array($plugins))
      $plugins = array();
    
    if(!in_array($pluginName, $plugins)) {
      array_push($plugins, $pluginName);
      $configParser['project']['bee']['enabled_plugins'] = $plugins;
      $yml = sfYaml::dump($configParser, 99);

      file_put_contents($beeConfig, $yml);
      $this->logLine('Installing plugin ' . $pluginName);
    }
    else {
      $this->logLine('Plugin ' . $pluginName . ' already installed');
    }

    // Configuration will not generated
    if (isset($options['no-configuration']))
      return true;
    
    // Configure plugin
    $pluginConfigDir = sprintf('%s/%s/config', $allPluginsDir, $pluginName);
    $files = nbFileFinder::create('file')->add('*.template.yml')->in($pluginConfigDir);

    $generator = new nbConfigurationGenerator();
    $this->getFileSystem()->mkdir($configDir, true);
    
    foreach($files as $file) {
      $target = sprintf('%s/%s', $configDir, str_replace('.template.yml', '.yml', basename($file)));
      
      $generator->generate($file, $target, $force);
      
      if ($verbose)
        $this->logLine('file+: ' . $target, nbLogger::INFO);
    }

    return true;
  }

}