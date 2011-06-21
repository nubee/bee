<?php

class nbSymfonyCheckDirsCommand  extends nbCommand
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
      new nbArgument('target_project_path', nbArgument::REQUIRED, 'Symfony target site path'),
    )));

    $this->setOptions(new nbOptionSet(array(
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
      $this->logLine('Checking symfony dirs for '.$arguments['target_project_path']);
      if(!file_exists($arguments['target_project_path'].'/log'))
          nbFileSystem::mkdir ($arguments['target_project_path'].'/log');
      if(!file_exists($arguments['target_project_path'].'/cache'))
          nbFileSystem::mkdir ($arguments['target_project_path'].'/cache');
      $this->logLine('Done - Checking symfony dirs');
      return true;
  }

}