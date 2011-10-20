<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';

$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));

$t = new lime_test(0);

$symfony = nbConfig::get('symfony_project-deploy_symfony-root-dir');
$application = nbConfig::get('test_go-online_application');
$environment = nbConfig::get('test_go-online_enviroment');

$cmd = new nbSymfonyGoOnlineCommand();
$cmd->run(new nbCommandLineParser(), sprintf('%s %s %s', $symfony, $application, $environment), 'Command runs succefully');
