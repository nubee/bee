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

$t->comment('enabling nbDummyPlugin');
$pluginName = 'nbDummyPlugin';

$t->ok(!isPluginEnabled($beeYaml, $pluginName), $pluginName . ' not found');

$cmd = new nbEnablePluginCommand();
$t->ok($cmd->run(new nbCommandLineParser(), $pluginName . ' ' . $projectDir), 'Command nbEnablePluginCommand called succefully');

$t->ok(isPluginEnabled($beeYaml, $pluginName), $pluginName . ' found');

$t->comment('enabling a fake plugin');
$cmd = new nbEnablePluginCommand();
try{
  $cmd->run(new nbCommandLineParser(), 'nbNonExistentPlugin' . ' ' . $projectDir);

  $t->fail('plugin nbNonExistentPlugin exists ?');
}
catch(Exception $e){
  $t->pass('plugin nbNonExistentPlugin not exists');
}


$t->comment('enabling nbFileSystemPlugin');
$otherPluginName = 'nbFileSystemPlugin';

$t->ok(!isPluginEnabled($beeYaml, $otherPluginName), $otherPluginName . ' not found');

$cmd = new nbEnablePluginCommand();
$t->ok($cmd->run(new nbCommandLineParser(), $otherPluginName . ' ' . $projectDir), 'Command nbEnablePluginCommand called succefully');

$t->ok(isPluginEnabled($beeYaml, $pluginName), $pluginName . ' found');
$t->ok(isPluginEnabled($beeYaml, $otherPluginName), $otherPluginName . ' found');
$t->ok(file_exists($nbFileSystemYaml), 'nbFileSystemPlugin.yml added to the destination dir :' . $nbFileSystemYaml);

nbFileSystem::delete($beeYaml);
nbFileSystem::delete($configYaml);
nbFileSystem::delete($nbFileSystemYaml);
nbFileSystem::rmdir($configDir);

#####################################################################
function isPluginEnabled($beeConfigurationFile, $plugin) {
  if (file_exists($beeConfigurationFile))
    $configParser = sfYaml::load($beeConfigurationFile);
  else {
    throw new Exception('bee config file not found');
  }
  $plugins = array();
  if (isset($configParser['proj']['bee']['plugins_enabled'])) {
    $plugins = $configParser['proj']['bee']['plugins_enabled'];
  }
  
  return in_array($plugin, $plugins);
}
