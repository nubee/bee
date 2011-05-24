<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbMysqlPlugin'));


$t = new lime_test(1);
$cmd = new nbMysqlRestoreCommand();
echo $command_line =  nbConfig::get('nb_mysql_db_name').' '.nbConfig::get('nb_mysql_dump_file').' '.nbConfig::get('nb_mysql_db_user_id').' '.nbConfig::get('nb_mysql_db_user_password')."\n";
$t->ok($cmd->run(new nbCommandLineParser(),$command_line),'Command MysqlDump called succefully');
