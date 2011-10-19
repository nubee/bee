<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbFileSystemPlugin'));

$t = new lime_test(32);
$fileToSync = nbFileSystemUtils::sanitize_dir(nbConfig::get('filesystem_dir-transfer_target-path')) . '/' . nbConfig::get('filesystem_test_file-to-sync');
$folderToExclude = nbFileSystemUtils::sanitize_dir(nbConfig::get('filesystem_dir-transfer_target-path')) . '/' . nbConfig::get('filesystem_test_folder-to-exclude');
$fileToExclude = nbFileSystemUtils::sanitize_dir(nbConfig::get('filesystem_dir-transfer_target-path')) . '/' . nbConfig::get('filesystem_test_file-to-exclude');
$fileToInclude = nbFileSystemUtils::sanitize_dir(nbConfig::get('filesystem_dir-transfer_target-path')) . '/' . nbConfig::get('filesystem_test_file-to-include');
$otherFileToSync = nbFileSystemUtils::sanitize_dir(nbConfig::get('filesystem_dir-transfer_target-path')) . '/' . nbConfig::get('filesystem_test_other-file-to-sync');
$fileToDelete = nbFileSystemUtils::sanitize_dir(nbConfig::get('filesystem_dir-transfer_target-path')) . '/fileToDelete';

$excludeFile = nbConfig::get('filesystem_dir-transfer_exclude-from');
$includeFile = nbConfig::get('filesystem_dir-transfer_include-from');


$cmd = new nbDirTransferCommand();

nbFileSystem::delete($fileToSync);
nbFileSystem::rmdir($folderToExclude,true);
nbFileSystem::delete($fileToExclude);
nbFileSystem::delete($fileToInclude);
nbFileSystem::delete($otherFileToSync);

nbFileSystem::touch($fileToDelete);

$commandLine = '--delete ' . nbConfig::get('filesystem_dir-transfer_source-path') . ' ' . nbConfig::get('filesystem_dir-transfer_target-path');
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command nbDirTransfer called succefully dry run');
$t->ok(!file_exists($fileToSync), 'fileToSync wasn\'t syncronyzed in the target site because doit option was not set');
$t->ok(!file_exists($otherFileToSync), 'otherFileToSyn wasn\'t syncronyzed in the target folder');
$t->ok(!file_exists($folderToExclude), 'folderToExclude wasn\'t syncronyzed in the target folder');
$t->ok(!file_exists($fileToExclude), 'fileToExclude wasn\'t syncronyzed in the target folder');
$t->ok(!file_exists($fileToInclude), 'fileToInclude wasn\'t syncronyzed in the target folder');
$t->ok(file_exists($fileToDelete), 'fileToDelete wasn\'t deleted in the target folder');

$t->ok($cmd->run(new nbCommandLineParser(), '--doit ' . $commandLine), 'Command nbDirTransfer called succefully doit option set');
$t->ok(file_exists($fileToSync), 'fileToSync was syncronyzed in the target folder');
$t->ok(file_exists($otherFileToSync), 'otherFileToSync was syncronyzed in the target folder');
$t->ok(file_exists($folderToExclude), 'folderToExclude was syncronyzed in the target folder');
$t->ok(file_exists($fileToExclude), 'fileToEclude was syncronyzed in the target folder');
$t->ok(file_exists($fileToInclude), 'fileToInclude was syncronyzed in the target folder');
$t->ok(!file_exists($fileToDelete), 'fileToDelete was deleted in the target folder');

nbFileSystem::delete($fileToSync);
nbFileSystem::delete($folderToExclude.'/readme');
nbFileSystem::rmdir($folderToExclude);
nbFileSystem::delete($fileToExclude);
nbFileSystem::delete($fileToInclude);
nbFileSystem::delete($otherFileToSync);

$t->ok($cmd->run(new nbCommandLineParser(), '--doit --exclude-from=' . $excludeFile . ' ' . $commandLine), 'Command nbDirTransfer called succefully exclude file');
$t->ok(!file_exists($folderToExclude), 'folderToExclude was not syncronyzed in the target site');
$t->ok(!file_exists($fileToExclude), 'fileToExclude was not syncronyzed in the target site');
$t->ok(!file_exists($fileToInclude), 'fileToInclude was not syncronyzed in the target site');
$t->ok(file_exists($fileToSync), 'fileToSync was syncronyzed in the target folder');
$t->ok(file_exists($otherFileToSync), 'otherFileToSync was syncronyzed in the target folder');

nbFileSystem::delete($fileToSync);
nbFileSystem::rmdir($folderToExclude,true);
nbFileSystem::delete($fileToExclude);
nbFileSystem::delete($fileToInclude);
nbFileSystem::delete($otherFileToSync);

$t->ok($cmd->run(new nbCommandLineParser(), '--doit --exclude-from=' . $excludeFile . ' --include-from=' . $includeFile . ' ' . $commandLine), 'Command nbDirTransfer called succefully');
$t->ok(!file_exists($folderToExclude), 'folderToExclude was not syncronyzed in the target site');
$t->ok(!file_exists($fileToExclude), 'fileToExclude was not syncronyzed in the target site');
$t->ok(file_exists($fileToInclude), 'fileToInclude was syncronyzed in the target site');
$t->ok(file_exists($fileToSync), 'fileToSync was syncronyzed in the target folder');
$t->ok(file_exists($otherFileToSync), 'otherFileToSync was syncronyzed in the target folder');

nbFileSystem::delete($fileToSync);
nbFileSystem::rmdir($folderToExclude);
nbFileSystem::delete($fileToExclude);
nbFileSystem::delete($fileToInclude);
nbFileSystem::delete($otherFileToSync);

$commandLine = '--delete ' . '--config-file='.dirname(__FILE__).'/../config/config.yml';
$t->ok($cmd->run(new nbCommandLineParser(), '--doit --exclude-from=' . $excludeFile . ' --include-from=' . $includeFile . ' ' . $commandLine), 'Command nbDirTransfer called succefully');
$t->ok(!file_exists($folderToExclude), 'folderToExclude was not syncronyzed in the target site');
$t->ok(!file_exists($fileToExclude), 'fileToExclude was not syncronyzed in the target site');
$t->ok(file_exists($fileToInclude), 'fileToInclude was syncronyzed in the target site');
$t->ok(file_exists($fileToSync), 'fileToSync was syncronyzed in the target folder');
$t->ok(file_exists($otherFileToSync), 'otherFileToSync was syncronyzed in the target folder');

nbFileSystem::delete($fileToSync);
nbFileSystem::rmdir($folderToExclude);
nbFileSystem::delete($fileToExclude);
nbFileSystem::delete($fileToInclude);
nbFileSystem::delete($otherFileToSync);
nbFileSystem::touch(nbFileSystemUtils::sanitize_dir(nbConfig::get('filesystem_dir-transfer_target-path')).'/readme');
