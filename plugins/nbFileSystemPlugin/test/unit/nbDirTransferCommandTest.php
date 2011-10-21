<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$t = new lime_test(32);
$fs = nbFileSystem::getInstance();

$sourceDir       = nbConfig::get('filesystem_dir-transfer_source-dir');
$targetDir       = nbFileSystem::sanitizeDir(nbConfig::get('filesystem_dir-transfer_target-dir'));
$fileToSync      = $targetDir . '/' . nbConfig::get('filesystem_test_file-to-sync');
$folderToExclude = $targetDir . '/' . nbConfig::get('filesystem_test_folder-to-exclude');
$fileToExclude   = $targetDir . '/' . nbConfig::get('filesystem_test_file-to-exclude');
$fileToInclude   = $targetDir . '/' . nbConfig::get('filesystem_test_file-to-include');
$otherFileToSync = $targetDir . '/' . nbConfig::get('filesystem_test_other-file-to-sync');
$fileToDelete    = $targetDir . '/fileToDelete';
$excludeFile     = nbConfig::get('filesystem_dir-transfer_exclude-from');
$includeFile     = nbConfig::get('filesystem_dir-transfer_include-from');

$cmd = new nbDirTransferCommand();

$fs->touch($fileToDelete);

$commandLine = sprintf('--delete %s %s', $sourceDir, $targetDir);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command nbDirTransfer called successfully dry run');
$t->ok(!file_exists($fileToSync), 'fileToSync wasn\'t synchronized in the target site because doit option was not set');
$t->ok(!file_exists($otherFileToSync), 'otherFileToSyn wasn\'t synchronized in the target folder');
$t->ok(!file_exists($folderToExclude), 'folderToExclude wasn\'t synchronized in the target folder');
$t->ok(!file_exists($fileToExclude), 'fileToExclude wasn\'t synchronized in the target folder');
$t->ok(!file_exists($fileToInclude), 'fileToInclude wasn\'t synchronized in the target folder');
$t->ok(file_exists($fileToDelete), 'fileToDelete wasn\'t deleted in the target folder');

$t->ok($cmd->run(new nbCommandLineParser(), '--doit ' . $commandLine), 'Command nbDirTransfer called successfully doit option set');
$t->ok(file_exists($fileToSync), 'fileToSync was synchronized in the target folder');
$t->ok(file_exists($otherFileToSync), 'otherFileToSync was synchronized in the target folder');
$t->ok(file_exists($folderToExclude), 'folderToExclude was synchronized in the target folder');
$t->ok(file_exists($fileToExclude), 'fileToEclude was synchronized in the target folder');
$t->ok(file_exists($fileToInclude), 'fileToInclude was synchronized in the target folder');
$t->ok(!file_exists($fileToDelete), 'fileToDelete was deleted in the target folder');

$fs->delete($fileToSync);
$fs->delete($folderToExclude . '/readme');
$fs->rmdir($folderToExclude);
$fs->delete($fileToExclude);
$fs->delete($fileToInclude);
$fs->delete($otherFileToSync);

$t->ok($cmd->run(new nbCommandLineParser(), '--doit --exclude-from=' . $excludeFile . ' ' . $commandLine), 'Command nbDirTransfer called successfully exclude file');
$t->ok(!file_exists($folderToExclude), 'folderToExclude was not synchronized in the target site');
$t->ok(!file_exists($fileToExclude), 'fileToExclude was not synchronized in the target site');
$t->ok(!file_exists($fileToInclude), 'fileToInclude was not synchronized in the target site');
$t->ok(file_exists($fileToSync), 'fileToSync was synchronized in the target folder');
$t->ok(file_exists($otherFileToSync), 'otherFileToSync was synchronized in the target folder');

$fs->delete($fileToSync);
$fs->delete($otherFileToSync);

$t->ok($cmd->run(new nbCommandLineParser(), '--doit --exclude-from=' . $excludeFile . ' --include-from=' . $includeFile . ' ' . $commandLine), 'Command nbDirTransfer called successfully');
$t->ok(!file_exists($folderToExclude), 'folderToExclude was not synchronized in the target site');
$t->ok(!file_exists($fileToExclude), 'fileToExclude was not synchronized in the target site');
$t->ok(file_exists($fileToInclude), 'fileToInclude was synchronized in the target site');
$t->ok(file_exists($fileToSync), 'fileToSync was synchronized in the target folder');
$t->ok(file_exists($otherFileToSync), 'otherFileToSync was synchronized in the target folder');

$fs->delete($fileToSync);
$fs->delete($fileToInclude);
$fs->delete($otherFileToSync);

$commandLine = '--delete ' . '--config-file=config.yml';
$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs($dataDir);

$t->ok($cmd->run($parser, '--doit --exclude-from=' . $excludeFile . ' --include-from=' . $includeFile . ' ' . $commandLine), 'Command nbDirTransfer called successfully');
$t->ok(!file_exists($folderToExclude), 'folderToExclude was not synchronized in the target site');
$t->ok(!file_exists($fileToExclude), 'fileToExclude was not synchronized in the target site');
$t->ok(file_exists($fileToInclude), 'fileToInclude was synchronized in the target site');
$t->ok(file_exists($fileToSync), 'fileToSync was synchronized in the target folder');
$t->ok(file_exists($otherFileToSync), 'otherFileToSync was synchronized in the target folder');

$fs->delete($fileToSync);
$fs->delete($fileToInclude);
$fs->delete($otherFileToSync);
$fs->touch($targetDir . '/readme');
