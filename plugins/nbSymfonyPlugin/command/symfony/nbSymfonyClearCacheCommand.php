<?php

class nbSymfonyClearCacheCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('symfony:project-clear-cache')
      ->setBriefDescription('Clears cache for a symfony project')
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

    $this->logLine('Clearing symfony cache');
    
    $cmd = 'php ' . $path . '/symfony cc';
    
    $this->executeShellCommand($cmd);
    
    $this->logLine('Symfony cache cleared!');
    
    return true;
  }

}