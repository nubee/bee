<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));

$fs = nbFileSystem::getInstance();
$rootDir = nbConfig::get('symfony_project_deploy_symfony_root_dir');
$logDir =  $rootDir . '/log';
$cacheDir = $rootDir . '/cache';

$t = new lime_test(3);
$cmd = new nbSymfonyCheckDirsCommand();
$t->ok($cmd->run(new nbCommandLineParser(), $rootDir), 'Command SymfonyCheckDirs called succefully');
$t->ok(file_exists($logDir), 'Check log dir existence');
$t->ok(file_exists($cacheDir), 'Check cache dir existence');

$fs->rmdir($logDir, true);
$fs->rmdir($cacheDir, true);
