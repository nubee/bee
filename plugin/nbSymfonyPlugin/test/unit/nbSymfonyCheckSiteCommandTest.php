<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));
$version_too_high = nbConfig::get('nb_symfony_plugin_test_migrate_version_high')+1;
$t = new lime_test(2);
$cmd = new nbSymfonyCheckSiteCommand();
$t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('nb_symfony_plugin_test_web_site').' 200'),'Command SymfonyCheckSite called succefully');
$t->ok(!$cmd->run(new nbCommandLineParser(), nbConfig::get('nb_symfony_plugin_test_web_site').' 500'),'Command SymfonyCheckSite called succefully');
