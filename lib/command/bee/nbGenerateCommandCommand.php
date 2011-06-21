<?php

/**
 * Generates a new command.
 *
 * @package    bee
 * @subpackage command
 */
class nbGenerateCommandCommand extends nbCommand {

  protected function configure() {
    $this->setName('bee:generate-command')
            ->setBriefDescription('Generates a new bee command')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

   <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
                new nbArgument('command_name', nbArgument::REQUIRED, 'The complete command name (namespace:command)'),
                new nbArgument('class_name', nbArgument::REQUIRED, 'The class name'),
            )));

    $this->setOptions(new nbOptionSet(array(
                new nbOption('force', 'f', nbOption::PARAMETER_NONE, 'This option overwrites the existing file'),
                new nbOption('directory', 'd', nbOption::PARAMETER_REQUIRED, 'Create command files inside directory'),
                new nbOption('plugin', 'p', nbOption::PARAMETER_REQUIRED, 'Create command files inside plugin structure')
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    list($namespace, $commandName) = explode(':', $arguments['command_name']);

    if (null === $commandName) {
      $commandName = $namespace;
      $namespace = '';
    }

    $className = $arguments['class_name'];
    $f = false;

    if (isset($options['directory']))
      $targetDir = $options['directory'];
    if (isset($options['plugin']))
      $targetDir = 'plugin/'.$options['plugin'].'/command';
    if (!isset($options['plugin']) && !isset($options['directory']))
      $targetDir = nbConfig::get('nb_command_dir');

    $this->log('Creating folder ' . $targetDir . '/' . $namespace, nbLogger::COMMENT);
    $this->log("\n");
    try {
      nbFileSystem::mkdir($targetDir . '/' . $namespace);
    } catch (Exception $e) {
      $this->log('mkdir: the folder already exists ... skipping', nbLogger::INFO);
      $this->log("\n");
    }
    $this->log('Copying ' . nbConfig::get('nb_template_dir') . '/beeCommand.tpl' . ' in ' .
            $targetDir . '/' . $namespace . '/' . $className . '.php', nbLogger::COMMENT);
    $this->log("\n");

    if (isset($options['force']))
      $f = true;

    $file = $targetDir . '/' . $namespace . '/' . $className . '.php';
    try {
      nbFileSystem::copy(nbConfig::get('nb_template_dir') . '/beeCommand.tpl'
                      , $file, $f);
    } catch (Exception $e) {
      $this->log($e->getMessage(), nbLogger::ERROR);
    }

    $search_string = array('%%CLASSNAME%%', '%%NAMESPACE%%', '%%NAME%%');
    $replace_string = array($className, $namespace, $commandName);

    nbFileSystem::replaceTokens($search_string, $replace_string, $file);
  }

}