<?php

class nbSymfonyGoOfflineCommand extends nbCommand {

  protected function configure() {
    $this->setName('symfony:go-offline')
      ->setBriefDescription('Puts a symfony application offline in a specified environment')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('symfony-path', nbArgument::REQUIRED, 'Symfony executable path'),
        new nbArgument('application', nbArgument::REQUIRED, 'Symfony application'),
        new nbArgument('environment', nbArgument::REQUIRED, 'Symfony environment')
      )));

    $this->setOptions(new nbOptionSet(array(
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $path = $arguments['symfony-path'];
    $application = $arguments['application'];
    $environment = $arguments['environment'];
    
    $website = sprintf('%s/%s (%s)', $path, $application, $environment);
    
    $this->logLine(sprintf('Putting site "%s" offline', $website));
    
    $cmd = sprintf('php %s/symfony project:disable %s %s', $path, $application, $environment);

    $this->executeShellCommand($cmd);
    
    $this->logLine(sprintf('Site "%s" is offline', $website));
    
    return true;
  }

}