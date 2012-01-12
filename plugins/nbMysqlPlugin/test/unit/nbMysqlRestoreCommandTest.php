<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';
if (!checkMysql()) return true;

$t = new lime_test(3);
$t->comment('Mysql Restore Command');

// Setup
createDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
createDatabaseUserWithGrantsOnDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName, $username, $password);

$cmd = new nbMysqlRestoreCommand();
$commandLine = sprintf('%s %s %s %s', $dbName, $dumpFilename, $username, $password);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlRestore executed successfully');

// Tear down
dropDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
dropDatabaseUser($mysqlAdminUsername, $mysqlAdminPassword, $username);

// Setup
createDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
createDatabaseUserWithGrantsOnDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName, $username);

$cmd = new nbMysqlRestoreCommand();
$commandLine = sprintf('%s %s %s', $dbName, $dumpFilename, $username);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlRestore executed successfully');

// Tear down
dropDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
dropDatabaseUser($mysqlAdminUsername, $mysqlAdminPassword, $username);

// Setup
createDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
createDatabaseUserWithGrantsOnDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName, $username, $password);

$cmd = new nbMysqlRestoreCommand();

$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(dirname(__FILE__) . '/../data/config');

$commandLine = '--config-file=mysql-plugin.yml';
$t->ok($cmd->run($parser, $commandLine), 'MysqlRestore executed successfully');

// Tear down
dropDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
dropDatabaseUser($mysqlAdminUsername, $mysqlAdminPassword, $username);
