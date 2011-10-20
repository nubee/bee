<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';

$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));
$t = new lime_test(0);

$cmd = new nbSymfonyDiemSetupCommand();
$cmd->run(new nbCommandLineParser(), nbConfig::get('symfony_project-deploy_symfony-exe-path'), 'Command SymfonyDiemSetup called succefully');
