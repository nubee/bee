<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));
$t = new lime_test(2);
$cmd = new nbSymfonyCheckSiteCommand();
$commandLine =  nbConfig::get('test_check-website').' 200';

$t->ok($cmd->run(new nbCommandLineParser(), $commandLine),'Command SymfonyCheckSite called succefully');

$commandLine =  nbConfig::get('test_check-website').' 500';

$t->ok(!$cmd->run(new nbCommandLineParser(), $commandLine),'Command SymfonyCheckSite called succefully');