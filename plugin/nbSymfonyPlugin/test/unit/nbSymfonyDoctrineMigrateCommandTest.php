<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));
$version_too_high = nbConfig::get('nb_symfony_plugin_test_migrate_version_high')+1;
$t = new lime_test(3);
$cmd = new nbSymfonyDoctrineMigrateCommand();
$t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('nb_symfony_plugin_test_symfony_dir').' '.nbConfig::get('nb_symfony_plugin_test_migrate_version_low')),'Command SymfonyDoctrineMigrate called succefully');
$t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('nb_symfony_plugin_test_symfony_dir').' '.nbConfig::get('nb_symfony_plugin_test_migrate_version_high')),'Command SymfonyDoctrineMigrate called succefully');
try{
  $cmd->run(new nbCommandLineParser(), nbConfig::get('nb_symfony_plugin_test_symfony_dir').' '.$version_too_high);
  $t->fail('Migration too high: exception not threw');
}
catch(Exception $e){
  $t->pass('Migration too high');
}