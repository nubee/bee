<?php

class %%CLASSNAME%%  extends nbCommand
{
  protected function configure()
  {
    $this->setName('%%NAMESPACE%%:%%NAME%%')
      ->setBriefDescription('')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
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