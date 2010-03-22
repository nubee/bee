<?php

class EmptyCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName(self::Name());
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    return true;
  }

  static function Name() { return 'empty:command'; }
}
