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
                new nbArgument('db-user', nbArgument::REQUIRED, 'Database user'),
                new nbArgument('db-user-pwd', nbArgument::OPTIONAL, 'Database user password')
            )));

    $this->setOptions(new nbOptionSet(array(
                new nbOption('config-file', 'f', nbOption::PARAMETER_OPTIONAL, 'Mysql plugin configuration file', './.bee/nbMysqlPlugin.yml')
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $timestamp = date('YmdHi', time());
    $dump_file = $arguments['db-name'] . '-' . $timestamp . '.sql';
    $this->logLine('mysqldump ' . $arguments['db-name'] . ' in ' . $arguments['dump-path'] . '/' . $dump_file);
    $shell = new nbShell();
    $cmd = 'mysqldump -u ' . $arguments['db-user'] . ' --password=' . $arguments['db-user-pwd'] . ' ' . $arguments['db-name'] . ' > ' . nbFileSystemUtils::sanitize_dir($arguments['dump-path']) . '/' . $dump_file;
    //$this->logLine('Mysql command: ' . $cmd);
    $shell->execute($cmd);
    $this->logLine('Done - mysqldump');
    return true;
  }

}