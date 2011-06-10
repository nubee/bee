<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbFileSystemPlugin'));
$t = new lime_test(0);

/*
require a server with ssh and you have to test it interactively for now
$t = new lime_test(5);

$cmd = new nbDirTransferCommand();
$commandLine = nbConfig::get('filesystem_remote-dir-transfer_source-folder').' '.nbConfig::get('filesystem_remote-dir-transfer_remote-server').' '.nbConfig::get('filesystem_remote-dir-transfer_remote-user').' '.nbConfig::get('filesystem_remote-dir-transfer_remote-folder');
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine));
$t->ok($cmd->run(new nbCommandLineParser(), '--doit '.$commandLine));
$t->ok($cmd->run(new nbCommandLineParser(), '--doit --exclude-from='.nbConfig::get('filesystem_remote-dir-transfer_exclude-from').' '.$commandLine));
$commandLine = '--config-file='.dirname(__FILE__).'/../config/config.yml';
$t->ok($cmd->run(new nbCommandLineParser(), '--doit '.$commandLine));
$t->ok($cmd->run(new nbCommandLineParser(), '--doit --exclude-from='.nbConfig::get('filesystem_remote-dir-transfer_exclude-from').' '.$commandLine));
*/