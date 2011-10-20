<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$t = new lime_test(2);
$t->comment('Symfony Check Site');

$cmd = new nbSymfonyCheckSiteCommand();

$checkSite = nbConfig::get('test_check-website');

$commandLine =  $checkSite. ' 200';
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'SymfonyCheckSite returned successfully with 200');

$commandLine = $checkSite . ' 500';
$t->ok(!$cmd->run(new nbCommandLineParser(), $commandLine), 'SymfonyCheckSite did not return a 500');
