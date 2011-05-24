<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbFileSystemPlugin'));
$t = new lime_test(0);
/*
$t = new lime_test(3);

$cmd = new nbDirTransferCommand();
$t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('nb_file_system_plugin_rsync_local_folder').' '.nbConfig::get('nb_file_system_plugin_rsync_remote_server').' '.nbConfig::get('nb_file_system_plugin_rsync_remote_user').' '.nbConfig::get('nb_file_system_plugin_rsync_remote_folder')));
$t->ok($cmd->run(new nbCommandLineParser(), '--doit '.nbConfig::get('nb_file_system_plugin_rsync_local_folder').' '.nbConfig::get('nb_file_system_plugin_rsync_remote_server').' '.nbConfig::get('nb_file_system_plugin_rsync_remote_user').' '.nbConfig::get('nb_file_system_plugin_rsync_remote_folder')));
$t->ok($cmd->run(new nbCommandLineParser(), '--doit --exclude-from='.nbConfig::get('nb_file_system_plugin_rsync_exclude_file').' '.nbConfig::get('nb_file_system_plugin_rsync_local_folder').' '.nbConfig::get('nb_file_system_plugin_rsync_remote_server').' '.nbConfig::get('nb_file_system_plugin_rsync_remote_user').' '.nbConfig::get('nb_file_system_plugin_rsync_remote_folder')));
*/