<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';

$configParser->parseFile(dirname(__FILE__) . '/../data/config/symfony-plugin.yml', '', true);
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin', 'nbArchivePlugin', 'nbMysqlPlugin', 'nbFileSystemPlugin'));

$fileSystem = nbFileSystem::getInstance();

$symfonyRootDir = nbConfig::get('symfony_project-deploy_symfony-root-dir');
$symfonyExePath = nbConfig::get('symfony_project-deploy_symfony-exe-path');
$application    = nbConfig::get('test_application');
$environment    = nbConfig::get('test_environment');

function checkMysql() {

  if (nbConfig::has('mysql_admin-username')) 
    return true;
  $t = new lime_test(0);
  return false;
}