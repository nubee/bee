<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$t = new lime_test(1);
$t->comment('Symfony Check Permissions');

$cmd = new nbSymfonyCheckPermissionsCommand();

$t->ok($cmd->run(new nbCommandLineParser(), $symfonyRootDir), 'Command SymfonyCheckPermissions called successfully');
