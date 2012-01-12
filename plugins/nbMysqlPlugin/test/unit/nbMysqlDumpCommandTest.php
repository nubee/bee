<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';
if (!checkMysql()) return true;

$fs = nbFileSystem::getInstance();

$t = new lime_test(6);
$t->comment('Mysql Dump Command');

// Setup
createDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
createDatabaseUserWithGrantsOnDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName, $username, $password);

$cmd = new nbMysqlDumpCommand();

$commandLine = sprintf('%s %s %s %s', $dbName, $dumpPath, $username, $password);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlDump executed successfully');

$timestamp = date('YmdHi', time());
$dumpFile = sprintf('%s/%s/%s-%s.sql', getcwd(), $dumpPath, $dbName, $timestamp);
$t->ok(file_exists($dumpFile), 'Dump file exists');

// Tear Down
$fs->delete($dumpFile);
dropDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
dropDatabaseUser($mysqlAdminUsername, $mysqlAdminPassword, $username);

// Setup
createDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
createDatabaseUserWithGrantsOnDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName, $username);

$cmd = new nbMysqlDumpCommand();

$commandLine = sprintf('%s %s %s', $dbName, $dumpPath, $username);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlDump executed successfully');

$timestamp = date('YmdHi', time());
$dumpFile = sprintf('%s/%s/%s-%s.sql', getcwd(), $dumpPath, $dbName, $timestamp);
$t->ok(file_exists($dumpFile), 'Dump file exists');

// Tear Down
$fs->delete($dumpFile);
dropDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
dropDatabaseUser($mysqlAdminUsername, $mysqlAdminPassword, $username);

// Setup
createDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
createDatabaseUserWithGrantsOnDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName, $username, $password);

$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(dirname(__FILE__) . '/../data/config');

$commandLine = '--config-file=mysql-plugin.yml';
$t->ok($cmd->run($parser, $commandLine), 'MysqlDump executed successfully from config file');

$timestamp = date('YmdHi', time());
$dumpFile = sprintf('%s/%s/%s-%s.sql', getcwd(), $dumpPath, $dbName, $timestamp);
$t->ok(file_exists($dumpFile), 'Dump file exists');

// Tear down
$fs->delete($dumpFile);
dropDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
dropDatabaseUser($mysqlAdminUsername, $mysqlAdminPassword, $username);

