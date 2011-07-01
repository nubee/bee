<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

$projectDir = nbConfig::get('nb_sandbox_dir') ;
$configDir = nbConfig::get('nb_sandbox_dir') . '/.bee';
$beeYaml = $configDir . '/bee.yml';
$configYaml = $configDir . '/config.yml';
$nbFileSystemYaml = $configDir . '/nbFileSystemPlugin.yml';

$t = new lime_test(12);

$cmd = new nbGenerateProjectCommand();
nbFileSystem::delete($beeYaml);
nbFileSystem::delete($configYaml);
nbFileSystem::delete($nbFileSystemYaml);
nbFileSystem::rmdir($configDir);

$t->ok($cmd->run(new nbCommandLineParser(), $projectDir), 'Command nbGenerateProjectCommand called succefully');

$t->ok(file_exists($beeYaml), 'bee.yml added to the destination dir :' . $beeYaml);
$t->ok(file_exists($configYaml), 'config.yml added to the destination dir :' . $configYaml);

$pluginName = 'nbDummyPlugin';
$plugins = getPlugins($beeYaml);
$found = false;
foreach ($plugins as $plugin)
  if ($plugin == $pluginName)
    $found = true;

$t->ok(!$found, $pluginName . ' not found');
$t->comment('enabling nbDummyPlugin');

$cmd = new nbEnablePluginCommand();
$t->ok($cmd->run(new nbCommandLineParser(), $pluginName . ' ' . $projectDir), 'Command nbEnablePluginCommand called succefully');

$plugins = getPlugins($beeYaml);
$found = false;
foreach ($plugins as $plugin)
  if ($plugin == $pluginName)
    $found = true;
$t->ok($found, $pluginName . ' found');

$t->comment('enabling a fake plugin');
$cmd = new nbEnablePluginCommand();
try{
  $cmd->run(new nbCommandLineParser(), 'nbNonExistentPlugin' . ' ' . $projectDir);

  $t->fail('plugin nbNonExistentPlugin exists ?');
}
catch(Exception $e){
  $t->pass('plugin nbNonExistentPlugin not exists');
}

$otherPluginName = 'nbFileSystemPlugin';
$plugins = getPlugins($beeYaml);
$found = false;
foreach ($plugins as $plugin)
  if ($plugin == $otherPluginName)
    $found = true;

$t->ok(!$found, $otherPluginName . ' not found');
$t->comment('enabling '.$otherPluginName);

$cmd = new nbEnablePluginCommand();
$t->ok($cmd->run(new nbCommandLineParser(), $otherPluginName . ' ' . $projectDir), 'Command nbEnablePluginCommand called succefully');

$plugins = getPlugins($beeYaml);
$found = false;
foreach ($plugins as $plugin)
  if ($plugin == $pluginName)
    $found = true;
$t->ok($found, $pluginName . ' found');

$found = false;
foreach ($plugins as $plugin)
  if ($plugin == $otherPluginName)
    $found = true;
$t->ok($found, $otherPluginName . ' found');
$t->ok(file_exists($nbFileSystemYaml), 'nbFileSystemPlugin.yml added to the destination dir :' . $nbFileSystemYaml);

nbFileSystem::delete($beeYaml);
nbFileSystem::delete($configYaml);
nbFileSystem::delete($nbFileSystemYaml);
nbFileSystem::rmdir($configDir);

#####################################################################
function getPlugins($beeConfigurationFile) {
  if (file_exists($beeConfigurationFile))
    $configParser = sfYaml::load($beeConfigurationFile);
  else {
    throw new Exception('bee config file not found');
  }
  $plugins = array();
  if (isset($configParser['proj']['bee']['plugins_enabled'])) {
    $plugins = $configParser['proj']['bee']['plugins_enabled'];
  } else {
    throw new Exception('plugins_enabled key not found');
  }
  return $plugins;
}
