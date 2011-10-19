<?php

class nbSymfonyGoOnlineCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('symfony:go-online')
      ->setBriefDescription('Puts a symfony application online in a specified enviroment')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
        );

    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('symfony_path', nbArgument::REQUIRED, 'Symfony executable path'),
      new nbArgument('application', nbArgument::REQUIRED, 'Symfony application'),
      new nbArgument('enviroment', nbArgument::REQUIRED, 'Symfony enviroment')
    )));

    $this->setOptions(new nbOptionSet(array(
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
      $this->logLine('Site is going online');
      $shell = new nbShell();
      $cmd = 'php '.$arguments['symfony_path'].'/symfony project:enable '.$arguments['application'].' '.$arguments['enviroment'];
      $this->logLine($cmd);
      $shell->execute($cmd);
      $this->logLine('Done - SymfonyGoOnlineCommand');
      return true;
  }

}