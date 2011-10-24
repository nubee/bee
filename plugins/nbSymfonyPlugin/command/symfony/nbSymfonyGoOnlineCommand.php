<?php

class nbSymfonyGoOnlineCommand extends nbCommand {

  protected function configure() {
    $this->setName('symfony:go-online')
      ->setBriefDescription('Puts a symfony application online in a specified enviroment')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('symfony-path', nbArgument::REQUIRED, 'Symfony executable path'),
        new nbArgument('application', nbArgument::REQUIRED, 'Symfony application'),
        new nbArgument('enviroment', nbArgument::REQUIRED, 'Symfony enviroment')
      )));

    $this->setOptions(new nbOptionSet(array(
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $path = $arguments['symfony-path'];
    $application = $arguments['application'];
    $environment = $arguments['enviroment'];
    
    $website = sprintf('%s/%s (%s)', $path, $application, $environment);
    
    $this->logLine(sprintf('Putting site "%s" online', $website));
    
    $cmd = sprintf('php %s/symfony project:enable %s %s', $path, $application, $environment);

    $this->executeShellCommand($cmd);
    
    $this->logLine(sprintf('Site "%s" is online', $website));

    return true;
  }

}