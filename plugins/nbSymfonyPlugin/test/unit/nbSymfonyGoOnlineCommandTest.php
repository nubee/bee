<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$t = new lime_test(1);
$t->comment('Symfony Go Online');

$cmd = new nbSymfonyGoOnlineCommand();
$t->ok($cmd->run(new nbCommandLineParser(), sprintf('%s %s %s', $symfonyRootDir, $application, $environment)), 'Symfony project put online');
