<?php

class nbMysqlDropCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('mysql:drop')
      ->setBriefDescription('Drops a database')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('db-name', nbArgument::REQUIRED, 'New database name'),
        new nbArgument('mysql-username', nbArgument::REQUIRED, 'Mysql username'),
        new nbArgument('mysql-password', nbArgument::OPTIONAL, 'Mysql password')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $mysqlUsername = $arguments['mysql-username'];
    $mysqlPassword = $arguments['mysql-password'];
    $dbName = $arguments['db-name'];
    $this->logLine(sprintf('Dropping database %s', $dbName));

    $cmd = sprintf('mysql -u%s %s -e "drop database %s"', $mysqlUsername, nbMysqlUtils::formatPasswordOption($mysqlPassword), $dbName);

    $this->executeShellCommand($cmd);
    $this->logLine(sprintf('Database %s dropped', $dbName));
    
    return true;
  }

}