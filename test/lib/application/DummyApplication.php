<?php

class DummyApplication extends nbApplication
{
  public function __construct(array $arguments = array(), array $options = array())
  {
    parent::__construct();

    if(null === $arguments)
      $arguments = new nbArgumentSet();

    if(null === $options)
      $options = new nbOptionSet();

    $this->addArguments($arguments);
    $this->addOptions($options);
  }

  protected function configure()
  {
    
  }
  protected function handleOptions(array $options)
  {

  }
  protected function formatSynopsys($synopsys)
  {

  }
  protected function formatArguments(nbArgumentSet $argumentSet, $max)
  {

  }
  protected function formatOptions(nbOptionSet $optionSet, $max)
  {

  }
  protected function formatDescription($description)
  {

  }
}