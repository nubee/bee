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

$t = new lime_test(10);
$t->comment('Check Configuration');

$t->comment(' 1. Config file checks correctly');
$checker = new nbConfigurationChecker(array(
//  'verbose' => true,
//  'logger' => nbLogger::getInstance()
));

$t->ok($checker->check($templateFile, $configFileOk), 'Project configuration checked successfully');

$t->comment(' 2. Config file has errors (no child and no required field)');
try {
  $checker->check($templateFile, $configFileNoField);
  $t->fail('Config file without required field not checked successfully');
}
catch(Exception $e) {
  $t->pass('Config file without required field not checked successfully');
}

$t->ok($checker->hasErrors(), 'Config file has errors');
$t->is(count($checker->getErrors()), 2, 'Config file has 2 errors');

$errors = array(
  'app_required_field' => 'required',
  'app_required_child_field' => 'required'
);
$t->is($checker->getErrors(), $errors, 'Config file has errors formatted correctly');


$t->comment(' 3. Config file has errors (no child)');
try {
  $checker->check($templateFile, $configFileNoChild);
  $t->fail('Config file without required child not checked successfully');
}
catch(Exception $e) {
  $t->pass('Config file without required child not checked successfully');
}

$t->ok($checker->hasErrors(), 'Config file has errors');
$t->is(count($checker->getErrors()), 1, 'Config file has 1 error');

$t->comment(' 4. Config file checks if files exist');
try {
  $checker->check($templateFile, $configFileNotExists);
  $t->fail('No config file to check exists');
}
catch(Exception $e) {
  $t->pass('No config file to check exists');
}

try {
  $checker->check($configFileOk, $templateFileNotExists);
  $t->fail('No template file to check exists');
}
catch(Exception $e) {
  $t->pass('No template file to check exists');
}