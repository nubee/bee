<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';

$dataDir = dirname(__FILE__) . '/../data/config';
$sandboxDir = dirname(__FILE__) . '/../sandbox';

$fileSystem = nbFileSystem::getInstance();

$configParser->parseFile($dataDir . '/config.yml');

$serviceContainer->pluginLoader->loadPlugins(array('nbFileSystemPlugin'));
