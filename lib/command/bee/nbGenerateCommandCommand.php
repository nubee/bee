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
    $this->setName('generate:command')
      ->setBriefDescription('Generate a new command')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

   <info>./bee {$this->getFullName()}</info>
TXT
        );          

    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('namespace', nbArgument::REQUIRED, 'The namespace name'),
      new nbArgument('command_name', nbArgument::REQUIRED, 'The command name'),
      new nbArgument('class_name', nbArgument::REQUIRED, 'The class name'),
    )));

    $this->setOptions(new nbOptionSet(array(
      new nbOption('force', 'f', nbOption::PARAMETER_NONE, 'This option overwrites the existing file')
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $namespace = $arguments['namespace'];
    $commandName = $arguments['command_name'];
    $className = $arguments['class_name'];
    $f = false;

    $this->log('Creating folder '. nbConfig::get('nb_command_dir') . '/'. $namespace, nbLogger::COMMENT);
    $this->log("\n");
    try {
      nbFileSystem::mkdir(nbConfig::get('nb_command_dir') . '/'. $namespace);
    }
    catch (Exception $e) {
      $this->log('mkdir: the folder already exists ... skipping' , nbLogger::INFO);
      $this->log("\n");
    }
    $this->log('Coping '. nbConfig::get('nb_template_dir'). '/beeCommand.tpl'. ' in ' .
             nbConfig::get('nb_command_dir') . '/'. $namespace . '/' . $className . '.php', nbLogger::COMMENT);
    $this->log("\n");

    if(isset($options['force']))
        $f = true;

    $file = nbConfig::get('nb_command_dir') . '/'. $namespace . '/' .$className . '.php';
    try {
      nbFileSystem::copy(nbConfig::get('nb_template_dir'). '/beeCommand.tpl'
            , $file , $f);
    }
    catch(Exception $e) {
      $this->log($e->getMessage(),nbLogger::ERROR);
    }

    $search_string  = array('%%CLASSNAME%%','%%NAMESPACE%%', '%%NAME%%');
    $replace_string = array($className, $namespace, $commandName);

    nbFileSystem::replaceTokens($search_string, $replace_string, $file);
  }
}