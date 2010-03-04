<?php

class DummyCommand extends nbCommand
{
  private $executed = false;
  
  public function __construct($name = 'dummy', nbArgumentSet $arguments = null, nbOptionSet $options = null)
  {
    parent::__construct();
    if(!$arguments)
      $arguments = new nbArgumentSet();
    if(!$options)
      $options = new nbOptionSet();
    
    $this->setName($name)
      ->setArguments($arguments)
      ->setOptions($options);
  }

  protected function configure()
  {

  }
  
  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->arguments = $arguments;
    $this->options = $options;
    $this->executed = true;
  }

  public function hasExecuted()
  {
    return $this->executed;
  }

  public function getArgument($argumentName)
  {
    return $this->arguments[$argumentName];
  }

  public function getOption($optionName)
  {
    return $this->options[$optionName];
  }
}