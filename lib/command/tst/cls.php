<?php

class cls
{
  protected function configure()
  {
    $this->setName('tst:cmd')
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