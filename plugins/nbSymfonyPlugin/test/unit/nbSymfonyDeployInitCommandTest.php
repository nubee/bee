<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';
if (!checkMysql()) return true;
$logDir = $symfonyRootDir . '/log';
$cacheDir = $symfonyRootDir . '/cache';
$dbName = 'nbSymfonyPlugintest_dev';
$adminUsername = nbConfig::get('mysql_admin-username');
$adminPassword = nbConfig::get('mysql_admin-password');

$t = new lime_test(2);
//Setup
try {
  $cmd = new nbMysqlDropCommand();
  $commandLine = sprintf('%s %s %s', $dbName, $adminUsername, $adminPassword);
  $cmd->run(new nbCommandLineParser(), $commandLine);
} catch (Exception $e) {
  $t->comment('Drop database: ' . $e->getMessage());
}
$cmd = new nbSymfonyDeployInitCommand();
$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(array(dirname(__FILE__) . '/../data/config'));

$t->comment('Symfony Deploy dry run');
$commandLine = '--config-file ';
$t->ok($cmd->run($parser, $commandLine), 'Symfony project init deploy executed successfully');

$cmd = new nbSymfonyDeployInitCommand();
$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(array(dirname(__FILE__) . '/../data/config'));

$t->comment('Symfony Deploy');
$commandLine = '--config-file --doit';
$t->ok($cmd->run($parser, $commandLine), 'Symfony project deployed successfully');

//Tear Down
$fileSystem->rmdir($logDir, true, true);
$fileSystem->rmdir($cacheDir, true, true);
