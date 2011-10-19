<?php

class nbDummyCommand extends nbCommand
{
  protected function configure()
  {
    $this->setName('dummy:hello-world')
      ->setBriefDescription('Shows an example command')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command is only an example;

   <info>./bee {$this->getFullName()}</info>
TXT
        );

  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->log(nbDummyLib::toUpper('Hello '.nbConfig::get('nbDummy_happy')." World!!!!\n"),nbLogger::COMMENT);
    $this->log(nbDummyLib::toLower('Hello '.nbConfig::get('nbDummy_angry')." World!!!!\n"),nbLogger::COMMENT);
    $this->log('Hello '.nbConfig::get('nbDummy_fool')." World!!!!\n",nbLogger::COMMENT);
  }
}