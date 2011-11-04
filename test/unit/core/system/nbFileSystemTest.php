<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(php_uname('s') == 'Linux' ? 35 : 31);

$dataDir = nbConfig::get('nb_data_dir') . '/system';
$sandboxDir = nbConfig::get('nb_sandbox_dir');

$fs = nbFileSystem::getInstance();

$t->comment('nbFileSystemTest - Test getFileName');

$t->is($fs->getFileName($dataDir . '/Class1.php'), 'Class1.php', '->getFileName() returns "Class1.php"');

$t->comment('nbFileSystemTest - Test mkdir');

$fs->mkdir($sandboxDir . '/dir', true);
$t->ok(is_dir($sandboxDir . '/dir'), 'mkdir() creates dir if it not exists');

try {
  $fs->mkdir($sandboxDir . '/dir');
  $t->pass(('$fs->mkdir() does not trow if directory already exists'));
}
catch(Exception $e) {
  $t->fail('$fs->mkdir() does not throw if directory already exists');
}

try {
  $fs->mkdir($dataDir . '/Class1.php');
  $t->fail('$fs->mkdir() throws if directory name already exists as file');
}
catch(Exception $e) {
  $t->pass('$fs->mkdir() throws if directory name already exists as file');
}

try {
  $fs->mkdir($sandboxDir . '/dir/sub1/sub2');
  $t->fail('$fs->mkdir() throws if parent directory doesn\'t exist');
}
catch(Exception $e) {
  $t->pass('$fs->mkdir() throws if parent directory doesn\'t exist');
}

$fs->mkdir($sandboxDir . '/dir/sub1/sub2', true);
$t->ok(file_exists($sandboxDir . '/dir/sub1/sub2'), '$fs->mkdir() can create parent folders');

////////////////////////////////////////
// Test rmdir
$t->comment('nbFileSystemTest - Test rmdir');

$fs->mkdir($sandboxDir . '/dir2');
$fs->rmdir($sandboxDir . '/dir2');
$t->ok(!file_exists($sandboxDir . '/dir2'), '$fs->rmdir() removes directory');

$fs->mkdir($sandboxDir . '/dir2/sub', true);
$fs->touch($sandboxDir . '/dir2/sub/file1');
$fs->touch($sandboxDir . '/dir2/sub/file2');
$fs->touch($sandboxDir . '/dir2/file1');
try {
  $fs->rmdir($sandboxDir . '/dir2');
  $t->fail('$fs->rmdir() removes only empty folders');
}
catch(Exception $e) {
  $t->pass('$fs->rmdir() removes only empty folders');
}

$fs->rmdir($sandboxDir . '/dir2', true);
$t->ok(!file_exists($sandboxDir . '/dir2'), '$fs->rmdir() can remove folder recursively');

try {
  $fs->rmdir($sandboxDir . '/dir3');
  $t->fail('$fs->rmdir() throws if folder doesn\'t exist');
}
catch(Exception $e) {
  $t->pass('$fs->rmdir() throws if folder doesn\'t exist');
}
$fs->touch($sandboxDir . '/file');
try {
  $fs->rmdir($sandboxDir . '/file');
  $t->fail('$fs->rmdir() doesn\'t remove files');
}
catch(Exception $e) {
  $t->pass('$fs->rmdir() doesn\'t remove files');
}

////////////////////////////////////////
// Test touch
$t->comment('nbFileSystemTest - Test touch');

$fs->touch($sandboxDir . '/file1');
$t->ok(file_exists($sandboxDir . '/file1'), '$fs->touch() create empty file');

try {
  $fs->touch($sandboxDir . '/fake-folder/file1');
  $t->fail('$fs->touch() throws if parent folder doesn\'t exist');
}
catch(Exception $e) {
  $t->pass('$fs->touch() throws if parent folder doesn\'t exist');
}

////////////////////////////////////////
// Test delete
$t->comment('nbFileSystemTest - Test Delete');

$fs->delete($sandboxDir . '/file1');
$t->ok(!file_exists($sandboxDir . '/file1'), '$fs->delete() remove file');

try {
  $fs->delete($sandboxDir . '/dir');
  $t->fail('$fs->delete() can\'t delete folder');
}
catch(Exception $e) {
  $t->pass('$fs->delete() can\'t delete folder');
}

////////////////////////////////////////
// Test copy
$t->comment('nbFileSystemTest - Test Copy');

try {
  $fs->copy($sandboxDir . '/fake-file', $sandboxDir . '/file1');
  $t->fail('$fs->copy() throws if source file doesn\'t exist');
}
catch(Exception $e) {
  $t->pass('$fs->copy() throws if source file doesn\'t exist');
}

$fs->touch($sandboxDir . '/file1');
$fs->touch($sandboxDir . '/file2');

try {
  $fs->copy($sandboxDir . '/file1', $sandboxDir . '/file2');
  $t->fail('$fs->copy() throws if destination file already exists');
}
catch(Exception $e) {
  $t->pass('$fs->copy() throws if destination file already exists');
}

try {
  $fs->copy($sandboxDir . '/file1', $sandboxDir . '/file2', true);
  $t->pass('$fs->copy() can overwrite destination file');
}
catch(Exception $e) {
  $t->fail('$fs->copy() can overwrite destination file');
}

$fs->delete($sandboxDir . '/file2');
$fs->copy($sandboxDir . '/file1', $sandboxDir . '/file2');
$t->ok(file_exists($sandboxDir . '/file1'), '$fs->copy() doesn\'t remove source file');
$t->ok(file_exists($sandboxDir . '/file2'), '$fs->copy() copies source file to destination file');

$fs->delete($sandboxDir . '/file2');
$fs->mkdir($sandboxDir . '/dir4');

$fs->copy($sandboxDir . '/file1', $sandboxDir . '/dir4');
$t->ok(file_exists($sandboxDir . '/dir4/file1'), '$fs->copy() copies source file in another folder maintaining the filename');

$fs->copy($sandboxDir . '/file1', $sandboxDir . '/dir_not_created/file1');
$t->ok(file_exists($sandboxDir . '/dir_not_created/file1'), '$fs->copy() copies source file in another file creating all the needed folders');

$fs->rmdir($sandboxDir, true, true);


////////////////////////////////////////
// Test move
$t->comment('nbFileSystemTest - Test Move');

$fs->mkdir($sandboxDir . '/dir/dir1', true);
$fs->touch($sandboxDir . '/dir/dir1/file1');
$fs->mkdir($sandboxDir . '/dir2');

$fs->move($sandboxDir . '/dir/dir1', $sandboxDir . '/dir2/dir1');

$t->ok(is_dir($sandboxDir . '/dir2/dir1'), '$fs->move() move folder to destination folder');
$t->ok(file_exists($sandboxDir . '/dir2/dir1/file1'), '$fs->move() move folder contents');
$t->ok(!file_exists($sandboxDir . '/dir/dir1'), '$fs->move() remove from old source the dir moved');
$t->ok(file_exists($sandboxDir . '/dir'), '$fs->move() doesn\'t remove parent folders');

$fs->rmdir($sandboxDir, true, true);

$fs->mkdir($sandboxDir . '/dir1', true);
$fs->mkdir($sandboxDir . '/dir', true);
$fs->touch($sandboxDir . '/dir1/file1');
$fs->move($sandboxDir . '/dir1/file1', $sandboxDir . '/dir/file1');

$t->ok(!file_exists($sandboxDir . '/dir1/file1'), '$fs->move() moves file');
$t->ok(file_exists($sandboxDir . '/dir/file1'), '$fs->move() moves file');

$fs->rmdir($sandboxDir . '/dir1', true);
try {
  $fs->move($sandboxDir . '/dir1', $sandboxDir . '/dir');
  $t->fail('$fs->move() throws if the source doesn\'t exist');
}
catch(Exception $e) {
  $t->pass('$fs->move() throws if the source doesn\'t exist');
}

$fs->mkdir($sandboxDir . '/dir1', true);
//$fs->rmdir($sandboxDir . '/dir2', true);

try {
  $fs->move($sandboxDir . '/dir1', $sandboxDir . '/dir2/dir');
  $t->fail('$fs->move() throws if the destination doesn\'t exist');
}
catch(Exception $e) {
  $t->pass('$fs->move() throws if the destination doesn\'t exist');
}

$fs->mkdir($sandboxDir . '/dir2');
$fs->move($sandboxDir . '/dir1', $sandboxDir . '/dir2/dir');
$t->ok(is_dir($sandboxDir . '/dir2' . '/dir'), '$fs->move() renames folder in "destination" if basename("destination") doesn\'t exist');

$fs->rmdir($sandboxDir, true, true);

// Works only on linux
if(php_uname('s') == 'Linux') {
  $t->comment('nbFileSystemTest - Test Chmod');

  $fs->rmdir($sandboxDir, true, true);

  $filename = $sandboxDir . '/file1';
  $fs->touch($filename);
  $perms = get_perms($filename);
  echo $fs->formatPermissions($filename);
  $t->ok($perms & 0x0080, 'User has write permission');  

  $fs->chmod($filename, 0440);
  $perms = get_perms($filename);
  echo $fs->formatPermissions($filename);
  $t->ok(!($perms & 0x0080), 'User has no write permission');

  $fs->chmod($filename, 0744);
  $perms = get_perms($filename);
  echo $fs->formatPermissions($filename);
  $t->ok($perms & 0x0080, 'User has write permission');

  $fs->rmdir($sandboxDir, true, true);
}

function get_perms($filename) {
   clearstatcache();
   return fileperms($filename);
}
