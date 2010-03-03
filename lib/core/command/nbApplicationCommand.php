<?php

/**
 * Represents an application command to execute.
 *
 * @package    bee
 * @subpackage argument
 */
abstract class nbApplicationCommand extends nbCommand
{
  private $application;

  public function setApplication($application)
  {
    $this->application = $application;
  }

  public function getApplication()
  {
    return $this->application;
  }
}
