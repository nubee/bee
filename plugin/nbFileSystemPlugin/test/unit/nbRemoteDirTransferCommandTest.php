<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbFileSystemPlugin'));
$t = new lime_test(0);

/*
//require a server with ssh and you have to test it interactively for now
$t = new lime_test(7);

$cmd = new nbRemoteDirTransferCommand();
$commandLine = nbConfig::get('filesystem_remote-dir-transfer_source-path').' '.
               nbConfig::get('filesystem_remote-dir-transfer_remote-server').' '.
               nbConfig::get('filesystem_remote-dir-transfer_remote-user').' '.
               nbConfig::get('filesystem_remote-dir-transfer_remote-path');
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine));

$t->ok($cmd->run(new nbCommandLineParser(), '--doit '.$commandLine));

$commandLine = '--delete '.$commandLine;
$t->ok($cmd->run(new nbCommandLineParser(),'--doit '.$commandLine));

$commandLine = '--exclude-from='.nbConfig::get('filesystem_remote-dir-transfer_exclude-from').' '.$commandLine;
$t->ok($cmd->run(new nbCommandLineParser(), '--doit '.$commandLine));

$commandLine = '--include-from='.nbConfig::get('filesystem_remote-dir-transfer_include-from').' '.$commandLine;
$t->ok($cmd->run(new nbCommandLineParser(), '--doit '.$commandLine));

$commandLine = '--config-file='.dirname(__FILE__).'/../config/config.yml';
$t->ok($cmd->run(new nbCommandLineParser(), '--doit '.$commandLine));
$t->ok($cmd->run(new nbCommandLineParser(), '--doit --exclude-from='.nbConfig::get('filesystem_remote-dir-transfer_exclude-from').' '.$commandLine));
*/