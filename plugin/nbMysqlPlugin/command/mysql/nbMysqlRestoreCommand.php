<?php

class nbMysqlRestoreCommand extends nbCommand {

  protected function configure() {
    $this->setName('mysql:restore')
            ->setBriefDescription('restore a mysql database')
            ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
    );

    $this->setArguments(new nbArgumentSet(array(
                new nbArgument('db-name', nbArgument::REQUIRED, 'Database name'),
                new nbArgument('dump-file', nbArgument::REQUIRED, 'Database dumpfile'),
                new nbArgument('db-user', nbArgument::REQUIRED, 'Database user'),
                new nbArgument('db-user-pwd', nbArgument::REQUIRED, 'Database user password')
            )));

    $this->setOptions(new nbOptionSet(array(
                new nbOption('config-file', 'f', nbOption::PARAMETER_OPTIONAL, 'Mysql plugin configuration file', './.bee/nbMysqlPlugin.yml')
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('mysql restore '.$arguments['dump-file'].' in '.$arguments['db-name']);
    $shell = new nbShell();
    $cmd = 'mysql -u '.$arguments['db-user'].' --password='.$arguments['db-user-pwd'].' '.$arguments['db-name'].' < '.$arguments['dump-file'];
    $this->logLine('Mysql command: '.$cmd);
    $shell->execute($cmd);
    $this->logLine('Done - mysql restore');
    return true;

  }

}