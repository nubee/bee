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
                new nbArgument('symfony_path', nbArgument::REQUIRED, 'Symfony executable path'),
            )));

    $this->setOptions(new nbOptionSet(array(
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('Diem setup');
    $shell = new nbShell();
    $cmd = 'php ' . $arguments['symfony_path'] . '/symfony dm:setup';
    $this->logLine($cmd);
    $shell->execute($cmd);
    $this->logLine('Done - Diem setup');
    return true;
  }

}