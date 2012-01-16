<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));
nbConfig::set('nb_plugins_dir', nbConfig::get('nb_test_plugins_dir'));

$configDir = nbConfig::get('nb_sandbox_dir') . '/.bee';
$beeYaml = $configDir . '/bee.yml';
$configYaml = $configDir . '/config.yml';
$projectType ='foo';

$t = new lime_test(17);

$cmd = new nbGenerateProjectCommand();
$t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('nb_sandbox_dir')), 'Command nbGenerateProjectCommand called successfully');

$t->ok(file_exists($beeYaml), 'bee.yml added to the destination dir :' . $beeYaml);
$t->ok(file_exists($configYaml), 'config.yml added to the destination dir :' . $configYaml);

try {
  $cmd->run(new nbCommandLineParser(), nbConfig::get('nb_sandbox_dir'));
  $t->fail('Exception not thrown');
}
catch(Exception $e) {
  $t->pass('Exception nbFileSystem::copy() thrown');
}

$t->ok($cmd->run(new nbCommandLineParser(), '--force ' . nbConfig::get('nb_sandbox_dir')), 'Command nbGenerateProjectCommand called successfully');

$t->ok(file_exists($beeYaml), 'bee.yml added to the destination dir :' . $beeYaml);
$t->ok(file_exists($configYaml), 'config.yml added to the destination dir :' . $configYaml);

// Tear down
$fs = nbFileSystem::getInstance();
$fs->delete($beeYaml);
$fs->delete($configYaml);
$fs->rmdir($configDir);

$t->ok($cmd->run(new nbCommandLineParser(), sprintf('--type=%s %s', $projectType,nbConfig::get('nb_sandbox_dir')), 'Command nbGenerateProjectCommand called successfully'));

$t->ok(file_exists($beeYaml), 'bee.yml added to the destination dir :' . $beeYaml);
$t->ok(file_exists($configYaml), 'config.yml added to the destination dir :' . $configYaml);

$configParser = sfYaml::load($beeYaml);
$t->is($configParser['project']['type'], $projectType,'project type '.$projectType.' set successfully');

// Tear down
$fs = nbFileSystem::getInstance();
$fs->delete($beeYaml);
$fs->delete($configYaml);
$fs->rmdir($configDir);

$projectType = 'symfony';
$defaultExecPath = nbConfig::get('symfony_defaults_exec-path');
$defaultTestEnviroment = nbConfig::get('symfony_defaults_test-enviroment');
$t->ok($cmd->run(new nbCommandLineParser(), sprintf('--type=%s %s', $projectType, nbConfig::get('nb_sandbox_dir')), 'Command nbGenerateProjectCommand called successfully'));

$t->ok(file_exists($beeYaml), 'bee.yml added to the destination dir :' . $beeYaml);
$t->ok(file_exists($configYaml), 'config.yml added to the destination dir :' . $configYaml);

$configParser = sfYaml::load($beeYaml);
$t->is($configParser['project']['type'], $projectType,'project type '.$projectType.' set successfully');
$t->is($configParser['project']['symfony']['exec-path'], $defaultExecPath,'default execution path '.$defaultExecPath.' set successfully');
$t->is($configParser['project']['symfony']['test-enviroment'], $defaultTestEnviroment,'default test enviroment '.$defaultTestEnviroment.' set successfully');

// Tear down
$fs = nbFileSystem::getInstance();
$fs->delete($beeYaml);
$fs->delete($configYaml);
$fs->rmdir($configDir);