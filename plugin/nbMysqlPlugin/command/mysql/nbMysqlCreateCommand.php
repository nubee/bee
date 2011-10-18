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
        new nbArgument('mysql-username', nbArgument::REQUIRED, 'Mysql username'),
        new nbArgument('mysql-password', nbArgument::OPTIONAL, 'Mysql password', null)
      )));

    $this->setOptions(new nbOptionSet(array(
        new nbOption('username', 'u', nbOption::PARAMETER_REQUIRED, 'New database username'),
        new nbOption('password', 'p', nbOption::PARAMETER_REQUIRED, 'New database password')
      )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $shell = new nbShell();
    $mysqlUsername = $arguments['mysql-username'];
    $mysqlPassword = $arguments['mysql-password'];
    $dbName = $arguments['db-name'];

    $cmd = 'mysqladmin -u ' . $mysqlUsername . ' create ' . $dbName;
    if($mysqlPassword)
      $cmd -= ' -p ' . $mysqlPassword;
    $shell->execute($cmd);
    $this->logLine(sprintf('Database %s created', $dbName));

    $username = isset($options['username']) ? $options['username'] : null;
    $password = isset($options['password']) ? $options['password'] : null;

    if($username) {
      $cmd = sprintf('mysql -u %s %s -e "grant all privileges on %s.* to \'%s\'@\'localhost\' %s"', 
        $mysqlUsername, 
        $this->formatPasswordOption($mysqlPassword), 
        $dbName,
        $username, 
        ($password ? sprintf(' identified by \'%s\'', $password) : ''));

      $shell->execute($cmd);
      $this->logLine(sprintf('Datebase user %s successfully created', $username));
    }
    
    return true;
  }
  
  protected function formatPasswordOption($password) {
    if($password)
      return ' -p' . $password;
    
    return '';
  }

}