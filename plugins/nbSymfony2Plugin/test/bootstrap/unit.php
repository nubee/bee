<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';

//$configParser->parseFile(dirname(__FILE__) . '/../data/config/symfony2-plugin.yml', '', true);
$configParser->parseFile(dirname(__FILE__) . '/../data/config/symfony2-deploy.yml', '', true);
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfony2Plugin', 'nbArchivePlugin', 'nbMysqlPlugin', 'nbFileSystemPlugin'));

$fileSystem = nbFileSystem::getInstance();

$symfonyRootDir = nbConfig::get('symfony_project-deploy_symfony-root-dir');
$symfonyExePath = nbConfig::get('symfony_project-deploy_symfony-exe-path');
$application    = nbConfig::get('test_application');
$environment    = nbConfig::get('test_environment');
