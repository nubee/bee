<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

$t = new lime_test(6);

$cmd = new nbGenerateCommandCommand();

$cmd->run(new nbCommandLineParser(), 'ns:cmd className');
$t->ok(file_exists(nbConfig::get('nb_command_dir') . '/ns/className.php'), 'Command create new CommandFile in command folder');

$cmd->run(new nbCommandLineParser(), '--force ns:cmd className');
$t->ok(file_exists(nbConfig::get('nb_command_dir') . '/ns/className.php'), 'Command can overwrite a file');

$cmd->run(new nbCommandLineParser(), 'ns2:cmd className');
$t->ok(file_exists(nbConfig::get('nb_command_dir') . '/ns2/className.php'), 'Command create new CommandFile in command folder');

$cmd->run(new nbCommandLineParser(), 'cmd className');
$t->ok(file_exists(nbConfig::get('nb_command_dir') . '/className.php'), 'Command can create default (non namespace) commands');

$cmd->run(new nbCommandLineParser(), '-f :cmd className');
$t->ok(file_exists(nbConfig::get('nb_command_dir') . '/className.php'), 'Command can create default (non namespace) commands');

nbFileSystem::mkdir(nbConfig::get('nb_command_dir').'/customFolder');

$cmd->run(new nbCommandLineParser(), '--directory='.nbConfig::get('nb_command_dir').'/customFolder :cmd className');
$t->ok(file_exists(nbConfig::get('nb_command_dir') . '/customFolder/className.php'), 'Command accept --directory option');

    //cleanDir(nbConfig::get('nb_command_dir'));
    //
    //function cleanDir($dir)
    //{
    //  $finder = nbFileFinder::create('any');
    //  $files = $finder->add('*')->remove('.')->remove('..')->in($dir);
    //  foreach($files as $file)
    //    if(is_dir($file))
    //      nbFileSystem::rmdir($file,true);
    //    else
    //      nbFileSystem::delete($file);
    //}
