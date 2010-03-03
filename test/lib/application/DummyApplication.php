<?php

class DummyApplication extends nbApplication
{
  public $executedFormatHelpString = false;

  public function __construct(array $arguments = array(), array $options = array())
  {
    parent::__construct(array());

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
  protected function handleOptions(array $options)
  {

  }
  protected function formatSynopsys($synopsys)
  {
    return $synopsys;
  }
  protected function formatArguments(nbArgumentSet $argumentSet, $max)
  {
    return (string)$argumentSet;
  }
  protected function formatOptions(nbOptionSet $optionSet, $max)
  {
    return (string)$argumentSet;
  }
  protected function formatDescription($description)
  {
    return $description;
  }
}