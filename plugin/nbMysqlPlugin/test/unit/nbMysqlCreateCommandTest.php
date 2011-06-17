<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbMysqlPlugin'));


$dbName = nbConfig::get('mysql_create_db-name');
$adminUser = nbConfig::get('mysql_create_admin-user');
$adminUserPwd = nbConfig::get('mysql_create_admin-user-pwd');
$dbUser = nbConfig::get('mysql_create_db-user');
$dbUserPwd = nbConfig::get('mysql_create_db-user-pwd');


$t = new lime_test(2);
$cmd = new nbMysqlCreateCommand();

$commandLine = $dbName.' '.$adminUser.' '.$adminUserPwd.' --db-user='.$dbUser.' --db-user-pwd='.$dbUserPwd;
$t->ok($cmd->run(new nbCommandLineParser(),$commandLine),'Command MysqlCreate called succefully');

$shell = new nbShell();
$cmd1 = 'mysql -u '.$dbUser.' --password='.$dbUserPwd.' -e "drop database '.$dbName.'"';
$shell->execute($cmd1);

$commandLine = $dbName.' '.$adminUser.' '.$adminUserPwd.' --db-user='.$dbUser;
$t->ok($cmd->run(new nbCommandLineParser(),$commandLine),'Command MysqlCreate called succefully without db user password');

$shell = new nbShell();
$cmd1 = 'mysql -u '.$dbUser.' --password='.$dbUserPwd.' -e "drop database '.$dbName.'"';
$shell->execute($cmd1);

