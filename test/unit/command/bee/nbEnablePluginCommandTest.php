<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

$projectDir       = nbConfig::get('nb_sandbox_dir') ;
$configDir        = nbConfig::get('nb_sandbox_dir') . '/.bee';
$beeConfig        = $configDir . '/bee.yml';
$config           = $configDir . '/config.yml';
$fileSystemConfig = $configDir . '/filesystem-plugin.yml';

function isPluginEnabled($beeConfig, $plugin) {
  if(!file_exists($beeConfig))
    throw new Exception('bee config file not found');
    
  $parser = sfYaml::load($beeConfig);
  
  $plugins = isset($parser['project']['bee']['enabled_plugins']) ? $parser['project']['bee']['enabled_plugins'] : array();
  
  return in_array($plugin, $plugins);
}

$t = new lime_test(12);

$cmd = new nbGenerateProjectCommand();
$t->ok($cmd->run(new nbCommandLineParser(), $projectDir), 'Command nbGenerateProjectCommand called successfully');

$t->ok(file_exists($beeConfig), 'bee.yml added to the destination dir :' . $beeConfig);
$t->ok(file_exists($config), 'config.yml added to the destination dir :' . $config);

$t->comment('enabling nbDummyPlugin');
$plugin = 'nbDummyPlugin';

$t->ok(!isPluginEnabled($beeConfig, $plugin), $plugin . ' not found');
$cmd = new nbEnablePluginCommand();
$t->ok($cmd->run(new nbCommandLineParser(), $plugin . ' ' . $projectDir), 'Plugin enabled successfully');
$t->ok(isPluginEnabled($beeConfig, $plugin), $plugin . ' found');

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
$secondPlugin = 'nbFileSystemPlugin';

$t->ok(!isPluginEnabled($beeConfig, $secondPlugin), $secondPlugin . ' not enabled');

$cmd = new nbEnablePluginCommand();
$t->ok($cmd->run(new nbCommandLineParser(), $secondPlugin . ' ' . $projectDir), 'Plugin ' . $secondPlugin . ' enabled');
$t->ok(isPluginEnabled($beeConfig, $plugin), $plugin . ' enabled');
$t->ok(isPluginEnabled($beeConfig, $secondPlugin), $secondPlugin . ' enabled');
$t->ok(file_exists($fileSystemConfig), 'configuration file added to the destination dir: ' . $fileSystemConfig);


// Tear down
$fs = nbFileSystem::getInstance();
$fs->delete($beeConfig);
$fs->delete($config);
$fs->delete($fileSystemConfig);
$fs->rmdir($configDir);

