<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbMysqlPlugin'));

$timestamp = date('YmdHi', time());

$dbName = nbConfig::get('mysql_db_name');
$mysqlUsername = nbConfig::get('mysql_mysql_username');
$mysqlPassword = nbConfig::get('mysql_mysql_password');
$dumpPath = nbConfig::get('mysql_dump_path');

$t = new lime_test(4);
$cmd = new nbMysqlDumpCommand();

$commandLine = sprintf('%s %s --username=%s --password=%s', $dbName, $dumpPath, $mysqlUsername, $mysqlPassword, $username, $password);

$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlDump executed succefully');
$dumpFile = $dumpPath . '/' . $dbName . '-' . $timestamp . '.sql';
$t->ok(file_exists($dumpFile), 'Dump file exists');
nbFileSystem::delete($dumpFile);

$timestamp = date('YmdHi', time());

$commandLine = sprintf('--config-file=%s', dirname(__FILE__) . '/../config/config.yml');
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlDump executed succefully from config file');
$dumpFile = $dumpPath . '/' . $dbName . '-' . $timestamp . '.sql';
$t->ok(file_exists($dumpFile), 'Dump file exists');
nbFileSystem::delete($dumpFile);

