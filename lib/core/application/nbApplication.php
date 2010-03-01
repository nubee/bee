<?php

/**
 * Represents an application that executes commands.
 *
 * @package    bee
 * @subpackage application
 */
abstract class nbApplication
{
  protected
    $name = 'UNDEFINED',
    $version = 'UNDEFINED',
    $arguments = null,
    $options = null,
    $commands = null;

  public function __construct()
  {
    $this->arguments = new nbArgumentSet();
    $this->options = new nbOptionSet();
    $this->commands = new nbCommandSet();
    
    $this->arguments->addArgument(
      new nbArgument('command', nbArgument::REQUIRED, 'The command to execute')
    );
    
    $this->configure();
  }

  public function run($commandLine = null)
  {
    $this->parser = new nbCommandLineParser($this->arguments, $this->options);
    $this->parser->parse($commandLine);

    if($this->handleOptions($this->parser->getOptionValues()))
      return;
    
    $command = $this->commands->getCommand($this->parser->getArgumentValue('command'));
    $command->run($this->parser, $commandLine);
  }

  public function getName()
  {
    return $this->name;
  }
  
  public function getVersion()
  {
    return $this->version;
  }
  
  protected abstract function configure();
  protected abstract function handleOptions(array $options);

  public function addArguments(array $arguments)
  {
    $this->arguments->addArguments($arguments);
  }

  public function hasArguments()
  {
    return $this->arguments->count() > 0;
  }

  public function getArguments()
  {
    return $this->arguments;
  }

  public function addOptions(array $options)
  {
    $this->options->addOptions($options);
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
