<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));

$t = new lime_test(7);
$fileToSync = nbConfig::get('nb_symfony_plugin_test_sync_project_target_path').nbConfig::get('nb_symfony_plugin_test_sync_project_file_to_sync');
$folderToExclude = nbConfig::get('nb_symfony_plugin_test_sync_project_target_path').nbConfig::get('nb_symfony_plugin_test_sync_project_folder_to_exclude');
$fileToExclude = nbConfig::get('nb_symfony_plugin_test_sync_project_target_path').nbConfig::get('nb_symfony_plugin_test_sync_project_file_to_exclude');
$excludeFile = nbConfig::get('nb_symfony_plugin_test_sync_project_exclude_file');
nbFileSystem::delete($fileToSync);
nbFileSystem::rmdir($folderToExclude);
nbFileSystem::delete($fileToExclude);
$cmd = new nbSymfonySyncProjectCommand();
//echo nbConfig::get('nb_symfony_plugin_test_sync_project_source_path').' '.nbConfig::get('nb_symfony_plugin_test_sync_project_target_path');
$t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('nb_symfony_plugin_test_sync_project_source_path').' '.nbConfig::get('nb_symfony_plugin_test_sync_project_target_path')),'Command SymfonySyncProject called succefully');
$t->ok(!file_exists($fileToSync),'fileToSync wasn\'t syncronyzed in the target site because doit option was not set');
$t->ok($cmd->run(new nbCommandLineParser(), '--doit '.nbConfig::get('nb_symfony_plugin_test_sync_project_source_path').' '.nbConfig::get('nb_symfony_plugin_test_sync_project_target_path')),'Command SymfonySyncProject called succefully');
$t->ok(file_exists($fileToSync),'fileToSync was syncronyzed in the target site');

nbFileSystem::delete($fileToSync);
nbFileSystem::rmdir($folderToExclude);
nbFileSystem::delete($fileToExclude);
$shell = new nbShell();
//$shell->execute('echo '.nbConfig::get('nb_symfony_plugin_test_sync_project_folder_to_exclude')."\n".'>'.$excludeFile);
//$shell->execute('echo '.nbConfig::get('nb_symfony_plugin_test_sync_project_file_to_exclude').'>>'.$excludeFile);
$t->ok($cmd->run(new nbCommandLineParser(), '--doit --exclude-from='.$excludeFile.' '.nbConfig::get('nb_symfony_plugin_test_sync_project_source_path').' '.nbConfig::get('nb_symfony_plugin_test_sync_project_target_path')),'Command SymfonySyncProject called succefully');
$t->ok(!file_exists($folderToExclude),'folderToExclude was not syncronyzed in the target site');
$t->ok(!file_exists($fileToExclude),'fileToExclude was not syncronyzed in the target site');

