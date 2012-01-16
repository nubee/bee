<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';
nbConfig::set('project_type', 'symfony');
nbConfig::set('project_symfony_exec-path', dirname(__FILE__) . '/../data/stage-site/symfony');
nbConfig::set('project_symfony_test-enviroment', 'lime');

$symfonyExecPath = nbConfig::get('project_symfony_exec-path');
$symfonyTestEnviroment = nbConfig::get('project_symfony_test-enviroment');

$t = new lime_test(2);
$t->comment('Symfony Test All');

$cmd = new nbSymfonyTestAllCommand();
$t->ok($cmd->run(new nbCommandLineParser(), ''), 'Symfony project test all');

nbConfig::set('project_type', 'xxx');

$t->comment('Symfony Test All with wrong project type');
try {
  $cmd = new nbSymfonyTestAllCommand();
  $cmd->run(new nbCommandLineParser(), '');
  $t->fail('Exception for wrong project type not thrown');
} catch (Exception $e) {
  $t->pass('Exception for wrong project type');  
}