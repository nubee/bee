<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$t = new lime_test(4);
// Setup
createAdminUser($mysqlAdminUsername, $mysqlAdminPassword, $tempAdminUsername, $tempAdminPassword);

$t->comment('MysqlCreateCommand executed by admin user with password');
$cmd = new nbMysqlCreateCommand();
$commandLine = sprintf('%s %s %s --username=%s --password=%s', $dbName, $tempAdminUsername, $tempAdminPassword, $username, $password);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command MysqlCreate Command executed by admin user with password done');

// Tear down
dropDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);

$t->comment('MysqlCreateCommand executed by admin user with password and user without password');
$cmd = new nbMysqlCreateCommand();
$commandLine = sprintf('%s %s %s --username=%s', $dbName, $tempAdminUsername, $tempAdminPassword, $username);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command MysqlCreate Command executed by admin user with password and user without password done');

// Tear down
dropDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
dropDatabaseUser($mysqlAdminUsername, $mysqlAdminPassword, $tempAdminUsername);

// Setup
createAdminUser($mysqlAdminUsername, $mysqlAdminPassword, $tempAdminUsernameWithNoPassword);

$t->comment('MysqlCreateCommand executed by admin user without password');
$cmd = new nbMysqlCreateCommand();
$commandLine = sprintf('%s %s %s --username=%s --password=%s', $dbName, $tempAdminUsernameWithNoPassword, '', $username, $password);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command MysqlCreate Command executed by admin user with password done');

// Tear down
dropDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);

$t->comment('MysqlCreateCommand executed by admin user with password and user without password');
$cmd = new nbMysqlCreateCommand();
$commandLine = sprintf('%s %s %s --username=%s', $dbName, $tempAdminUsernameWithNoPassword, '', $username);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command MysqlCreate Command executed by admin user with password and user without password done');

// Tear down
dropDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName);
dropDatabaseUser($mysqlAdminUsername, $mysqlAdminPassword, $tempAdminUsernameWithNoPassword);
dropDatabaseUser($mysqlAdminUsername, $mysqlAdminPassword, $username);

