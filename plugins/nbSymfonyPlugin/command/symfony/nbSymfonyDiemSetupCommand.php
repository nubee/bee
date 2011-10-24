<?php

class nbSymfonyDiemSetupCommand extends nbCommand {

  protected function configure() {
    $this->setName('symfony:diem-setup')
      ->setBriefDescription('Invokes diem setup')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('symfony-path', nbArgument::REQUIRED, 'Symfony executable path'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $path = $arguments['symfony-path'];

    $this->logLine('Setting Diem project up');

    $cmd = 'php ' . $path . '/symfony dm:setup';

    $this->executeShellCommand($cmd);

    $this->logLine('Diem project set up!');
  }

}