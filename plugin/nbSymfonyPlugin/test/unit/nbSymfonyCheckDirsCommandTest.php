<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));

$logFolder = nbConfig::get('nb_symfony_plugin_test_symfony_dir').'/log';
$cacheFolder = nbConfig::get('nb_symfony_plugin_test_symfony_dir').'/cache';
nbFileSystem::rmdir($logFolder,true);
nbFileSystem::rmdir($cacheFolder, true);

$t = new lime_test(3);
$cmd = new nbSymfonyCheckDirsCommand();
$t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('nb_symfony_plugin_test_symfony_dir')),'Command SymfonyCheckDirs called succefully');
$t->ok(file_exists($logFolder),'Check log dir existence');
$t->ok(file_exists($cacheFolder),'Check cache dir existence');