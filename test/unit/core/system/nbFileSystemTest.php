<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(11);

$dataDir = dirname(__FILE__) . '/../../../data/system';
$sandboxDir = dirname(__FILE__).'/../../../sandbox';

$t->is(nbFileSystem::getFileName($dataDir . '/Class1.php'), 'Class1.php', '->getFileName() returns "Class1.php"');

$t->comment('nbFileSystemTest - Test mkdir');

nbFileSystem::mkdir($sandboxDir.'/dir');
$t->ok(is_dir($sandboxDir.'/dir'), 'mkdir() creates dir if it not exists');

try {
  nbFileSystem::mkdir($sandboxDir.'/dir');
  $t->fail(('nbFileSystem::mkdir() trows if directory already exists'));
}
catch( Exception $e) {
  $t->pass('nbFileSystem::mkdir() trows if directory already exists');
}

try {
  nbFileSystem::mkdir($sandboxDir.'/dir/sub1/sub2');
  $t->fail('nbFileSystem::mkdir() trows if parent directory doesn\'t exist');
}
catch( Exception $e) {
  $t->pass('nbFileSystem::mkdir() trows if parent directory doesn\'t exist');
}

nbFileSystem::mkdir($sandboxDir.'/dir/sub1/sub2', true);
$t->ok(file_exists($sandboxDir.'/dir/sub1/sub2'), 'nbFileSystem::mkdir() can create parent folders');


//nbFileSystem::mkdir($sandboxDir.'/dir', true); // ->crea dir in 'c:/tmp/dir' se tmp non esiste crea anche lei

$t->comment('nbFileSystemTest - Test rmdir');

nbFileSystem::mkdir($sandboxDir.'/dir2');
nbFileSystem::rmdir($sandboxDir.'/dir2');
$t->ok(! file_exists($sandboxDir.'/dir2'),'nbFileSystem::rmdir() removes directory');

nbFileSystem::mkdir($sandboxDir.'/dir2/sub',true);
try {
  nbFileSystem::rmdir($sandboxDir.'/dir2');
  $t->fail('nbFileSystem::rmdir() removes only empty folders');
}
catch(Exception $e) {
  $t->pass('nbFileSystem::rmdir() removes only empty folders');
}

//nbFileSystem::rmdir($sandboxDir.'/dir2',true);
//$t->ok(! file_exists($sandboxDir.'/dir2'),'nbFileSystem::rmdir() can remove folder recursively');

// ?????????????????????????????????????
//try {
//  nbFileSystem::rmdir($sandboxDir.'/dir3');
//  $t->fail('nbFileSystem::rmdir() throws if folder doesn\'t exist');
//}
//catch(Exception $e) {
//  $t->pass('nbFileSystem::rmdir() throws if folder doesn\'t exist');
//}

//nbFileSystem::touch($sandboxDir.'/file');
//try {
//  nbFileSystem::rmdir($sandboxDir.'/file');
//  $t->fail('nbFileSystem::rmdir() doesn\'t remove files');
//}
//catch(Exception $e) {
//  $t->pass('nbFileSystem::rmdir() doesn\'t remove files');
//}

$t->comment('nbFileSystemTest - Test touch');

nbFileSystem::touch($sandboxDir.'/file1');
$t->ok(file_exists($sandboxDir.'/file1'),'nbFileSystem::touch create empty file');

try {
  nbFileSystem::touch($sandboxDir.'/fake-folder/file1');
  $t->fail('nbFileSystem::touch trows if parent folder doesn\'t exist');
}
catch(Exception $e) {
  $t->pass('nbFileSystem::touch trows if parent folder doesn\'t exist');
}

$t->comment('nbFileSystemTest - Test Delete');

nbFileSystem::delete($sandboxDir.'/file1');
$t->ok(!file_exists($sandboxDir.'/file1'),'nbFileSystem::delite remove file');

try {
  nbFileSystem::delete($sandboxDir. '/dir');
  $t->fail('nbFileSystem::delete can\'t delete folder');
}
catch(Exception $e) {
  $t->pass('nbFileSystem::delete can\'t delete folder');
}

$t->comment('nbFileSystemTest - Test Copy');

//nbFileSystem::touch($sandboxDir.'file1');
//nbFileSystem::mkdir($sandboxDir.'/dir3');
//nbFileSystem::copy($sandboxDir.'file1', $sandboxDir.'dir'.'file1');
//$t->ok(file_exists($sandboxDir.'dir3'.'file1'),'nbFileSystem::copy copies file');
//$t->ok(file_exists($sandboxDir.'file1'),'nbFileSystem::delite doesn\'t remove file copied');
