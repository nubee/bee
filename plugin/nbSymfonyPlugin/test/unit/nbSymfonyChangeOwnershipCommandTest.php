<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));

$t = new lime_test(1);
$cmd = new nbSymfonyChangeOwnershipCommand();
echo $command_line =  nbConfig::get('nb_symfony_plugin_test_sync_project_target_path').' '.nbConfig::get('nb_symfony_plugin_test_owner_userid').' '.nbConfig::get('nb_symfony_plugin_test_owner_group');
$t->ok($cmd->run(new nbCommandLineParser(),$command_line),'Command SymfonyChangeOwnership called succefully');
