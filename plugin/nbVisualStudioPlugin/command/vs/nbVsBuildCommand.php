<?php

class nbVsBuildCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('vs:build')
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
    return true;
  }

}