<?php

class FirstCommand extends nbCommand 
{
  public function configure()
  {
    $this->setName('first');
  }
  
  public function execute(array $arguments = array(), array $options = array())
  {
    return false;
  }
}