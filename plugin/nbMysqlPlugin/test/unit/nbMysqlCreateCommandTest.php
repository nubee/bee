<?php

require_once dirname(__FILE__) . '/bootstrap.php';

$t = new lime_test(2);
$t->comment('Mysql Create Command');

$cmd = new nbMysqlCreateCommand();
$commandLine = sprintf('%s %s %s --username=%s --password=%s', $dbName, $mysqlUsername, formatPassword($mysqlPassword), $username, $password);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command MysqlCreate executed succefully');

$t->comment('MysqlCreateCommand executed');

// Tear down
dropDb($mysqlUsername, $mysqlPassword, $dbName);

$cmd = new nbMysqlCreateCommand();
$commandLine = sprintf('%s %s %s --username=%s', $dbName, $mysqlUsername, formatPassword($mysqlPassword), $username);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command MysqlCreate executed succefully without user password');

// Tear down
dropDb($mysqlUsername, $mysqlPassword, $dbName);
