<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$t = new lime_test(2);
$t->comment('Mysql Restore Command');

// Setup
createDb($mysqlUsername, $mysqlPassword, $dbName, $username, $password);

$cmd = new nbMysqlRestoreCommand();
$commandLine = sprintf('%s %s %s %s', $dbName, $dumpFilename, $username, $password);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlRestore executed successfully');

$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(dirname(__FILE__) . '/../data/config');

$commandLine = '--config-file=mysql-plugin.yml';
$t->ok($cmd->run($parser, $commandLine), 'MysqlRestore executed successfully');

// Tear down
dropDb($mysqlUsername, $mysqlPassword, $dbName);
