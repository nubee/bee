<?php

class %%CLASSNAME%%
{
  protected function configure()
  {
    $this->setName('%%NAMESPACE%%:%%NAME%%')
      ->setBriefDescription('')
      ->setDescription(<<<TXT
TXT
        );

    $this->setArguments(new nbArgumentSet(array(
    )));

    $this->setOptions(new nbOptionSet(array(
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
  }

}