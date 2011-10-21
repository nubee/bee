<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$fs = nbFileSystem::getInstance();

$t = new lime_test(4);
$t->comment('Mysql Dump Command');

// Setup
createDb($mysqlUsername, $mysqlPassword, $dbName, $username, $password);
$cmd = new nbMysqlDumpCommand();

$commandLine = sprintf('%s %s %s %s', $dbName, $dumpPath, $username, $password);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlDump executed successfully');

$timestamp = date('YmdHi', time());
$dumpFile = sprintf('%s/%s/%s-%s.sql', getcwd(), $dumpPath, $dbName, $timestamp);
$t->ok(file_exists($dumpFile), 'Dump file exists');

// Cleaning up
$fs->delete($dumpFile);

$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(dirname(__FILE__) . '/../data/config');

$commandLine = '--config-file=mysql-plugin.yml';
$t->ok($cmd->run($parser, $commandLine), 'MysqlDump executed successfully from config file');

$timestamp = date('YmdHi', time());
$dumpFile = sprintf('%s/%s/%s-%s.sql', getcwd(), $dumpPath, $dbName, $timestamp);
$t->ok(file_exists($dumpFile), 'Dump file exists');

// Cleaning up
$fs->delete($dumpFile);

// Tear down
dropDb($mysqlUsername, $mysqlPassword, $dbName);
