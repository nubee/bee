<?php

class nbSymfonyDiemSetupCommand extends nbCommand {

  protected function configure() {
    $this->setName('symfony:diem-setup')
            ->setBriefDescription('Invoke diem setup')
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
    $shell->execute('php ' . $arguments['symfony_path'] . '/symfony dm:setup');
    $this->logLine('Done - Diem setup');
    return true;
  }

}