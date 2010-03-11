<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

$t = new lime_test(3);

$cmd = new nbGenerateCommandCommand();

$cmd->run(new nbCommandLineParser(), 'ns name1Command className');
$t->ok(file_exists(nbConfig::get('nb_command_dir'). '/ns/className.php'),'Command create new CommandFile in command folder');

$cmd->run(new nbCommandLineParser(), '--force ns name1Command className');
$t->ok(file_exists(nbConfig::get('nb_command_dir'). '/ns/className.php'),'Command can overwrite a file');

$cmd->run(new nbCommandLineParser(), 'ns2 name2Command class2Name');
$t->ok(file_exists(nbConfig::get('nb_command_dir'). '/ns2/class2Name.php'),'Command create new CommandFile in command folder');

cleanDir(nbConfig::get('nb_command_dir'));

function cleanDir($dir)
{
  $finder = nbFileFinder::create('any');
  $files = $finder->add('*')->remove('.')->remove('..')->in($dir);
  foreach($files as $file)
    if(is_dir($file))
      nbFileSystem::rmdir($file,true);
    else
      nbFileSystem::delete($file);
}