<?php

class DummyApplication extends nbApplication
{
  public $executedFormatHelpString = false;

  public function __construct(array $arguments = array(), array $options = array())
  {
    parent::__construct(new nbCommandSet());

    if(null === $arguments)
      $arguments = new nbArgumentSet();

    if(null === $options)
      $options = new nbOptionSet();

    $this->addArguments($arguments);
    $this->addOptions($options);
  }

  public function formatHelpString()
  {
    $this->executedFormatHelpString = true;
    return 'formatHelpString';
  }

  protected function configure()
  {
  }

//  protected function handleOptions(array $options)
//  {
//  }
}