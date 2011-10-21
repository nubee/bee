<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_plugins_dir', nbConfig::get('nb_data_dir') . '/plugins');
nbConfig::set('nb_config_dir', nbConfig::get('nb_sandbox_dir') . '/config');

$configDir        = nbConfig::get('nb_config_dir');
$dataDir          = nbConfig::get('nb_data_dir') . '/config';
$pluginName       = 'FirstPlugin';
$compareFile1     = $dataDir . '/plugin.compare.yml';
$compareFile2     = $dataDir . '/command.compare.yml';
$destinationFile1 = $configDir . '/plugin.yml';
$destinationFile2 = $configDir . '/command.yml';
$pluginFile       = 'plugin.template.yml';
$commandFile      = 'command.template.yml';
$fs = nbFileSystem::getInstance();

// Support functions
function removeCarriageReturn($text) {
  return str_replace("\r", '', $text);
}

$t = new lime_test(3);
$t->comment('Configure Plugin');

$cmd = new nbConfigurePluginCommand();

$t->ok($cmd->run(new nbCommandLineParser(), $pluginName), 'Plugin configured');

$content1 = removeCarriageReturn(file_get_contents($destinationFile1));
$compare1 = removeCarriageReturn(file_get_contents($compareFile1));
$t->is($content1, $compare1, 'Generated plugin config file content is correct');

$content2 = removeCarriageReturn(file_get_contents($destinationFile2));
$compare2 = removeCarriageReturn(file_get_contents($compareFile2));
$t->is($content2, $compare2, 'Generated command config file content is correct');

$fs->rmdir($configDir, true);
