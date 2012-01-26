<?php

class nbSymfonyDoctrineBuildCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('symfony:doctrine-build')
      ->setBriefDescription('Executes symfony doctrine:build command')
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
        new nbOption('no-confirmation', 'f', nbOption::PARAMETER_NONE, 'no confirmation'),
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $this->logLine('Starting doctrine build task...');
    $symfony_path = $arguments['symfony-root-dir'];
    $env = $arguments['environment'];
    $no_confirmation = (isset($options['no-confirmation']))?'--no-confirmation':'';
    
    $cmd = sprintf('php %s/symfony doctrine:build --all --and-load --env=%s %s', $symfony_path, $env, $no_confirmation);
    $this->executeShellCommand($cmd);
    //$this->logLine($cmd);

    $this->logLine('Doctrine build executed!');
    return true;
  }

}