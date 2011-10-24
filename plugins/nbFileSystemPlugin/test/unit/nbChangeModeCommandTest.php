<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$mode = '644';
$folder = nbConfig::get('nb_sandbox_dir') . '/folder';

if(php_uname('s') == 'Linux') {
  $t = new lime_test(1);
  $cmd = new nbChangeModeCommand();
  $commandLine = sprintf('%s %s', $folder, $mode);

  $t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Folder mode changed successfully');
}
else {
  $t = new lime_test(0);
  $t->comment('No tests under Windows');
}
