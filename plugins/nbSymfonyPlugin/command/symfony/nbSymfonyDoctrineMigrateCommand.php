<?php

class nbSymfonyDoctrineMigrateCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('symfony:doctrine-migrate')
      ->setBriefDescription('Migrates a symfony database to a given version')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('symfony-path', nbArgument::REQUIRED, 'Symfony executable path'),
        new nbArgument('version', nbArgument::REQUIRED, 'Migration version'),
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('env', 'e', nbOption::PARAMETER_REQUIRED, 'Enviroment'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $path = $arguments['symfony-path'];
    $version = $arguments['version'];
    $environment = isset($options['env']) ? '--env=' . $options['env'] : '';

    $this->logLine(sprintf('Migrating database to version %s (%s)', $version, isset($options['env']) ? '(' . $environment . ')' : ''));

    
    $latestVersion = $this->executeShellCommand('php ' . $path . '/symfony doctrine:get-latest-migration-version');;
    
    if(preg_match('/^\d+$/', $latestVersion) == 0)
      throw new Exception('Error retrieving latest migration version');
    
    if($latestVersion < $version)
      throw new Exception(sprintf('Cannot migrate from version %s to version %s', $latestVersion, $version));
    
    
    $currentVersion = $this->executeShellCommand('php ' . $path . '/symfony doctrine:get-migration-version');;
    if(preg_match('/^\d+$/', $currentVersion) == 0)
      throw new Exception('Error retrieving current migration version');
    
    if($currentVersion > $version) {
      for($i = $currentVersion - 1; $i >= $version; $i--) {
        $this->executeShellCommand('php ' . $path . '/symfony doctrine:migrate ' . $i . $environment);
      }
    }
    
    if($currentVersion < $version) {
      for($i = $currentVersion + 1; $i <= $version; $i++) {
        $this->executeShellCommand('php ' . $version . '/symfony doctrine:migrate ' . $i . $environment);
      }
    }
    
    $currentVersion = $this->executeShellCommand('php ' . $path . '/symfony doctrine:get-migration-version');
    
    if(preg_match('/^\d+$/', $currentVersion) == 0)
      throw new Exception('Error retrieving migration version');
    if($currentVersion != $version)
      throw new Exception('Migration Error: migration version equal to ' . $currentVersion);
    
    $this->logLine('Symfony project migrated successfully to version ' . $version);
  }

}