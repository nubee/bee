<?php
class nbGenerateCommandCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('generate:command')
      ->setBriefDescription('Generate a new command')
      ->setDescription(<<<TXT
The <info>generate:command</info> command:

   <info>./bee generate:command</info>
TXT
        );          

    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('namespace', nbArgument::REQUIRED, 'The namespace name'),
      new nbArgument('command_name', nbArgument::REQUIRED, 'The command name'),
      new nbArgument('class_name', nbArgument::REQUIRED, 'The class name'),
    )));

    $this->setOptions(new nbOptionSet(array(
      new nbOption('file', 'f', nbOption::PARAMETER_OPTIONAL, 'This option overwrites the existing file')
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $namespace = $arguments['namespace'];
    $commandName = $arguments['command_name'];
    $className = $arguments['class_name'];
    $b = false;

    try
    {
    $this->log('Creating folder '. nbConfig::get('nb_command_dir') . '/'. $namespace, nbLogger::COMMENT);
    $this->log("\n");
      nbFileSystem::mkdir(nbConfig::get('nb_command_dir') . '/'. $namespace);
    }
    catch (Exception $e)
    {
      $this->log('mkdir failed: ' . $e->getMessage(), nbLogger::ERROR);
    }
    $this->log("\n");
    $this->log('Coping '. nbConfig::get('nb_template_dir'). '/beeCommand.tpl'. ' in ' .
             nbConfig::get('nb_command_dir') . '/'. $namespace . '/' . $className . '.php', nbLogger::INFO);
    $this->log("\n");

    if(isset($options['file']))
      $b = true;
    try
    {
      nbFileSystem::copy(nbConfig::get('nb_template_dir'). '/beeCommand.tpl'
            , nbConfig::get('nb_command_dir') . '/'. $namespace . '/' .$className . '.php', $b);
    }
    catch(Exception $e)
    {
      $this->log('copy failed: ' . $e->getMessage(), nbLogger::ERROR);
    }

  }
}
