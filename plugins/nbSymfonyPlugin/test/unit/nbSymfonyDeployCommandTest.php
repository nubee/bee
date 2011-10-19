<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin','nbArchivePlugin','nbFileSystemPlugin'));

if(!file_exists(nbConfig::get('archive_inflate-dir_archive-path'))){
  nbFileSystem::mkdir(nbConfig::get('archive_inflate-dir_archive-path'));
}
$logFolder = nbConfig::get('symfony_project-deploy_symfony-exe-path').'/log';
$cacheFolder = nbConfig::get('symfony_project-deploy_symfony-exe-path').'/cache';
nbFileSystem::rmdir($logFolder,true);
nbFileSystem::rmdir($cacheFolder, true);

$t = new lime_test(1);
$cmd = new nbSymfonyDeployCommand();
echo $command_line =  dirname(__FILE__) . '/../config/config.yml';
$t->ok($cmd->run(new nbCommandLineParser(),$command_line),'Command SymfonyDeploy called succefully');

nbFileSystem::rmdir($logFolder,true);
nbFileSystem::rmdir($cacheFolder, true);
nbFileSystem::rmdir(nbConfig::get('filesystem_dir-transfer_target-path').'/httpdocs',true);
nbFileSystem::rmdir(nbConfig::get('archive_inflate-dir_archive-path'),true);
if(!file_exists(nbConfig::get('archive_inflate-dir_archive-path'))){
  nbFileSystem::mkdir(nbConfig::get('archive_inflate-dir_archive-path'));
}

