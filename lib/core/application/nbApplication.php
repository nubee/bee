<?php

/**
 * Represents an application that executes commands.
 *
 * @package    bee
 * @subpackage application
 */
class nbApplication
{
  private
    $arguments = null,
    $options = null,
    $commands = null;

  public function __construct(nbArgumentSet $arguments = null, nbOptionSet $options = null)
  {
    if(null === $arguments)
      $arguments = new nbArgumentSet();

    if(null === $options)
      $options = new nbOptionSet();

    $this->setArguments($arguments);
    $this->setOptions($options);
  }

  public function setArguments(nbArgumentSet $arguments)
  {
    $this->arguments = $arguments;
  }

  public function hasArguments()
  {
    return $this->arguments->count() > 0;
  }

  public function getArguments()
  {
    return $this->arguments;
  }

  public function setOptions(nbOptionSet $options)
  {
    $this->options = $options;
  }

  public function hasOptions()
  {
    return $this->options->count() > 0;
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function setCommands(nbCommandSet $commands)
  {
    $this->commands = $commands;
  }

  public function hasCommands()
  {
    return $this->commands->count() > 0;
  }

  public function getCommands()
  {
    return $this->commands;
  }
}
