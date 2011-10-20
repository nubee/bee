<?php

class nbConfigPluginCommand extends nbCommand {

  protected function configure() {
    $this->setName('bee:config-plugin')
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
        new nbOption('force', 'f', nbOption::PARAMETER_NONE, 'Overwrites the existing configuration'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $pluginName = $arguments['plugin-name'];
    $this->logLine('Configuring plugin: ' . $pluginName, nbLogger::COMMENT);
    
    $force = isset($options['force']);
    
    $pluginsDir = nbConfig::get('nb_plugins_dir');

    $source = sprintf('%s/%s/config', $pluginsDir, $pluginName);
    $destination = './.bee';

    if (!file_exists($source))
      throw new LogicException('Plugin ' . $pluginName . ' not found in ' . nbConfig::get('nb_plugins_dir'));

    $files = nbFileFinder::create('file')
      ->remove('.')
      ->remove('..')
      ->in($source);

    foreach ($files as $file) {
      $this->getFileSystem()->copy($file, $destination, $force);
      $this->logLine('file+: ' . $destination . '/' . basename($file), nbLogger::INFO);
    }

    $this->logLine('Done: bee:config-plugin', nbLogger::COMMENT);
    
    return true;
  }

}