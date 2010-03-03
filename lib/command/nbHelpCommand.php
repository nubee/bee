<?php

/**
 * Prints command help.
 *
 * @package    bee
 * @subpackage command
 */
class nbHelpCommand extends nbApplicationCommand
{
  protected function configure()
  {
    $this->setName('help')
      ->setBriefDescription('Displays help for a command')
      ->setDescription(<<<TXT
The <info>help</info> command displays help for a given task:

   <info>./bee help command</info>
TXT
        );
    
    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('command_name', nbArgument::OPTIONAL, 'The command name'),
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $command = $this;
    if(isset($arguments['command_name'])) {
      $commandName = $arguments['command_name'];
      $command = $this->getApplication()->getCommands()->getCommand($commandName);
    }

    $max = 0;
    foreach($command->getArgumentsArray() as $argument) {
      $length = strlen($argument->getName()) + 2;
      if($max < $length) $max = $length;
    }
    foreach($command->getOptionsArray() as $option) {
      $length = strlen($option->getName()) + 2;
      if($max < $length) $max = $length;
    }
    
    $res = $this->getApplication()->formatHelpString($command->getFullName(),
            $command->getArguments(),
            $command->getOptions(),
            $command->getDescription());
    
    $this->log($res);
  }
}
