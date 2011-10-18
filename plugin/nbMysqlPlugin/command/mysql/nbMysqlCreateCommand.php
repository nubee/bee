<?php

class nbMysqlCreateCommand extends nbCommand
{

  protected function configure()
  {
    $this->setName('mysql:create')
      ->setBriefDescription('Creates a database, associates its user and grants privileges to it')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('db-name', nbArgument::REQUIRED, 'New database name'),
        new nbArgument('mysql-user', nbArgument::REQUIRED, 'Mysql user'),
        new nbArgument('mysql-password', nbArgument::OPTIONAL, 'Mysql user password')
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('user', 'u', nbOption::PARAMETER_REQUIRED, 'New database user'),
        new nbOption('password', 'p', nbOption::PARAMETER_REQUIRED, 'New database password')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $shell = new nbShell();
    $mysqlUser = $arguments['mysql-user'];
    $mysqlPassword = $arguments['mysql-password'];
    $dbName = $arguments['db-name'];

    $cmd = 'mysqladmin -u ' . $mysqlUser . ' -p ' . $mysqlPassword . ' create ' . $dbName;
    $shell->execute($cmd);
    $this->logLine(sprintf('Database %s created', $dbName));

    $username = isset($options['username']) ? $options['username'] : null;
    $password = isset($options['password']) ? $options['password'] : null;

    if($username) {
      $cmd = 'mysql -u ' . $mysqlUser . ' -p ' . $mysqlPassword .
        ' -e "grant all privileges on ' . $dbName . '.* to \'' . $username . '\'@\'localhost\'';

      if($password) {
        $cmd .= ' identified by \'' . $password . '\'';
      }

      $cmd .= '"';

      $shell->execute($cmd);
      $this->logLine(sprintf('Datebase user %s successfully created', $username));
    }
  }

}