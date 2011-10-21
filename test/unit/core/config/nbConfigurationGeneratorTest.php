<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

$dataDir         = nbConfig::get('nb_data_dir') . '/config';
$sandboxDir      = nbConfig::get('nb_sandbox_dir');
$compareFile     = $dataDir . '/config.compare.yml';
$destinationFile = $sandboxDir . '/config.yml';
$templateFile    = $dataDir . '/template.config.yml';
$templateFileNotExists = $dataDir . '/template.notexists.sample.yml';

$t = new lime_test(5);
$t->comment('Generate Configuration');

$fs = nbFileSystem::getInstance();
$generator = new nbConfigurationGenerator();

$t->ok($generator->generate($templateFile, $destinationFile), 'Configuration generated successfully');

$content = removeCarriageReturn(file_get_contents($destinationFile));
$compare = removeCarriageReturn(file_get_contents($compareFile));
$t->is($content, $compare, 'Generated file content is correct');

try {
  $generator->generate($templateFile, $destinationFile, false);
  $t->fail('Cannot overwrite file');
}
catch(Exception $e) {
  $t->pass('Cannot overwrite file');
}

$t->ok($generator->generate($templateFile, $destinationFile, true), 'Can overwrite file is "force" option set');
$fs->delete($destinationFile);

try {
  $generator->generate($templateFileNotExists, $destinationFile);
  $t->fail('No template file to check exists');
}
catch(Exception $e) {
  $t->pass('No template file to check exists');
}

function removeCarriageReturn($text) {
  return str_replace("\r", '', $text);
}