<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));
$t = new lime_test(0);

$cmd = new nbSymfonyGoOnlineCommand();
$cmd->run(new nbCommandLineParser(), nbConfig::get('nb_symfony_plugin_test_symfony_dir')." ".nbConfig::get('nb_symfony_plugin_test_application')." ".nbConfig::get('nb_symfony_plugin_test_enviroment'),'Command SymfonyGoOnline called succefully');
