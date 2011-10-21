<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$t = new lime_test(1);
$t->comment('Mysql Drop Command');

// Setup
createDb($mysqlUsername, $mysqlPassword, $dbName, $username, $password);

$cmd = new nbMysqlDropCommand();
$commandLine = sprintf('%s %s %s', $dbName, $username, $password);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'MysqlDrop executed successfully');

