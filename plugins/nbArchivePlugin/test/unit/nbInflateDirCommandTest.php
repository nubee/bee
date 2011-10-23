<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../data/config/archive-plugin.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbArchivePlugin'));

$fs = nbFileSystem::getInstance();
$archiveDir = nbConfig::get('archive_inflate-dir_archive-dir');
$sourceDir  = nbConfig::get('archive_inflate-dir_source-dir');

$filename = basename($sourceDir);

$t = new lime_test(4);
$t->comment('Inflate Dir');

$cmd = new nbInflateDirCommand();
$timestamp = date('YmdHi', time());
$fileTgz =  sprintf('%s/%s-%s.tgz', $archiveDir, $filename, $timestamp);

$commandLine = $sourceDir . ' ' . $archiveDir;
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command archive:inflate-dir inflate a directory into a destination file');
$t->ok(file_exists($fileTgz), 'Destination file exists');

// Tear down
$fs->delete($fileTgz);


// parameter passed as config-file options
$timestamp = date('YmdHi', time());
$fileTgz =  sprintf('%s/%s-%s.tgz', $archiveDir, $filename, $timestamp);

$commandLine = '--config-file=archive-plugin.yml';

$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(
  dirname(__FILE__) . '/../data/config'
);

$t->ok($cmd->run($parser, $commandLine), 'Command archive:inflate-dir inflate a directory into a destination file');
$t->ok(file_exists($fileTgz), 'Destination file exists');

// Tear down
$fs->delete($fileTgz);
