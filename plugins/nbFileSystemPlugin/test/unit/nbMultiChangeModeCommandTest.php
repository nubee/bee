<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$listFile = $dataDir . '/filesystem-multi-change-mode.yml';

if(php_uname('s') == 'Linux') {
  $t = new lime_test(1);
  $cmd = new nbMultiChangeModeCommand();
  $commandLine = sprintf('%s --doit', $listFile);

  $t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Folder mode changed successfully');
}
else {
  $t = new lime_test(1);
  $cmd = new nbMultiChangeModeCommand();
  $commandLine = sprintf('%s', $listFile);

  $t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Folder mode changed successfully');
}
