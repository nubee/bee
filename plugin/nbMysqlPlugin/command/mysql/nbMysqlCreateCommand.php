<?php

class nbMysqlCreateCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('mysql:create')
      ->setBriefDescription('')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
        );

    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('db-name', nbArgument::REQUIRED, 'Database name'),
      new nbArgument('mysql-user', nbArgument::REQUIRED, 'Mysql user'),
      new nbArgument('mysql-user-pwd', nbArgument::REQUIRED, 'Mysql user password')
    )));
    

    $this->setOptions(new nbOptionSet(array(
      new nbOption('db-user', '', nbOption::PARAMETER_REQUIRED, 'Database user'),
      new nbOption('db-user-pwd', '', nbOption::PARAMETER_REQUIRED, 'Database user password')
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $shell = new nbShell();
    $cmd = 'mysqladmin -u '.$arguments['mysql-user'].' --password='.$arguments['mysql-user-pwd'].' create '. $arguments['db-name'];
    $shell->execute($cmd);
    $this->logLine('Done - mysql create database');

    if(isset($options['db-user'])) {
      $cmd = 'mysql -u '.$arguments['mysql-user'].' --password='.$arguments['mysql-user-pwd'].
        ' -e "grant all privileges on '.$arguments['db-name'].'.* to \''.$options['db-user'].'\'@\'localhost\'';
      
      if(isset($options['db-user-pwd'])) {
        $cmd = $cmd.' identified by \''.$options['db-user-pwd'].'\'';   
      }
      
      $cmd = $cmd.'"';
      
      $shell->execute($cmd);
      $this->logLine('Done - create database user');
    }

    return true;
  }

}