<?php

class nbSymfonyCheckPermissionsCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('symfony:check-permission')
      ->setBriefDescription('Check permission in a symfony project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
        );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('symfony_path', nbArgument::REQUIRED, 'Symfony executable path')
    )));

    $this->setOptions(new nbOptionSet(array(
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
      $this->logLine('Checking file permissions');
      $shell = new nbShell();
      $cmd = 'php '.$arguments['symfony_path'].'/symfony project:permissions';
      $this->logLine($cmd);
      $shell->execute($cmd);
      $this->logLine('Done checking permissions');

      return true;
  }

}