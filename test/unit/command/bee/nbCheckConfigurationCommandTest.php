<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

$dataDir = nbConfig::get('nb_data_dir') . '/config';
$sandboxDir = nbConfig::get('nb_sandbox_dir');
$configFileOk = $dataDir . '/config.ok.yml';
$configFileNoField = $dataDir . '/config.nofield.yml';
$configFileNoChild = $dataDir . '/config.nochild.yml';
$configFileNotExists = $dataDir . '/config.notexists.yml';
$templateFile = $dataDir . '/template.config.yml';
$templateFileNotExists = $dataDir . '/template.notexists.sample.yml';

$t = new lime_test(5);
$t->comment('Check Configuration');

$cmd = new nbCheckConfigurationCommand();

$t->ok($cmd->run(new nbCommandLineParser(), sprintf('%s %s', $templateFile, $configFileOk)), 'Project configuration checked successfully');

try {
  $cmd->run(new nbCommandLineParser(), sprintf('%s %s', $templateFile, $configFileNoField));
  $t->fail('Config file without required field not checked');
}
catch(Exception $e) {
  $t->pass('Config file without required field not checked');
}

try {
  $cmd->run(new nbCommandLineParser(), sprintf('%s %s', $templateFile, $configFileNoChild));
  $t->fail('Config file without required child not checked');
}
catch(Exception $e) {
  $t->pass('Config file without required child not checked');
}

try {
  $cmd->run(new nbCommandLineParser(), sprintf('%s %s', $templateFile, $configFileNotExists));
  $t->fail('No config file to check exists');
}
catch(Exception $e) {
  $t->pass('No config file to check exists');
}

try {
  $cmd->run(new nbCommandLineParser(), sprintf('%s %s', $configFileOk, $templateFileNotExists));
  $t->fail('No template file to check exists');
}
catch(Exception $e) {
  $t->pass('No template file to check exists');
}