<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin','nbArchivePlugin','nbMysqlPlugin','nbFileSystemPlugin'));

$fileSystem = nbFileSystem::getInstance();

$symfonyRootDir = nbConfig::get('symfony_project-deploy_symfony-root-dir');
$application    = nbConfig::get('test_application');
$environment    = nbConfig::get('test_enviroment');
