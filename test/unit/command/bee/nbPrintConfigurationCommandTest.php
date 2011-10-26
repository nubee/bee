<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

$dataDir             = nbConfig::get('nb_data_dir') . '/config';
$configFileOk        = $dataDir . '/config.ok.yml';
$configFileNotExists = $dataDir . '/config.notexists.yml';

$t = new lime_test(2);
$t->comment('Print Configuration');

$cmd = new nbPrintConfigurationCommand();
$commandLine = '--filename=' . $configFileOk;

$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Project configuration printed successfully');

try {
  $commandLine = '--filename=' . $configFileNotExists;
  $cmd->run(new nbCommandLineParser(), $commandLine);
  $t->fail('No config file to print exists');
}
catch(Exception $e) {
  $t->pass('No config file to print exists');
}
