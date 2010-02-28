<?php

class DummyCommand extends nbCommand
{
  public function __construct($name = null, nbArgumentSet $arguments = null)
  {
    if(!$arguments)
      $arguments = new nbArgumentSet();
    
    $this->setName($name);
    $this->setArguments($arguments);
  }

  public function getOptions()
  {
//    return new nbOptionSet();
  }

}