<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$t = new lime_test(1);

$cmd = new nbSymfonyGoOfflineCommand();
$t->ok($cmd->run(new nbCommandLineParser(), sprintf('%s %s %s', $symfonyRootDir, $application, $environment)), 'Command runs succefully');
