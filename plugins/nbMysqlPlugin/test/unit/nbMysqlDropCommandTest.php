<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';
if (!checkMysql()) return true;

$t = new lime_test(3);
$t->comment('Mysql Drop Command');

// Setup
createAdminUser($mysqlAdminUsername, $mysqlAdminPassword, $tempAdminUsername, $tempAdminPassword);
createDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);

$t->comment('MysqlDropCommand executed by admin user with password');
$cmd = new nbMysqlDropCommand();
$commandLine = sprintf('%s %s %s', $dbName, $tempAdminUsername, $tempAdminPassword);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlDrop executed successfully');

//TearDown
dropDatabaseUser($mysqlAdminUsername, $mysqlAdminPassword, $tempAdminUsername);

// Setup
createAdminUser($mysqlAdminUsername, $mysqlAdminPassword, $tempAdminUsernameWithNoPassword);
createDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);

$t->comment('MysqlDropCommand executed by admin user without password');
$cmd = new nbMysqlDropCommand();
$commandLine = sprintf('%s %s', $dbName, $tempAdminUsernameWithNoPassword);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlDrop executed successfully');

//TearDown
dropDatabaseUser($mysqlAdminUsername, $mysqlAdminPassword, $tempAdminUsernameWithNoPassword);

// Setup
createDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);

$cmd = new nbMysqlDropCommand();
$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(dirname(__FILE__) . '/../data/config');

$commandLine = '--config-file=mysql-plugin.yml';
$t->ok($cmd->run($parser, $commandLine), 'MysqlDrop executed successfully from config file');

