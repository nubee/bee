<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

$dataDir = nbConfig::get('nb_data_dir') . '/config';
$sandboxDir = nbConfig::get('nb_sandbox_dir');
$compareFile = $dataDir . '/config.compare.yml';
$destinationFile = $sandboxDir . '/config.yml';
$templateFile = $dataDir . '/template.config.yml';
$templateFileNotExists = $dataDir . '/template.notexists.sample.yml';

$t = new lime_test(3);
$t->comment('Generate Configuration');

$cmd = new nbGenerateConfigurationCommand();
$fs = nbFileSystem::getInstance();

$t->ok($cmd->run(new nbCommandLineParser(), sprintf('%s %s', $templateFile, $destinationFile)), 'Project configuration generate succefully');

$content = removeCarriageReturn(file_get_contents($destinationFile));
$compare = removeCarriageReturn(file_get_contents($compareFile));
$t->is($content, $compare, 'Generated file content is correct');

$fs->delete($destinationFile);

try {
  $cmd->run(new nbCommandLineParser(), sprintf('%s %s', $templateFileNotExists, $destinationFile));
  $t->fail('No template file to check exists');
}
catch(Exception $e) {
  $t->pass('No template file to check exists');
}

function removeCarriageReturn($text) {
  return str_replace("\r", '', $text);
}