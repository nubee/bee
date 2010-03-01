<?php

class DummyCommand extends nbCommand
{
  public function __construct($name = null, nbArgumentSet $arguments = null, nbOptionSet $options = null)
  {
    if(!$arguments)
      $arguments = new nbArgumentSet();
    if(!$options)
      $options = new nbOptionSet();
    
    $this->setName($name);
    $this->setArguments($arguments);
    $this->setOptions($options);
  }
}