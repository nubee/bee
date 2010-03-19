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
  private $commands = array();

  public function  __construct($alias, array $commands = array())
  {
    $this->alias = $alias;
    $this->commands = $commands;
    parent::__construct();
  }

  protected function setCommandChain(array $commands)
  {
    $this->commands = $commands;
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
//      echo "[nbChainCommand::execute] " . $command->getFullName() . "\n";
      $ret = $command->execute($arguments, $options) && $ret;
    }
    return $ret;
  }
}
