<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../data/config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbMysqlPlugin'));

$dbName        = nbConfig::get('mysql_db-name');
$mysqlUsername = nbConfig::get('mysql_mysql-username');
$mysqlPassword = nbConfig::get('mysql_mysql-password');
$username      = nbConfig::get('mysql_username');
$password      = nbConfig::get('mysql_password');
$dumpPath      = nbConfig::get('mysql_dump-path');
$dumpFilename  = nbConfig::get('mysql_dump-filename');

function formatPassword($password, $option = '')
{
  if($password)
    return $option . $password;

  return '';
}

function createDb($mysqlUsername, $mysqlPassword, $dbName, $username, $password) {
  $cmd = new nbMysqlCreateCommand();
  $commandLine = sprintf('%s %s %s --username=%s --password=%s', $dbName, $mysqlUsername, formatPassword($mysqlPassword), $username, $password);
  $cmd->run(new nbCommandLineParser(), $commandLine);
}

function dropDb($mysqlUsername, $mysqlPassword, $dbName)
{
  $cmd = new nbMysqlDropCommand();
  $commandLine = sprintf('%s %s %s', $dbName, $mysqlUsername, formatPassword($mysqlPassword));
  $cmd->run(new nbCommandLineParser(), $commandLine);
}
