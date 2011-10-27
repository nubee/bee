<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

$dataDir             = nbConfig::get('nb_data_dir') . '/config';
$configFileOk        = $dataDir . '/config.ok.yml';
$configFileNoChild   = $dataDir . '/config.nochild.yml';
$configFileNotExists = $dataDir . '/config.notexists.yml';

$t = new lime_test(3);
$t->comment('ConfigurationPrinter');

$printer = new nbConfigurationPrinter();
$printer->addConfigurationFile($configFileOk);

try {
  $printer->addConfigurationFile($configFileNotExists);
  $t->fail('No config file to print exists');
}
catch(Exception $e) {
  $t->pass('No config file to print exists');
}

$text = '
app_required_child_field: value
app_required_field: text
app_param1: value4
app_dir1: test/data/system
app_file1: test/data/system/Class.java';

$t->is(removeCarriageReturn($printer->printAll()), removeCarriageReturn($text), 'Printed text is formatted correctly');


$printer = new nbConfigurationPrinter();
$printer->addConfigurationFile($configFileNoChild);

$text = '
app_required_child_field: <error>required</error>
app_required_field: value
app_param3: value3
app_dir1: <error>dir_exists</error>
app_file1: <error>file_exists</error>';

$errors = array(
  'app_required_child_field' => 'required',
  'app_dir1' => 'dir_exists',
  'app_file1' => 'file_exists'
);
$printer->addConfigurationErrors($errors);

$t->is(removeCarriageReturn($printer->printAll()), removeCarriageReturn($text), 'Printed text is formatted correctly with errors');

function removeCarriageReturn($text) {
  $text = str_replace("\r", '', $text);
  $text = str_replace("\n", '|', $text);
  
  return $text;
}