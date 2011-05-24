<?php

class nbSymfonySyncProjectCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('symfony:sync-project')
      ->setBriefDescription('Syncronize a symfony project with a source project')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
        );

    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('source_project_path', nbArgument::REQUIRED, 'Symfony site to sync path'),
      new nbArgument('target_project_path', nbArgument::REQUIRED, 'Symfony target site path')

    )));

    $this->setOptions(new nbOptionSet(array(
      new nbOption('doit', 'd', nbOption::PARAMETER_NONE, 'This option execute syncronization'),
      new nbOption('exclude-from', 'e', nbOption::PARAMETER_REQUIRED, 'This option set the exclude file')
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
      $this->logLine('Start site syncronization');
      $shell = new nbShell();
      $exclude_option = '';
      $doit_option = 'n';
      //'rsync -azvCh  --exclude-from './exclude.txt' --delete '.$arguments['source_project_path'].' '.$arguments['target_project_path']
      if(isset ($options['exclude-from']) && file_exists($options['exclude-from']))
          $exclude_option = ' --exclude-from \''.$options['exclude-from'].'\' ';
      if(isset ($options['doit']))
          $doit_option = '';
      $shell->execute('rsync -azvCh'.$doit_option.' '.$exclude_option.' --delete  --include=core '.$arguments['source_project_path'].' '.$arguments['target_project_path']);
      $this->logLine('Done site syncronization');
      return true;
  }

}