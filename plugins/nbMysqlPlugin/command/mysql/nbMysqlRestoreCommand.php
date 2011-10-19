<?php

class nbMysqlRestoreCommand extends nbMysqlAbstractCommand
{

  protected function configure()
  {
    $this->setName('mysql:restore')
      ->setBriefDescription('Restores a mysql database')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('db-name', nbArgument::REQUIRED, 'Database name'),
        new nbArgument('dump-filename', nbArgument::REQUIRED, 'Database dump filename'),
        new nbArgument('username', nbArgument::REQUIRED, 'Database username'),
        new nbArgument('password', nbArgument::REQUIRED, 'Database password')
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('config-file', 'f', nbOption::PARAMETER_OPTIONAL, 'Mysql plugin configuration file', './.bee/nbMysqlPlugin.yml')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $dbName = $arguments['db-name'];
    $filename = $arguments['dump-filename'];
    $username = $arguments['username'];
    $password = $arguments['password'];
    
    $this->logLine('Restoring ' . $filename . ' to ' . $dbName);
    
    $shell = new nbShell();
    $cmd = sprintf('mysql -u%s -p%s %s < %s', $username, $password, $dbName, $filename);
    $this->logLine($cmd);
    if($shell->execute($cmd)) {
      $this->logLine('MySql database restored!');
      return true;
    }
    
    return false;
  }

}