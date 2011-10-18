<?php


require_once dirname(__FILE__) . '/bootstrap.php';

$t = new lime_test(2);
$t->comment('Mysql Dump Command');

// Setup
createDb($mysqlUsername, $mysqlPassword, $dbName, $username, $password);

$cmd = new nbMysqlDumpCommand();
$commandLine = sprintf('%s %s %s %s', $dbName, $dumpPath, $username, $password);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlDump executed succefully');

$timestamp = date('YmdHi', time());
$dumpFile = sprintf('%s/%s-%s.sql', $dumpPath, $dbName, $timestamp);
$t->ok(file_exists($dumpFile), 'Dump file exists');

// Cleaning up
nbFileSystem::delete($dumpFile);
/*
$commandLine = sprintf('--config-file=%s', dirname(__FILE__) . '/../config/config.yml');
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlDump executed succefully from config file');

$timestamp = date('YmdHi', time());
$dumpFile = sprintf('%s/%s-%s.sql', $dumpPath, $dbName, $timestamp);
$t->ok(file_exists($dumpFile), 'Dump file exists');

// Cleaning up
nbFileSystem::delete($dumpFile);
*/

// Tear down
dropDb($mysqlUsername, $mysqlPassword, $dbName);
