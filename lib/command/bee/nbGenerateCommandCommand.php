<?php
class nbGenerateCommandCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('generate:command')
      ->setBriefDescription('')
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
    //$command = $this;
  }
}


