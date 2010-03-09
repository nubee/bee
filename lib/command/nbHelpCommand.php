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
The <info>{$this->getFullName()}</info> command displays help for a given task:

   <info>./bee {$this->getFullName()} command</info>
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
      $command = $this->getApplication()->getCommand($commandName);
    }

    $this->log($this->formatHelp($command));
  }

  public function formatHelp(nbCommand $command)
  {
    $max = 0;
    foreach($command->getArgumentsArray() as $argument) {
      $length = strlen($argument->getName()) + 2;
      if($max < $length) $max = $length;
    }
    foreach($command->getOptionsArray() as $option) {
      $length = strlen($option->getName()) + 6;
      if($max < $length) $max = $length;
    }

    $res = nbHelpFormatter::formatSynopsys($command->getSynopsys());
    $res .= nbHelpFormatter::formatArguments($command->getArguments(), $max);
    $res .= nbHelpFormatter::formatOptions($command->getOptions(), $max - 6);
    $res .= nbHelpFormatter::formatDescription($command->getDescription());

    return $res;
  }
}
