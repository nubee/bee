<?php

class DummyNoArgsCommand extends nbCommand
{
  private $executed = false;
  
  protected function configure()
  {
    $this->setName('dummynoargs');
  }
  
  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->executed = true;
  }

  public function hasExecuted()
  {
    return $this->executed;
  }
}