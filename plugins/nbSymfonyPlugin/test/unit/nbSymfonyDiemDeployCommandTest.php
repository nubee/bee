<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$t = new lime_test(0);
/*
$fileSystem->mkdir(nbConfig::get('archive_inflate-dir_archive-path'));
$logFolder = $symfonyRootDir . '/log';
$cacheFolder = $symfonyRootDir . '/cache';

$t = new lime_test(1);
$t->comment('Symfony Diem Deploy');

$cmd = new nbSymfonyDiemDeployCommand();
$commandLine = dirname(__FILE__) . '/../data/config/config.yml';
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Diem project deploy successfully');

// tear down
$fileSystem->rmdir($logFolder, true);
$fileSystem->rmdir($cacheFolder, true);
$fileSystem->rmdir(nbConfig::get('filesystem_dir-transfer_target-path') . '/httpdocs', true);
$fileSystem->rmdir(nbConfig::get('archive_inflate-dir_archive-path'), true);
*/