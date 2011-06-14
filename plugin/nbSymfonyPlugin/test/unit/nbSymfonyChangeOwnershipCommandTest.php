<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));

$t = new lime_test(1);
$cmd = new nbSymfonyChangeOwnershipCommand();
echo $command_line =  nbConfig::get('symfony_project-deploy_site-dir').' '.nbConfig::get('symfony_project-deploy_site-user').' '.nbConfig::get('symfony_project-deploy_site-group');
$t->ok($cmd->run(new nbCommandLineParser(),$command_line),'Command SymfonyChangeOwnership called succefully');
