<?php

/**
 * Represents an application that executes commands.
 *
 * @package    bee
 * @subpackage application
 */
class nbBeeApplication extends nbApplication
{
  protected function configure()
  {
    $this->name = 'bee';
    $this->version = '0.1.0';

    $this->loadCommands();
  }
}
