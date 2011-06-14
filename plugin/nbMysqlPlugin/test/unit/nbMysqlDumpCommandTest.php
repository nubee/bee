<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbMysqlPlugin'));

#nbFileSystem::delete(nbConfig::get('nb_mysql_dump_path').nbConfig::get('nb_mysql_db_name').'.sql');
$timestamp = date('YmdHi',  time());

$t = new lime_test(4);
$cmd = new nbMysqlDumpCommand();
$command_line =  nbConfig::get('mysql_dump_db-name').' '.nbConfig::get('mysql_dump_dump-path').' '.nbConfig::get('mysql_dump_db-user').' '.nbConfig::get('mysql_dump_db-user-pwd');
$t->ok($cmd->run(new nbCommandLineParser(),$command_line),'Command MysqlDump called succefully');
$dumpFile = nbConfig::get('mysql_dump_dump-path').'/'.nbConfig::get('mysql_dump_db-name').'-'.$timestamp.'.sql';
$t->ok(file_exists($dumpFile), 'Dump file exist');
nbFileSystem::delete($dumpFile);

$timestamp = date('YmdHi',  time());

$command_line = '--config-file='.dirname(__FILE__).'/../config/config.yml';
$t->ok($cmd->run(new nbCommandLineParser(),$command_line),'Command MysqlDump called succefully');
$dumpFile = nbConfig::get('mysql_dump_dump-path').'/'.nbConfig::get('mysql_dump_db-name').'-'.$timestamp.'.sql';
$t->ok(file_exists($dumpFile), 'Dump file exist');
nbFileSystem::delete($dumpFile);

