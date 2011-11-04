<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$mode = '644';
$folder = nbConfig::get('nb_sandbox_dir') . '/folder';
$fs = nbFileSystem::getInstance();

if(php_uname('s') == 'Linux') {
  $fs->mkdir($folder);
  
  $t = new lime_test(1);
  $cmd = new nbChangeModeCommand();
  $commandLine = sprintf('%s %s', $folder, $mode);

  $t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Folder mode changed successfully');
  
  $fs->rmdir($folder);
}
else {
  $t = new lime_test(0);
  $t->comment('No tests under Windows');
}
