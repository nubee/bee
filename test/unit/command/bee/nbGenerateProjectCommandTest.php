<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));
nbConfig::set('nb_plugin_dir', nbConfig::get('nb_test_plugin_dir'));
$configDir = nbConfig::get('nb_sandbox_dir') . '/.bee';
$beeYaml = $configDir . '/bee.yml';
$configYaml = $configDir . '/config.yml';

$t = new lime_test(7);

$cmd = new nbGenerateProjectCommand();
nbFileSystem::delete($beeYaml);
nbFileSystem::delete($configYaml);
nbFileSystem::rmdir($configDir);

$t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('nb_sandbox_dir')), 'Command nbGenerateProjectCommand called succefully');

$t->ok(file_exists($beeYaml), 'bee.yml added to the destination dir :' . $beeYaml);
$t->ok(file_exists($configYaml), 'config.yml added to the destination dir :' . $configYaml);

try {
  $t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('nb_sandbox_dir')), 'Command nbGenerateProjectCommand called succefully');
  $t->fail('Exception not thrown');
} catch (Exception $e) {
  $t->pass('Exception nbFileSystem::copy() thrown');
}

$t->ok($cmd->run(new nbCommandLineParser(), '--force '.nbConfig::get('nb_sandbox_dir')), 'Command nbGenerateProjectCommand called succefully');

$t->ok(file_exists($beeYaml), 'bee.yml added to the destination dir :' . $beeYaml);
$t->ok(file_exists($configYaml), 'config.yml added to the destination dir :' . $configYaml);
nbFileSystem::delete($beeYaml);
nbFileSystem::delete($configYaml);
nbFileSystem::rmdir($configDir);