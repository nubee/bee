<?php

class nbMysqlDumpCommand extends nbCommand {

  protected function configure() {
    $this->setName('mysql:dump')
      ->setBriefDescription('Dumps a mysql database')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
        new nbArgument('db-name', nbArgument::REQUIRED, 'Database name'),
        new nbArgument('dump-path', nbArgument::REQUIRED, 'Database dump path'),
        new nbArgument('username', nbArgument::REQUIRED, 'Database username'),
        new nbArgument('password', nbArgument::OPTIONAL, 'Database password', null),
        new nbArgument('host', nbArgument::OPTIONAL, 'Host', '127.0.0.1'),
      )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $dbName = $arguments['db-name'];
    $path = $arguments['dump-path'];
    $username = $arguments['username'];
    $password = isset($arguments['password']) ? $arguments['password'] : null;
    $host = $arguments['host'];

    $timestamp = date('YmdHi', time());
    $dump = sprintf('%s/%s-%s.sql', $path, $timestamp, $dbName);

    $this->logLine(sprintf('Dumping database "%s" to "%s"', $dbName, $dump));

        $cmd = sprintf('mysqldump -u%s %s -h%s %s > %s', $username, nbMysqlUtils::formatPasswordOption($password), $host, $dbName, $dump);

    $this->executeShellCommand($cmd);
    $this->logLine('MySql database dumped!');

    return true;
  }

}