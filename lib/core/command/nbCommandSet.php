<?php

class nbCommandSet {

  private 
    $commands = array();

  function __construct($commands = array())
  {
    $this->addCommands($commands);
  }

  public function count()
  {
    return count($this->commands);
  }

  public function getCommands()
  {
    return $this->commands;
  }

  public function hasCommand($commandName)
  {
    if(array_key_exists($commandName, $this->commands))
      return true;

    $count = 0;
    foreach($this->commands as $command) {
      if($command->hasShortcut($commandName) || $command->hasAlias($commandName))
        ++$count;
    }

    return 1 === $count;
  }

  public function addCommands($commands = array())
  {
    if(!is_array($commands))
      throw new InvalidArgumentException("[nbCommandSet::addCommands] First argument must be an array");

    foreach ($commands as $command)
      $this->addCommand($command);
  }

  public function addCommand(nbCommand $command)
  {
    if($this->hasCommand($command->getFullName()))
      throw new InvalidArgumentException(sprintf("[nbCommandSet::addCommand] Command %s already exists", $command->getFullName()));

    $this->commands[$command->getFullName()] = $command;
  }

  public function getCommand($commandName)
  {
    if(array_key_exists($commandName, $this->commands))
      return $this->commands[$commandName];

    $commands = array();
    foreach($this->commands as $command) {
      if($command->hasShortcut($commandName) || $command->hasAlias($commandName))
        $commands[] = $command;
    }

    if(count($commands) == 0)
      throw new RangeException('[nbCommandSet::getCommand] Undefined command: ' . $commandName);

    if(count($commands) > 1)
      throw new LogicException('[nbCommandSet::getCommand] Ambiguous command: ' . $commandName);

    return $commands[0];
  }

  public function getValues()
  {
    $res = array();
    foreach ($this->commands as $command)
      $res[$command->getFullName()] = $command->getValue();

    return $res;
  }

  public function  __toString()
  {
    $result = '';
    foreach($this->commands as $command)
      $result .= ' ' . $command;

    return $result;
  }
}