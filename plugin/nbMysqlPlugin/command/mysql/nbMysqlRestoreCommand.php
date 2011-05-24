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
                new nbArgument('db_name', nbArgument::REQUIRED, 'Database name'),
                new nbArgument('dump_file', nbArgument::REQUIRED, 'Database dumpfile'),
                new nbArgument('db_user', nbArgument::REQUIRED, 'Database user'),
                new nbArgument('db_user_pwd', nbArgument::REQUIRED, 'Database user password')
            )));

    $this->setOptions(new nbOptionSet(array(
            )));
  }

  protected function execute(array $arguments = array(), array $options = array()) {
    $this->logLine('mysql restore '.$arguments['dump_file'].' in '.$arguments['db_name']);
    $shell = new nbShell();
    $cmd = 'mysql -u '.$arguments['db_user'].' --password='.$arguments['db_user_pwd'].' '.$arguments['db_name'].' < '.$arguments['dump_file'];
    $this->logLine('Mysql command: '.$cmd);
    $shell->execute($cmd);
    $this->logLine('Done - mysql restore');
    return true;

  }

}