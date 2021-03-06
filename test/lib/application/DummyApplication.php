<?php

class DummyApplication extends nbApplication
{
  public $executedFormatHelpString = false;

  public function __construct(sfServiceContainerBuilder $container, array $arguments = array(), array $options = array())
  {
    parent::__construct($container);

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

  public function getOption($optionName)
  {
    return $this->options->getOption($optionName);
  }
}