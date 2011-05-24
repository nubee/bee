<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin','nbTarPlugin'));

$t = new lime_test(1);
$cmd = new nbSymfonyDiemDeployCommand();
echo $command_line =  dirname(__FILE__) . '/../data/testDeploy.yml';
$t->ok($cmd->run(new nbCommandLineParser(),$command_line),'Command SymfonyDeploy called succefully');
