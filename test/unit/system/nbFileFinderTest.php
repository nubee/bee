<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$dataDir = dirname(__FILE__) . '/../../data/system';

$t = new lime_test(22);

$t->comment('nbFileFinder - Test create');

$finder = nbFileFinder::create();
$t->is($finder->getType(), 'file', '->create() returns a "file" finder.');

$finder = nbFileFinder::create('dir');
$t->is($finder->getType(), 'directory', '->create("dir") returns a "directory" finder.');

$finder = nbFileFinder::create('any');
$t->is($finder->getType(), 'any', '->create("any") returns an "any" finder.');

$finder = nbFileFinder::create('file');
$t->is($finder->getType(), 'file', '->create("file") returns an "file" finder.');

$t->comment('nbFileFinder - Test setType');

$finder = nbFileFinder::create();
$t->is($finder->setType('dir')->getType(), 'directory', '->setType("dir") returns a "directory" finder.');
$t->is($finder->setType('any')->getType(), 'any', '->setType("any") returns an "any" finder.');
$t->is($finder->setType('file')->getType(), 'file', '->setType("file") returns a "file" finder.');

$t->comment('nbFileFinder - Test add');

$finder = nbFileFinder::create('file');
$names = array('Class1.php', 'Class2.php', 'Class3.php');
$files = $finder->add('*.php')->in($dataDir);
$t->is(count($files), 3, '->add() found 3 files');
for($i = 0; $i != count($files); ++$i) {
  $t->is(nbFileSystem::getFileName($files[$i]), $names[$i], '->add() found ' . $names[$i]);
}

$t->comment('nbFileFinder - Test remove');

$finder = nbFileFinder::create('file');
$names = array('Class.java');
$files = $finder->remove('*.php')->in($dataDir);
$t->is(count($files), 1, '->remove() found 1 files');
for($i = 0; $i != count($files); ++$i) {
  $t->is(nbFileSystem::getFileName($files[$i]), $names[$i], '->remove("*.php") found ' . $names[$i]);
}

$t->comment('nbFileFinder - Test add and remove');

$finder = nbFileFinder::create('file');
$files = $finder->add('*.java')->remove('*.php')->in($dataDir);
$t->is(count($files), 1, '->add()->remove() found 1 file');

$t->comment('nbFileFinder - Test prune');

$finder = nbFileFinder::create('file');
$names = array('Class.java', 'Class1.php', 'Class2.php');
$files = $finder->prune('pruned')->in($dataDir);
$t->is(count($files), 3, '->prune() found 3 files');
for($i = 0; $i != count($files); ++$i) {
  $t->is(nbFileSystem::getFileName($files[$i]), $names[$i], '->prune() found ' . $names[$i]);
}

$t->comment('nbFileFinder - Test discard');

$finder = nbFileFinder::create('file');
$names = array('Class1.php', 'Class2.php', 'Class3.php');
$files = $finder->discard('Class.java')->in($dataDir);
$t->is(count($files), 3, '->discard() found 3 files');
for($i = 0; $i != count($files); ++$i) {
  $t->is(nbFileSystem::getFileName($files[$i]), $names[$i], '->discard() found ' . $names[$i]);
}

