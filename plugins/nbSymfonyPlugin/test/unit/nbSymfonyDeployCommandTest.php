<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$logDir = $symfonyRootDir . '/log';
$cacheDir = $symfonyRootDir . '/cache';

$fileSystem->mkdir(nbConfig::get('archive_archive-dir_destination-dir'));

$t = new lime_test(2);
$cmd = new nbSymfonyDeployCommand();
$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(array(dirname(__FILE__) . '/../data/config'));

$t->comment('Symfony Deploy dry run');
$commandLine = '--config-file ';
$t->ok($cmd->run($parser, $commandLine), 'Symfony project deployed successfully');

$cmd = new nbSymfonyDeployCommand();
$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(array(dirname(__FILE__) . '/../data/config'));

$t->comment('Symfony Deploy');
$commandLine = '--config-file --doit';
$t->ok($cmd->run($parser, $commandLine), 'Symfony project deployed successfully');

$fileSystem->rmdir($logDir, true, true);
$fileSystem->rmdir($cacheDir, true, true);
$fileSystem->rmdir(nbConfig::get('archive_archive-dir_destination-dir'), true);
