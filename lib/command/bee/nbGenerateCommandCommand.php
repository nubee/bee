<?php
class nbGenerateCommandCommand extends nbCommand
{
  private $application = null;

  protected function configure()
  {
    $this->setName('GenerateCommand')
      ->setBriefDescription('')
      ->setDescription(<<<TXT
The <info>GenerateCommand</info> command displays help for a given task:

   <info>./bee GenerateCommand</info>
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
    $command = $this;
  }
}


