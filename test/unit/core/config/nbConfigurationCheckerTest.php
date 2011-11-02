<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

$dataDir = nbConfig::get('nb_data_dir') . '/config';
$sandboxDir = nbConfig::get('nb_sandbox_dir');
$configFileOk = $dataDir . '/config.ok.yml';
$configFileNoField = $dataDir . '/config.nofield.yml';
$configFileNoChild = $dataDir . '/config.nochild.yml';
$configFileNotExists = $dataDir . '/config.notexists.yml';
$dirNotExists = $dataDir . '/config.dirnotexists.yml';
$fileNotExists = $dataDir . '/config.filenotexists.yml';
$templateFile = $dataDir . '/template.config.yml';
$templateFileNotExists = $dataDir . '/template.notexists.sample.yml';

$t = new lime_test(16);
$t->comment('Check Configuration');

$t->comment(' 1. Config file checks correctly');
$checker = new nbConfigurationChecker(array(
  'verbose' => true,
  'logger' => nbLogger::getInstance()
));

$t->ok($checker->checkConfigFile($templateFile, $configFileOk), 'Project configuration checked successfully');

$t->comment(' 2. Config file has errors (no child and no required field)');
try {
  $checker->checkConfigFile($templateFile, $configFileNoField);
  $t->fail('Config file without required field not checked successfully');
}
catch(Exception $e) {
  $t->pass('Config file without required field not checked successfully');
}

$t->ok($checker->hasErrors(), 'Config file has errors');
$t->is(count($checker->getErrors()), 2, 'Config file has 2 errors');

$errors = array(
  'app_required_field' => 'required',
  'app_required_child_field' => 'required',
  // These are not errors, since fields are not required
//  'app_dir1' => 'dir_exists',
//  'app_file1' => 'file_exists'
);
$t->is($checker->getErrors(), $errors, 'Config file has errors formatted correctly');


$t->comment(' 3. Config file has errors (no child)');
try {
  $checker->checkConfigFile($templateFile, $configFileNoChild);
  $t->fail('Config file without required child not checked successfully');
}
catch(Exception $e) {
  $t->pass('Config file without required child not checked successfully');
}

$t->ok($checker->hasErrors(), 'Config file has errors');
$t->is(count($checker->getErrors()), 1, 'Config file has 1 errors');

$t->comment(' 4. Config file checks if files exist');
try {
  $checker->checkConfigFile($templateFile, $configFileNotExists);
  $t->fail('No config file to check exists');
}
catch(Exception $e) {
  $t->pass('No config file to check exists');
}

try {
  $checker->checkConfigFile($templateFileNotExists, $configFileOk);
  $t->fail('No template file to check exists');
}
catch(Exception $e) {
  $t->pass('No template file to check exists');
}

try {
  $checker->checkConfigFile($templateFile, $dirNotExists);
  $t->fail('No directory exists (and it should be)');
}
catch(Exception $e) {
  $t->pass('No directory exists (and it should be)');
}

try {
  $checker->checkConfigFile($templateFile, $fileNotExists);
  $t->fail('No file exists (and it should be)');
}
catch(Exception $e) {
  $t->pass('No file exists (and it should be)');
}


$t->comment(' 5. Check whole configuration');
nbConfig::set('app_required_field', 'value');
try {
  $checker->check($templateFile, nbConfig::getAll());
  $t->fail('Whole configuration without required fields not checked successfully');
}
catch(Exception $e) {
  $t->pass('Whole configuration without required fields not checked successfully');
}

$t->ok($checker->hasErrors(), 'Configuration has errors');
$t->is(count($checker->getErrors()), 1, 'Configuration has 1 error');

nbConfig::set('app_required_child_field', 'value');
$checker->check($templateFile, nbConfig::getAll());
$t->ok(!$checker->hasErrors(), 'Configuration has no errors');
