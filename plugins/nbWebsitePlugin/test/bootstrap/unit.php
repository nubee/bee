<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';

nbConfig::set('web_base_dir', 'plugins/nbWebsitePlugin/test/data');
$configParser->parseFile(dirname(__FILE__) . '/../data/config/website-deploy.yml', '', true);
$serviceContainer->pluginLoader->loadPlugins(array('nbWebsitePlugin', 'nbArchivePlugin', 'nbMysqlPlugin', 'nbFileSystemPlugin'));

$fileSystem = nbFileSystem::getInstance();
