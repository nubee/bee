<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(9);

// Setup
// This folder must be outside the bee folder to avoid recursion
$installDir = nbConfig::get('nb_bee_dir') . '/../bee-sandbox/';

$t->comment('Bee Install Command');

$cmd = new nbBeeInstallCommand();

$t->comment(' 1. bee:install requires the destination folder');
try {
  $cmd->run(new nbCommandLineParser(), '');
  $t->fail('Command requires 1 argument');
}
catch(Exception $e) {
  $t->pass('Command requires 1 argument');
}

$t->comment(' 2. bee:install installs correctly on ' . $installDir);
$cmd->run(new nbCommandLineParser(), $installDir . ' -s ' . nbConfig::get('nb_bee_dir') . '/');

$t->ok(file_exists($installDir . '/config'), 'Command created config directory in installation folder');
$t->ok(file_exists($installDir . '/data'), 'Command created data directory in installation folder');
$t->ok(file_exists($installDir . '/docs'), 'Command created docs directory in installation folder');
$t->ok(file_exists($installDir . '/lib'), 'Command created lib directory in installation folder');
$t->ok(file_exists($installDir . '/plugin'), 'Command created plugin directory in installation folder');
$t->ok(file_exists($installDir . '/test'), 'Command created test directory in installation folder');
$t->ok(file_exists($installDir . '/bee'), 'Command created bee file in installation folder');
/*
if (PHP_OS == "Linux") {
  $t->ok(file_exists('/usr/bin/bee'), 'Command create symbolic link bee /usr/bin');
}
else if (PHP_OS == "WINNT") {

  $t->pass( "TODO: check symbolic link");
}
*/
/*
if (PHP_OS == "Linux") {
  $shell->execute( 'rm -rf '.$installDir);
  //$shell->execute( 'rm  /usr/bin/bee');
}
else if (PHP_OS == "WINNT") {
  $shell->execute( 'rd /S /Q '.$installDir);
}*/

// Tear down
nbFileSystem::getInstance()->rmdir($installDir, true);
$t->ok(!file_exists($installDir), 'Installation folder removed successfully');