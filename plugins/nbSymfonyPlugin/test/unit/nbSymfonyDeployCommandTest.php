<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$logDir = $symfonyRootDir . '/log';
$cacheDir = $symfonyRootDir . '/cache';

$t = new lime_test(1);
$t->comment('Symfony Deploy');

$cmd = new nbSymfonyDeployCommand();
$parser = new nbCommandLineParser();
$parser->setDefaultConfigurationDirs(array(dirname(__FILE__) . '/../data/config'));

//print_r($cmd->getDefaultConfigurationDirs());
//die;
//$commandLine = sprintf('--config-file=%s', '/plugins/nbSymfonyPlugin/test/data/config/symfony-plugin.yml');
$commandLine = '--config-file';
$t->ok($cmd->run($parser, $commandLine . ' --doit'), 'Symfony project deployed successfully');
//$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Symfony project deployed successfully');

$fileSystem->rmdir($logDir, true, true);
$fileSystem->rmdir($cacheDir, true, true);
//$fileSystem->rmdir(nbConfig::get('filesystem_dir-transfer_target-dir'), true);
$fileSystem->rmdir(nbConfig::get('archive_archive-dir_destination-dir'), true);

