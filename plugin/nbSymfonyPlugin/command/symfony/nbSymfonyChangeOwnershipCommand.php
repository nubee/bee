<?php

class nbSymfonyChangeOwnershipCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('symfony:project-chown')
      ->setBriefDescription('Change file ownership for symfony project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
        );

    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('target_project_path', nbArgument::REQUIRED, 'Symfony target site path'),
      new nbArgument('user', nbArgument::REQUIRED, 'Owner userid'),
      new nbArgument('group', nbArgument::REQUIRED, 'Owner group')
    )));

    $this->setOptions(new nbOptionSet(array(
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
      $this->logLine('Changing ownership for project: '.$arguments['target_project_path']);
      $shell = new nbShell();
      $shell->execute('chown -R '.$arguments['user'].':'.$arguments['group'].' '.$arguments['target_project_path'].'*');
      $this->logLine('Done - Changing ownership');
      return true;
  }

}