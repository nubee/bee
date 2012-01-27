<?php

class nbSymfonyDoctrineMigrateAllCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('symfony:doctrine-migration')
      ->setBriefDescription('Executes symfony doctrine:migrate command')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
        );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('symfony-root-dir', nbArgument::REQUIRED, 'Symfony executable path'),
        new nbArgument('environment', nbArgument::REQUIRED, 'Symfony environment')
    )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('dry-run', 'd', nbOption::PARAMETER_NONE, 'dry-run'),
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->logLine('Starting doctrine migrate task...');
    $symfony_path = $arguments['symfony-root-dir'];
    $env = $arguments['environment'];
    $dry_run = (isset($options['dry-run']))?'--dry-run':'';
    
    $cmd = sprintf('php %s/symfony doctrine:migrate --env=%s %s', $symfony_path, $env, $dry_run);
    $this->executeShellCommand($cmd);
    //$this->logLine($cmd);

    $this->logLine('Doctrine migrate executed!');
    return true;
  }

}