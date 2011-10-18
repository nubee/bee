<?php

require_once dirname(__FILE__) . '/bootstrap.php';

$t = new lime_test(1);
$t->comment('Mysql Restore Command');

// Setup
createDb($mysqlUsername, $mysqlPassword, $dbName, $username, $password);

$cmd = new nbMysqlRestoreCommand();
$commandLine = sprintf('%s %s %s %s', $dbName, $dumpFilename, $username, $password);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlRestore executed succefully');

/*
$cmd = new nbMysqlRestoreCommand();
$commandLine = sprintf('--config-file=%s', dirname(__FILE__) . '/../config/config.yml');
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlRestore executed succefully');
*/
// Tear down
dropDb($mysqlUsername, $mysqlPassword, $dbName);
