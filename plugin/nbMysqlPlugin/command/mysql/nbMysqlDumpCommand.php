<?php

class nbMysqlDumpCommand  extends nbCommand
{
  protected function configure()
  {
    $this->setName('mysql:dump')
      ->setBriefDescription('dump a mysql database')
      ->setDescription(<<<TXT
The <info>{$this->getFullName()}</info> command:

  <info>./bee {$this->getFullName()}</info>
TXT
        );

    $this->setArguments(new nbArgumentSet(array(
      new nbArgument('db_name', nbArgument::REQUIRED, 'Database name'),
      new nbArgument('dump_path', nbArgument::REQUIRED, 'Database dump path'),
      new nbArgument('db_user', nbArgument::REQUIRED, 'Database user'),
      new nbArgument('db_user_pwd', nbArgument::REQUIRED, 'Database user password')
    )));

    $this->setOptions(new nbOptionSet(array(
    )));
  }

  protected function execute(array $arguments = array(), array $options = array())
  {
    $timestamp = date('YmdHi',  time());
    $dump_file = $arguments['db_name'].'-'.$timestamp.'.sql';
    $this->logLine('mysqldump '.$arguments['db_name'].' in '.$arguments['dump_path'].'/'.$dump_file);
    $shell = new nbShell();
    $cmd = 'mysqldump -u '.$arguments['db_user'].' --password='.$arguments['db_user_pwd'].' '.$arguments['db_name'].' > '.$arguments['dump_path'].'/'.$dump_file;
    $this->logLine('Mysql command: '.$cmd);
    $shell->execute($cmd);
    $this->logLine('Done - mysqldump');
    return true;
  }

}