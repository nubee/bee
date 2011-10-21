<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$t = new lime_test(1);
$t->comment('Symfony Clear Cache');

$cmd = new nbSymfonyClearCacheCommand();

$t->ok($cmd->run(new nbCommandLineParser(), $symfonyRootDir), 'Symfony cache cleared successfully');
