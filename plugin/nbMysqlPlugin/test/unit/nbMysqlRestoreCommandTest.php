<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbMysqlPlugin'));


$t = new lime_test(2);
$cmd = new nbMysqlRestoreCommand();
$command_line =  nbConfig::get('mysql_restore_db-name').' '.nbConfig::get('mysql_restore_dump-file').' '.nbConfig::get('mysql_restore_db-user').' '.nbConfig::get('mysql_restore_db-user-pwd');
$t->ok($cmd->run(new nbCommandLineParser(),$command_line),'Command MysqlDump called succefully');

$cmd = new nbMysqlRestoreCommand();
$command_line =  '--config-file='.dirname(__FILE__).'/../config/config.yml';
$t->ok($cmd->run(new nbCommandLineParser(),$command_line),'Command MysqlDump called succefully');
