<?php

class nbMysqlRestoreCommand extends nbCommand
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
        new nbArgument('password', nbArgument::OPTIONAL, 'Database password',null)
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $dbName   = $arguments['db-name'];
    $filename = $arguments['dump-filename'];
    $username = $arguments['username'];
    $password = isset($arguments['password']) ? $arguments['password'] : null;
    
    if(!file_exists($filename))
      throw new InvalidArgumentException('Database dump file: ' . $filename . ' does not exist');
    
    $this->logLine('Restoring ' . $filename . ' to database ' . $dbName);
    
    $cmd = sprintf('mysql -u%s %s %s < %s', $username, nbMysqlUtils::formatPasswordOption($password), $dbName, $filename);
    $this->logLine($cmd);
    $this->executeShellCommand($cmd);
    
    $this->logLine('MySql database restored!');
    
    return true;
  }

}