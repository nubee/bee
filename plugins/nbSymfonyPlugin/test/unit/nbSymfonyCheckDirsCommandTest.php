<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$logDir =  $symfonyRootDir . '/log';
$cacheDir = $symfonyRootDir . '/cache';

$t = new lime_test(3);
$t->comment('Symfony Check Dirs');

$cmd = new nbSymfonyCheckDirsCommand();
$t->ok($cmd->run(new nbCommandLineParser(), $symfonyRootDir), 'Command SymfonyCheckDirs called successfully');
$t->ok(file_exists($logDir), 'Check log dir existence');
$t->ok(file_exists($cacheDir), 'Check cache dir existence');

$fileSystem->rmdir($logDir, true);
$fileSystem->rmdir($cacheDir, true);
