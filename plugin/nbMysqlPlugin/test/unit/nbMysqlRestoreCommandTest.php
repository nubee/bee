<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbMysqlPlugin'));

$dbName = nbConfig::get('mysql_db_name');
$mysqlUsername = nbConfig::get('mysql_mysql_username');
$mysqlPassword = nbConfig::get('mysql_mysql_password');
$username = nbConfig::get('mysql_username');
$password = nbConfig::get('mysql_password');
$dumpFilename = nbConfig::get('mysql_dump_filename');

$t = new lime_test(2);
$cmd = new nbMysqlRestoreCommand();
$commandLine = sprintf('%s %s %s --username=%s --password=%s', $dbName, $dumpFilename, $username, $password);

$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlRestore executed succefully');

$cmd = new nbMysqlRestoreCommand();
$commandLine = sprintf('--config-file=%s', dirname(__FILE__) . '/../config/config.yml');
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlRestore executed succefully');
