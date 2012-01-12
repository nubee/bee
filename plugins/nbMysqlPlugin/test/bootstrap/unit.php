<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../data/config/config.yml', '', true);
$serviceContainer->pluginLoader->loadPlugins(array('nbMysqlPlugin'));

$dbName = nbConfig::get('mysql_db-name');
$mysqlAdminUsername = nbConfig::get('mysql_admin-username');
$mysqlAdminPassword = nbConfig::get('mysql_admin-password');
$tempAdminUsername = nbConfig::get('mysql_temp-admin-username');
$tempAdminPassword = nbConfig::get('mysql_temp-admin-password');
$tempAdminUsernameWithNoPassword = nbConfig::get('mysql_temp-admin-with-no-password');
$username = nbConfig::get('mysql_username');
$password = nbConfig::get('mysql_password');
$dumpPath = nbConfig::get('mysql_dump-path');
$dumpFilename = nbConfig::get('mysql_dump-filename');

function createDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName) {
  try {
    $cmd = sprintf('mysqladmin -u%s %s create %s"', $mysqlAdminUsername, ($mysqlAdminPassword != '' ? '-p' . $mysqlAdminPassword : ''), $dbName);
    $shell = new nbShell();
    $shell->execute($cmd, true);
  } catch (Exception $e) {
    echo $e->message;
  }
}

function dropDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName) {
  try {
    $cmd = sprintf('mysqladmin -u%s %s drop %s --force', $mysqlAdminUsername, ($mysqlAdminPassword != '' ? '-p' . $mysqlAdminPassword : ''), $dbName);
    $shell = new nbShell();
    $shell->execute($cmd, true);
  } catch (Exception $e) {
    // Ignore errors
  }
}

function createAdminUser($mysqlAdminUsername, $mysqlAdminPassword, $newAdminUsername, $newAdminUserPassword='') {
  try {
    $cmd = sprintf('mysql -u%s %s -e "grant all privileges on *.* to \'%s\'@\'localhost\'  identified by \'%s\' with grant option"', $mysqlAdminUsername, ($mysqlAdminPassword != '' ? '-p' . $mysqlAdminPassword : ''), $newAdminUsername, $newAdminUserPassword);
    $shell = new nbShell();
    $shell->execute($cmd, true);
  } catch (Exception $e) {
    // Ignore errors
  }
}

function createDatabaseUserWithGrantsOnDb($mysqlAdminUsername, $mysqlAdminPassword, $dbName, $username, $password='') {
  try {
    $cmd = sprintf('mysql -u%s %s -e "grant all privileges on %s.* to \'%s\'@\'localhost\' %s"', $mysqlAdminUsername, ($mysqlAdminPassword != '' ? '-p' . $mysqlAdminPassword : ''), $dbName, $username, ($password != '' ? sprintf('identified by \'%s\'', $password) : ''));
    $shell = new nbShell();
    $shell->execute($cmd, true);
  } catch (Exception $e) {
    // Ignore errors
  }
}

function dropDatabaseUser($mysqlAdminUsername, $mysqlAdminPassword, $username) {
  try {
    $cmd = sprintf('mysql -u%s %s -e "drop user \'%s\'@\'localhost\'"', $mysqlAdminUsername, ($mysqlAdminPassword != '' ? '-p' . $mysqlAdminPassword : ''), $username);
    $shell = new nbShell();
    $shell->execute($cmd, true);
  } catch (Exception $e) {
    // Ignore errors
  }
}

function checkMysql() {

  if (nbConfig::has('mysql_admin-username')) 
    return true;
  $t = new lime_test(0);
  return false;
}
