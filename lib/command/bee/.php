<?php

class ./  extends nbCommand
{
  protected function configure()
  {
    $this->setName('bee:generate-plugin')
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