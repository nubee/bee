<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbMysqlPlugin'));

#nbFileSystem::delete(nbConfig::get('nb_mysql_dump_path').nbConfig::get('nb_mysql_db_name').'.sql');
$timestamp = date('YmdHi',  time());

$t = new lime_test(2);
$cmd = new nbMysqlDumpCommand();
echo $command_line =  nbConfig::get('nb_mysql_db_name').' '.nbConfig::get('nb_mysql_dump_path').' '.nbConfig::get('nb_mysql_db_user_id').' '.nbConfig::get('nb_mysql_db_user_password')."\n";
$t->ok($cmd->run(new nbCommandLineParser(),$command_line),'Command MysqlDump called succefully');
echo nbConfig::get('nb_mysql_dump_path').nbConfig::get('nb_mysql_db_name').'-'.$timestamp.'.sql';
$t->ok(file_exists(nbConfig::get('nb_mysql_dump_path').'/'.nbConfig::get('nb_mysql_db_name').'-'.$timestamp.'.sql'), 'Dump file exist');
