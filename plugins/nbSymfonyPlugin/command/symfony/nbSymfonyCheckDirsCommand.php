<?php

class nbSymfonyCheckDirsCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('symfony:project-check-dirs')
      ->setBriefDescription('Checks dirs in a symfony project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('project-dir', nbArgument::REQUIRED, 'Symfony target site dir'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $projectDir = $arguments['project-dir'];
    $fs = $this->getFileSystem();
    $this->logLine('Checking symfony dirs for ' . $projectDir);

    if(!file_exists($projectDir . '/log'))
      $fs->mkdir($projectDir . '/log', true);
    
    if(!file_exists($projectDir . '/cache'))
      $fs->mkdir($projectDir . '/cache', true);
    
    $this->logLine('Done - Checking symfony dirs');
    return true;
  }

}