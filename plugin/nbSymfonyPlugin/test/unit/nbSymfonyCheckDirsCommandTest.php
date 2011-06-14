<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));

$logFolder = nbConfig::get('symfony_project-deploy_symfony-exe-path').'/log';
$cacheFolder = nbConfig::get('symfony_project-deploy_symfony-exe-path').'/cache';
nbFileSystem::rmdir($logFolder,true);
nbFileSystem::rmdir($cacheFolder, true);

$t = new lime_test(3);
$cmd = new nbSymfonyCheckDirsCommand();
$t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('symfony_project-deploy_symfony-exe-path')),'Command SymfonyCheckDirs called succefully');
$t->ok(file_exists($logFolder),'Check log dir existence');
$t->ok(file_exists($cacheFolder),'Check cache dir existence');

nbFileSystem::rmdir($logFolder,true);
nbFileSystem::rmdir($cacheFolder, true);
