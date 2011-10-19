<?php

class SecondCommand extends nbCommand
{

  public function configure()
  {
    $this->setName('second');
  }

  public function execute(array $arguments = array(), array $options = array())
  {
  }

}
