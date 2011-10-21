<?php
require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$version_too_high = nbConfig::get('test_doctrine-migrate_high')+1;
$t = new lime_test(0);
/*
$t = new lime_test(3);
$cmd = new nbSymfonyDoctrineMigrateCommand();
$t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('symfony_project-deploy_symfony-exe-path').' '.nbConfig::get('test_doctrine-migrate_version-low')),'Command SymfonyDoctrineMigrate called successfully');
$t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('symfony_project-deploy_symfony-exe-path').' '.nbConfig::get('test_doctrine-migrate_version-high')),'Command SymfonyDoctrineMigrate called successfully');
try{
  $cmd->run(new nbCommandLineParser(), nbConfig::get('nb_symfony_plugin_test_symfony_dir').' '.$version_too_high);
  $t->fail('Migration too high: exception not threw');
}
catch(Exception $e){
  $t->pass('Migration too high');
}
 * 
 */