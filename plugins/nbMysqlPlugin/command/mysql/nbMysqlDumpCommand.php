<?php

class nbMysqlDumpCommand extends nbMysqlAbstractCommand
{

  protected function configure()
  {
    $this->setName('mysql:dump')
      ->setBriefDescription('Dumps a mysql database')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('db-name', nbArgument::REQUIRED, 'Database name'),
        new nbArgument('path', nbArgument::REQUIRED, 'Database dump path'),
        new nbArgument('username', nbArgument::REQUIRED, 'Database username'),
        new nbArgument('password', nbArgument::REQUIRED, 'Database password')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $dbName   = $arguments['db-name'];
    $path     = $arguments['path'];
    $username = $arguments['username'];
    $password = $arguments['password'];

    $timestamp = date('YmdHi', time());
    $dump = sprintf('%s/%s-%s.sql', $path, $dbName, $timestamp);
    
    $this->logLine('Dumping ' . $dbName . ' to ' . $dump);
    
    $shell = new nbShell();
    $cmd = sprintf('mysqldump -u%s -p%s %s > %s', $username, $password, $dbName, $dump);

    if($shell->execute($cmd)) {
      $this->logLine('Mysql database dumped!');
      return true;
    }
    
    return false;
  }

}