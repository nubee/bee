<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbMysqlPlugin'));

$dbName = nbConfig::get('mysql_db_name');
$mysqlUsername = nbConfig::get('mysql_mysql_username');
$mysqlPassword = nbConfig::get('mysql_mysql_password');
$username = nbConfig::get('mysql_username');
$password = nbConfig::get('mysql_password');

function formatPassword($password, $option = ' -p')
{
  if($password)
    return $option . $mysqlPassword;

  return '';
}

$t = new lime_test(2);
$t->comment('MysqlCreateCommand');

$cmd = new nbMysqlCreateCommand();

$commandLine = sprintf('%s %s %s --username=%s --password=%s', $dbName, $mysqlUsername, formatPassword($mysqlPassword), $username, $password);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command MysqlCreate executed succefully');

$t->comment('MysqlCreateCommand executed');

$shell = new nbShell();
$commandLine = sprintf('mysql -u %s %s -e "drop database %s"', $mysqlUsername, formatPassword($mysqlPassword, ' -p'), $dbName);
$shell->execute($commandLine);

$commandLine = sprintf('%s %s %s --username=%s', $dbName, $mysqlUsername, formatPassword($mysqlPassword), $username);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command MysqlCreate executed succefully without user password');

$shell = new nbShell();
$commandLine = sprintf('mysql -u %s %s -e "drop database %s"', $mysqlUsername, formatPassword($mysqlPassword, ' -p'), $dbName);
$shell->execute($commandLine);
