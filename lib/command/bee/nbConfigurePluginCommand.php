<?php

class nbConfigurePluginCommand extends nbCommand {

  protected function configure() {
    $this->setName('bee:configure-plugin')
      ->setBriefDescription('Copy the default plugin configuration in .bee directory')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('plugin-name', nbArgument::REQUIRED, 'plugin name'),
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('force',      'f', nbOption::PARAMETER_NONE, 'Overwrites the existing configuration'),
        new nbOption('config-dir', '',  nbOption::PARAMETER_REQUIRED, 'Configuration directory'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $pluginName  = $arguments['plugin-name'];
    $force       = isset($options['force']);
    $destination = isset($options['config-dir']) ? $options['config-dir'] : nbConfig::get('nb_config_dir');
    $pluginsDir  = nbConfig::get('nb_plugins_dir');
    $pluginDir   = sprintf('%s/%s', $pluginsDir, $pluginName);
    
    $this->logLine('Configuring plugin: ' . $pluginName, nbLogger::COMMENT);

    if(!is_dir($pluginDir))
      throw new LogicException('Plugin ' . $pluginName . ' not found in ' . $pluginsDir);

    $source = sprintf('%s/%s/config', $pluginsDir, $pluginName);
    $files = nbFileFinder::create('file')
      ->add('*.template.yml')
      ->remove('.')
      ->remove('..')
      ->in($source);

    $generator = new nbConfigurationGenerator();
    $this->getFileSystem()->mkdir($destination, true);
    foreach($files as $file) {
      $target = sprintf('%s/%s', $destination, str_replace('.template.yml', '.yml', basename($file)));
      
      $generator->generate($file, $target, $force);
      $this->logLine('file+: ' . $target, nbLogger::INFO);
    }

    $this->logLine(sprintf('Plugin %s successully configured in %s', $pluginName, $destination), nbLogger::COMMENT);

    return true;
  }

}