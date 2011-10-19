<?php

/**
 * Generates a new command.
 *
 * @package    bee
 * @subpackage command
 */
class nbGenerateCommandCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('bee:generate-command')
      ->setBriefDescription('Generates a new bee command')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

   <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('command-name', nbArgument::REQUIRED, 'The complete command name (namespace:command)'),
        new nbArgument('class-name', nbArgument::REQUIRED, 'The class name'),
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('force', 'f', nbOption::PARAMETER_NONE, 'This option overwrites the existing file'),
        new nbOption('directory', 'd', nbOption::PARAMETER_REQUIRED, 'Create command files inside directory'),
        new nbOption('plugin', 'p', nbOption::PARAMETER_REQUIRED, 'Create command files inside plugin structure')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $commandName = $arguments['command-name'];
    if(strpos($commandName, ':') > 0) {
      list($namespace, $commandName) = explode(':', $commandName);
    }
    else
      $namespace = '';

    $className = $arguments['class-name'];

    if(isset($options['directory']))
      $targetDir = $options['directory'];
    if(isset($options['plugin']))
      $targetDir = 'plugin/' . $options['plugin'] . '/command';
    if(!isset($options['plugin']) && !isset($options['directory']))
      $targetDir = nbConfig::get('nb_command_dir');

    $path = $targetDir . '/' . $namespace;
    $this->log('Creating folder ' . $path, nbLogger::COMMENT);
    $this->log("\n");
    try {
      $this->getFileSystem()->mkdir($path);
    }
    catch(Exception $e) {
      $this->logLine('mkdir: the folder already exists ... skipping', nbLogger::INFO);
      $this->log("\n");
    }

    $file = $path . '/' . $className . '.php';

    $this->logLine(sprintf('Copying %s/beeCommand.tpl in %s', nbConfig::get('nb_template_dir'), $file));

    $force = isset($options['force']) ? true : false;

    try {
      $this->getFileSystem()->copy(nbConfig::get('nb_template_dir') . '/beeCommand.tpl', $file, $force);
    }
    catch(Exception $e) {
      $this->log($e->getMessage(), nbLogger::ERROR);
    }

    $search = array('%%CLASSNAME%%', '%%NAMESPACE%%', '%%NAME%%');
    $replace = array($className, $namespace, $commandName);

    $this->getFileSystem()->replaceTokens($search, $replace, $file);
  }

}