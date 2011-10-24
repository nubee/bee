<?php

class nbBeeUpdateCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('bee:update')
      ->setBriefDescription('Updates bee')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('source_dir', nbArgument::OPTIONAL, 'Sources directory', nbConfig::get('nb_bee_dir'))
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $sourceDir = nbFileSystemUtils::sanitize_dir($arguments['source_dir']);

    $this->logLine('Updating bee from ' . $sourceDir, nbLogger::COMMENT);

    $this->executeShellCommand('cd ' . $sourceDir . ' && git pull');
    $this->logLine('Bee successfully updated', nbLogger::COMMENT);
  }

}