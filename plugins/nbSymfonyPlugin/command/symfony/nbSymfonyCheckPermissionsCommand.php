<?php

class nbSymfonyCheckPermissionsCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('symfony:check-permission')
      ->setBriefDescription('Checks permission in a symfony project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('symfony-path', nbArgument::REQUIRED, 'Symfony executable path')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $path = $arguments['symfony-path'];
    $this->logLine('Checking symfony file permissions');

    $cmd = 'php ' . $path . '/symfony project:permissions';
    $this->executeShellCommand($cmd);

    $this->logLine('Symfony permissions checked!');
    
    return true;
  }

}