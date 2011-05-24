<?php
require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbTarPlugin'));
#nbFileSystem::delete(nbConfig::get('nb_tar_plugin_test_archive_path').'/'.nbConfig::get('nb_tar_plugin_test_target_dir').'.tgz');
$timestamp = date('YmdHi',  time());
$t = new lime_test(2);
$cmd = new nbTarInflateDirCommand();
$t->ok($cmd->run(new nbCommandLineParser(), nbConfig::get('nb_tar_plugin_test_target_path')." ".nbConfig::get('nb_tar_plugin_test_archive_path')." ".nbConfig::get('nb_tar_plugin_test_target_dir')), 'Command tar inflate a directory into a destination file');
$t->ok(file_exists(nbConfig::get('nb_tar_plugin_test_archive_path').'/'.nbConfig::get('nb_tar_plugin_test_target_dir').'-'.$timestamp.'.tgz'), 'verify that destination file exist');
