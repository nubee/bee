<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

#nbFileSystem::rmdir(nbConfig::get('nb_test_installation_dir'), true);
$shell = new nbShell();
if (PHP_OS == "Linux") {
  nbConfig::set('nb_test_installation_dir', nbConfig::get('nb_test_linux_installation_dir'));
  $shell->execute( 'rm -rf '.nbConfig::get('nb_test_installation_dir'));
}
else if (PHP_OS == "WINNT") {
  nbConfig::set('nb_test_installation_dir', nbConfig::get('nb_test_win_installation_dir'));
  echo nbConfig::get('nb_test_installation_dir');
  $shell->execute( 'rd /S /Q '.nbConfig::get('nb_test_installation_dir'));
}

$t = new lime_test(8);
$cmd = new nbBeeInstallCommand();
$cmd->run(new nbCommandLineParser(), './ ' . nbConfig::get('nb_test_installation_dir'));
$t->ok(file_exists(nbConfig::get('nb_test_installation_dir') . '/config'), 'Command create config directory in installation folder');
$t->ok(file_exists(nbConfig::get('nb_test_installation_dir') . '/data'), 'Command create data directory in installation folder');
$t->ok(file_exists(nbConfig::get('nb_test_installation_dir') . '/documentation'), 'Command create documentatio directory in installation folder');
$t->ok(file_exists(nbConfig::get('nb_test_installation_dir') . '/lib'), 'Command create lib directory in installation folder');
$t->ok(file_exists(nbConfig::get('nb_test_installation_dir') . '/plugin'), 'Command create plugin directory in installation folder');
$t->ok(file_exists(nbConfig::get('nb_test_installation_dir') . '/test'), 'Command create test directory in installation folder');
$t->ok(file_exists(nbConfig::get('nb_test_installation_dir') . '/bee'), 'Command create bee file in installation folder');

if (PHP_OS == "Linux") {
  $t->ok(file_exists('/usr/bin/bee'), 'Command create symbolic link bee /usr/bin');
}
else if (PHP_OS == "WINNT") {

  $t->pass( "TODO: check symbolic link");
}

if (PHP_OS == "Linux") {
  $shell->execute( 'rm -rf '.nbConfig::get('nb_test_installation_dir'));
  $shell->execute( 'rm  /usr/bin/bee');
}
else if (PHP_OS == "WINNT") {
  $shell->execute( 'rd /S /Q '.nbConfig::get('nb_test_installation_dir'));
}
