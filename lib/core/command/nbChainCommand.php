<?php

/**
 * Defines an command that executes a sequence of other commands
 *
 * @package    bee
 * @subpackage command
 */
class nbChainCommand extends nbCommand
{
  private $alias;
  protected $commands = array();

  public function  __construct($alias, array $commands = array())
  {
    $this->alias = $alias;
    $this->commands = $commands;
    parent::__construct();
  }

  public function addCommand(nbCommand $command)
  {
    $this->commands[] = $command;
    $briefDescription = $this->getBriefDescription() . ' -> ' . $command->getFullName();
    $this->setBriefDescription($briefDescription);

//    foreach ($command->getArgumentsArray() as $argument)
//      $this->addArgument($argument);
//
//    foreach ($command->getOptionsArray() as $option)
//      $this->addOption($option);
  }

  protected function configure()
  {
    $this->setName($this->alias);
    $briefDescription = 'This command executes: ';
    foreach ($this->commands as $command)
      $briefDescription .= ' -> ' . $command->getFullName();
    $this->setBriefDescription($briefDescription);
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $ret = true;
    foreach ($this->commands as $command)
    {
      $commandArgs = array();
      foreach ($command->getArgumentsArray() as $argument)
        $commandArgs[$argument->getName()] = $argument->getValue();
      $this->log($this->formatLine('Executing command ' . $command->getFullName(), nbLogger::INFO));
      $ret = $command->execute($commandArgs, $options) && $ret;
    }
    return $ret;
  }
}
