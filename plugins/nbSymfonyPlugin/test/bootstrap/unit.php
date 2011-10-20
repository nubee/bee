<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));

$fileSystem = nbFileSystem::getInstance();

$symfonyRootDir = nbConfig::get('symfony_project-deploy_symfony-root-dir');
