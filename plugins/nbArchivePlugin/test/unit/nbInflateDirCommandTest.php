<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../data/config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbArchivePlugin'));

$fs = nbFileSystem::getInstance();

$t = new lime_test(4);

$cmd = new nbInflateDirCommand();

$archivePath = nbConfig::get('archive_inflate-dir_archive-path');
$targetPath = nbConfig::get('archive_inflate-dir_target-path');
$targetDir = nbConfig::get('archive_inflate-dir_target-dir');

// parameter passed as arguments in command line
$timestamp = date('YmdHi', time());
$fileTgz =  sprintf('%s/%s-%s.tgz', $archivePath, $targetDir, $timestamp);

$commandLine = $targetPath . ' ' . $targetDir . ' ' . $archivePath;
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command archive:inflate-dir inflate a directory into a destination file');
$t->ok(file_exists($fileTgz), 'Destination file exists');

// Tear down
$fs->delete($fileTgz);


// parameter passed as config-file options
$timestamp = date('YmdHi', time());
$fileTgz =  sprintf('%s/%s-%s.tgz', $archivePath, $targetDir, $timestamp);

$commandLine = '--config-file=config.yml';

$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(
  dirname(__FILE__) . '/../data/config'
);

$t->ok($cmd->run($parser, $commandLine), 'Command archive:inflate-dir inflate a directory into a destination file');
$t->ok(file_exists($fileTgz), 'Destination file exists');

// Tear down
$fs->delete($fileTgz);
