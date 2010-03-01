<?php

/**
 * Represents an application that executes commands.
 *
 * @package    bee
 * @subpackage application
 */
class nbBeeApplication extends nbApplication
{
  private $parser = null;
  private $verbose = false;
  
  protected function configure()
  {
    $this->name = 'bee';
    $this->version = '0.1.0';
    $this->options->addOption(new nbOption('version', 'V', nbOption::PARAMETER_NONE, 'Shows the version'));
    $this->options->addOption(new nbOption('verbose', 'v', nbOption::PARAMETER_NONE, 'Set verbosity'));
  }

  public function run($commandLine = null)
  {
    $this->parser = new nbCommandLineParser($this->arguments, $this->options);
    $this->parser->parse($commandLine);
    $this->handleOptions();
  }

  protected function handleOptions()
  {
    $logger = nbLogger::getInstance();
    if($this->parser->getOptionValue('verbose'))
      $this->verbose = true;

    if($this->parser->getOptionValue('version')) {
      $logger->log(($this->verbose ? $this->getName() . ' version ' : '') . $this->getVersion());
    }
  }
}
