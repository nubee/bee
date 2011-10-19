<?php

class nbSymfonyDoctrineMigrateCommand  extends nbCommand
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
      new nbArgument('symfony_path', nbArgument::REQUIRED, 'Symfony executable path'),
      new nbArgument('version', nbArgument::REQUIRED, 'Migration version'),
    )));

    $this->setOptions(new nbOptionSet(array(
      new nbOption('env', 'e', nbOption::PARAMETER_REQUIRED, 'Enviroment'),
      new nbOption('app', 'a', nbOption::PARAMETER_REQUIRED, 'Application'),
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
      $this->logLine('Migration to version '.$arguments['version']);
      $cmd_options = '';
      if(isset($options['env']))
        $cmd_options = ' --env='.$options['env'];
      if(isset($options['app']))
        $cmd_options .= ' --app='.$options['app'];
      $shell = new nbShell();
      $shell->execute('php '.$arguments['symfony_path'].'/symfony doctrine:get-latest-migration-version' );
      $latest_version = $shell->getOutput();
      if(preg_match('/^\d+$/',$latest_version) == 0)
          throw new Exception ('Error retrieving latest migration version');
      if($latest_version < $arguments['version'])
          throw new Exception ('Migration version to high');

      $shell->execute('php '.$arguments['symfony_path'].'/symfony doctrine:get-migration-version' );
      $current_version = $shell->getOutput();
      if(preg_match('/^\d+$/',$current_version) == 0)
          throw new Exception ('Error retrieving migration version');
      if($current_version > $arguments['version']){
        for($i = $current_version-1; $i >= $arguments['version']; $i--){
          $shell->execute('php '.$arguments['symfony_path'].'/symfony doctrine:migrate '.$i.$cmd_options);      
        }
      }
      if($current_version < $arguments['version']){
        for($i = $current_version+1; $i <= $arguments['version']; $i++){
          $shell->execute('php '.$arguments['symfony_path'].'/symfony doctrine:migrate '.$i.$cmd_options);
        }
      }
      $shell->execute('php '.$arguments['symfony_path'].'/symfony doctrine:get-migration-version' );
      $current_version = $shell->getOutput();
      if(preg_match('/^\d+$/',$current_version) == 0)
          throw new Exception ('Error retrieving migration version');
      if($current_version != $arguments['version'])
          throw new Exception ('Migration Error: migration version equal to '.$current_version);
      $this->logLine('Done - Symfony Migration');
      return true;

  }

}