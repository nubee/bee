<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../data/config/archive-plugin.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbArchivePlugin'));

$fs             = nbFileSystem::getInstance();
$sourceDir      = nbConfig::get('archive_archive-dir_source-dir');
$destinationDir = nbConfig::get('archive_archive-dir_destination-dir');
$dirToBeCreated = nbConfig::get('archive_archive-dir_destination-dir') . '/created-dir';
$dirNotExists   = 'fake-dir';

$filename = basename($sourceDir);

$t = new lime_test(10);
$t->comment('Archive Dir');

$t->comment('  1. - Create archive');
$cmd = new nbArchiveDirCommand();
$timestamp = date('YmdHi', time());
$archivedFile =  sprintf('%s/%s-%s.tar.gz', $destinationDir, $filename, $timestamp);

$commandLine = $sourceDir . ' ' . $destinationDir;
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Directory has been archived into a destination file ');
$t->ok(file_exists($archivedFile), 'Destination file exists');

// Tear down
$fs->delete($archivedFile);

$t->comment('  2. - Create archive with config-file');
// parameters passed by config-file
$timestamp = date('YmdHi', time());
$archivedFile =  sprintf('%s/%s-%s.tar.gz', $destinationDir, $filename, $timestamp);

$commandLine = '--config-file=archive-plugin.yml';

$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(
  dirname(__FILE__) . '/../data/config'
);

$t->ok($cmd->run($parser, $commandLine), 'Directory has been archived into a destination file with a config-file');
$t->ok(file_exists($archivedFile), 'Destination file exists');

// Tear down
$fs->delete($archivedFile);

$t->comment('  3. - Cannot create archive with directories options');
try {
  $commandLine = $dirNotExists . ' ' . $destinationDir;
  $cmd->run(new nbCommandLineParser(), $commandLine);
  
  $t->fail('Cannot archive a source folder that does not exist ');
}
catch(InvalidArgumentException $e) {
  $t->pass('Cannot archive a source folder that does not exist');
}

try {
  $commandLine = $sourceDir . ' ' . $dirNotExists;
  $cmd->run(new nbCommandLineParser(), $commandLine);
  
  $t->fail('Cannot archive in a destination folder that does not exist and no --create-destination-dir option set');
}
catch(InvalidArgumentException $e) {
  $t->pass('Cannot archive in a destination folder that does not exist and no --create-destination-dir option set');
}

$t->comment('  4. - Create archive in a new directory');
$timestamp = date('YmdHi', time());
$archivedFile =  sprintf('%s/%s-%s.tar.gz', $dirToBeCreated, $filename, $timestamp);

$commandLine = sprintf('%s %s --create-destination-dir', $sourceDir, $dirToBeCreated);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Directory has been archived into a new destination directory');
$t->ok(file_exists($archivedFile), 'Destination file exists');

// Tear down
$fs->rmdir($dirToBeCreated, true);

$t->comment('  5. - Create archive with a new filename');
$timestamp = date('YmdHi', time());
$filename = 'archive.tar.gz';
$archivedFile =  sprintf('%s/%s', $destinationDir, $filename);

$commandLine = sprintf('%s %s --filename=%s', $sourceDir, $destinationDir, $filename);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Directory has been archived into a new destination directory');
$t->ok(file_exists($archivedFile), 'Destination file exists');

// Tear down
$fs->delete($archivedFile);
