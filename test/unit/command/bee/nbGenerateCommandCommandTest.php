<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

$commandDir = nbConfig::get('nb_command_dir');

$t = new lime_test(6);

$cmd = new nbGenerateCommandCommand();

$cmd->run(new nbCommandLineParser(), 'ns:cmd className');
$t->ok(file_exists($commandDir . '/ns/className.php'), 'Command create new CommandFile in command folder');

$cmd->run(new nbCommandLineParser(), '--force ns:cmd className');
$t->ok(file_exists($commandDir . '/ns/className.php'), 'Command can overwrite a file');

$cmd->run(new nbCommandLineParser(), 'ns2:cmd className');
$t->ok(file_exists($commandDir . '/ns2/className.php'), 'Command create new CommandFile in command folder');

$cmd->run(new nbCommandLineParser(), 'cmd className');
$t->ok(file_exists($commandDir . '/className.php'), 'Command can create default (non namespace) commands');

$cmd->run(new nbCommandLineParser(), '-f :cmd className');
$t->ok(file_exists($commandDir . '/className.php'), 'Command can create default (non namespace) commands');

nbFileSystem::getInstance()->mkdir($commandDir.'/customFolder');

$cmd->run(new nbCommandLineParser(), '--directory='.$commandDir.'/customFolder :cmd className');
$t->ok(file_exists($commandDir . '/customFolder/className.php'), 'Command accept --directory option');

// Tear down
// TODO clean up
nbFileSystem::getInstance()->rmdir($commandDir, true);