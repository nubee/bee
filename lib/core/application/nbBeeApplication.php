<?php

/**
 * Represents an application that executes commands.
 *
 * @package    bee
 * @subpackage application
 */
class nbBeeApplication extends nbApplication
{
  private $verbose = false;
  
  protected function configure()
  {
    $this->name = 'bee';
    $this->version = '0.1.0';
    $this->options->addOption(new nbOption('version', 'V', nbOption::PARAMETER_NONE, 'Shows the version'));
    $this->options->addOption(new nbOption('verbose', 'v', nbOption::PARAMETER_NONE, 'Set verbosity'));
  }

  protected function handleOptions(array $options)
  {
    $logger = nbLogger::getInstance();
    if($options['verbose'])
      $this->verbose = true;

    if($options['version']) {
      $logger->log(($this->verbose ? $this->getName() . ' version ' : '') . $this->getVersion());
      return true;
    }

    return false;
  }
}
