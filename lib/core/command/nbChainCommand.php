<?php

/**
 * Defines an command that executes a sequence of other commands
 *
 * @package    bee
 * @subpackage command
 */
abstract class nbChainCommand extends nbCommand
{
  private $commands = array();

  protected function setCommandChain(array $commands)
  {
    $this->commands = $commands;
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $ret = true;
    foreach ($this->commands as $command)
    {
      echo "[nbChainCommand::execute]\n";
      $ret = $ret && $command->execute($arguments, $options);
    }
    return $ret;
  }
}
