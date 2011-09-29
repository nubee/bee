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
    $this->logLine('Running: bee:config-plugin', nbLogger::COMMENT);
    $force = isset($options['force']);

    $source = nbConfig::get('nb_plugin_dir') . '/' . $arguments['plugin-name'] . '/config';
    $destinaiton = './.bee';

    if (!file_exists($source))
      throw new LogicException(sprintf("
[nbConfigPluginCommand::execute] Error:
  %s
", 'plugin ' . $arguments['plugin-name'] . ' not found in ' . nbConfig::get('nb_plugin_dir')
      ));

    $files = nbFileFinder::create('file')
      ->remove('.')
      ->remove('..')
      ->in($source);

    foreach ($files as $file) {
      nbFileSystem::copy($file, $destinaiton, $force);
      $this->logLine('file+: ' . $destinaiton . '/' . basename($file), nbLogger::INFO);
    }

    $this->logLine('Done: bee:config-plugin', nbLogger::COMMENT);
    return true;
  }

}