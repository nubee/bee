<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin', 'nbArchivePlugin', 'nbFileSystemPlugin'));

$logDir = $symfonyRootDir . '/log';
$cacheDir = $symfonyRootDir . '/cache';

$t = new lime_test(1);
$t->comment('Symfony Deploy');

$cmd = new nbSymfonyDeployCommand();
$commandLine = dirname(__FILE__) . '/../config/config.yml';
//$t->ok($cmd->run(new nbCommandLineParser(), $commandLine . ' --doit'), 'Symfony project deployed succefully');
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Symfony project deployed succefully');

//$fileSystem->rmdir($logDir, true);
//$fileSystem->rmdir($cacheDir, true);
//$fileSystem->rmdir(nbConfig::get('filesystem_dir-transfer_target-path') . '/httpdocs', true);
//$fileSystem->rmdir(nbConfig::get('archive_inflate-dir_archive-path'), true);

