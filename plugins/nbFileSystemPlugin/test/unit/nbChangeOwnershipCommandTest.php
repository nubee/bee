<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$user = 'adam';
$group = 'adam';
$folder = nbConfig::get('nb_sandbox_dir') . '/folder';

if(php_uname('s') == 'Linux') {
  $t = new lime_test(1);
  $cmd = new nbChangeOwnershipCommand();
  $commandLine = sprintf('%s %s %s', $folder, $user, $group);

  $t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Folder ownership changed successfully');
}
else {
  $t = new lime_test(0);
  $t->comment('No tests under Windows');
}

