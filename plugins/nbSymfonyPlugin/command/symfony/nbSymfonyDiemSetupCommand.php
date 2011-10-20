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
    $this->logLine('Diem setup');
    $shell = new nbShell();

    $cmd = 'php ' . $arguments['symfony-path'] . '/symfony dm:setup';

    $this->logLine($cmd);
    if (!$shell->execute($cmd)) {
      throw new LogicException(sprintf("
[nbSymfonyDiemSetupCommand::execute] Error executing command:
  %s
", $cmd
      ));
    }

    $this->logLine('Done - Diem setup');

    return true;
  }

}