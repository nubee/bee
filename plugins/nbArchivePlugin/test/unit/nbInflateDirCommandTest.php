<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbArchivePlugin'));
$timestamp = date('YmdHi',  time());
$t = new lime_test(4);
$cmd = new nbInflateDirCommand();
// parameter passed as arguments in command line
$fileTgz = nbConfig::get('archive_inflate-dir_archive-path').'/'.nbConfig::get('archive_inflate-dir_target-dir').'-'.$timestamp.'.tgz';
$commandLine = nbConfig::get('archive_inflate-dir_target-path').' '.nbConfig::get('archive_inflate-dir_target-dir').' '.nbConfig::get('archive_inflate-dir_archive-path');
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command archive:inflate-dir inflate a directory into a destination file');
$t->ok(file_exists($fileTgz), 'verify that destination file exist');
nbFileSystem::delete($fileTgz);

// parameter passed as config-file options
$timestamp = date('YmdHi',  time());
$fileTgz = nbConfig::get('archive_inflate-dir_archive-path').'/'.nbConfig::get('archive_inflate-dir_target-dir').'-'.$timestamp.'.tgz';
$commandLine = '--config-file='.dirname(__FILE__).'/../config/config.yml';
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command archive:inflate-dir inflate a directory into a destination file');
$t->ok(file_exists($fileTgz), 'verify that destination file exist');
nbFileSystem::delete($fileTgz);
