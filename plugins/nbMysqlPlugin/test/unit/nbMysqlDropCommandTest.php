<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$t = new lime_test(2);
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


