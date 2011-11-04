<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$listFile = $dataDir . '/filesystem-multi-change-mode.yml';

if(php_uname('s') == 'Linux') {
  //test setup
  $testDir1 = $sandboxDir.'/list-item-1';
  $testDir2 = $sandboxDir.'/list-item-2';
  $dirname = '/dirname';
  $filename = '/filename';
  
  $fileSystem->mkdir($testDir1.$dirname, true);
  $fileSystem->touch($testDir1.$filename);
  $fileSystem->mkdir($testDir2.$dirname, true);
  $fileSystem->touch($testDir2.$filename);
  
  $t = new lime_test(7);
  $cmd = new nbMultiChangeModeCommand();
  nbConfig::set('test_dir_1', $testDir1);
  nbConfig::set('test_dir_2', $testDir2);
  nbConfig::set('dir_mode', '666');
  nbConfig::set('file_mode', '444');
  
  $commandLine = sprintf('%s --doit', $listFile);

  $t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Folder mode changed successfully');
  $t->is(fileperms($testDir1), '666');
  $t->is(fileperms($testDir1.$dirname), '666');
  $t->is(fileperms($testDir1.$filename), '444');
  $t->is(fileperms($testDir2), '666');
  $t->is(fileperms($testDir2.$dirname), '666');
  $t->is(fileperms($testDir2.$filename), '444');
  
  //tear down
  $fileSystem->rmdir($testDir1, true);
  $fileSystem->rmdir($testDir2, true);
}
else {
  $t = new lime_test(1);
  $cmd = new nbMultiChangeModeCommand();
  $commandLine = sprintf('%s', $listFile);

  $t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Folder mode changed successfully');
}
