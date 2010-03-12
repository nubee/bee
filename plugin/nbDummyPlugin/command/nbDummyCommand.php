<?php

class nbDummyCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('nbDummy:command')
      ->setBriefDescription('Example Plugin Command ')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command is only an example;

   <info>./bee {$this->getFullName()}</info>
TXT
        );

  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log('Hello '.nbConfig::get('nbDummy_hello').' World!!!!',nbLogger::COMMENT);
  }
}