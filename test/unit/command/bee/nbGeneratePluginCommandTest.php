<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));
 nbConfig::set('nb_plugin_dir',nbConfig::get('nb_test_plugin_dir'));
$t = new lime_test(18);

$cmd = new nbGeneratePluginCommand();
$cmd->run(new nbCommandLineParser(), 'pluginName');
$t->ok(file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName'), 'Command create new pluginName directory in plugin folder');
$t->ok(file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName/command'), 'Command create new command directory in pluginName folder');
$t->ok(file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName/config'), 'Command create new config directory in pluginName folder');
$t->ok(file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName/lib'), 'Command create new lib directory in pluginName folder');
$t->ok(file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName/test'), 'Command create new test directory in pluginName folder');
$t->ok(file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName/vendor'), 'Command create new vendor directory in pluginName folder');

nbFileSystemUtils::mkdir(nbConfig::get('nb_test_plugin_dir').'/pluginName/otherDir');
$t->ok(file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName/otherDir'), 'otherDir folder created');
$cmd->run(new nbCommandLineParser(), 'pluginName -f');
$t->ok(file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName/command'), 'Command create new command directory in pluginName folder');
$t->ok(file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName/config'), 'Command create new config directory in pluginName folder');
$t->ok(file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName/lib'), 'Command create new lib directory in pluginName folder');
$t->ok(file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName/test'), 'Command create new test directory in pluginName folder');
$t->ok(file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName/vendor'), 'Command create new vendor directory in pluginName folder');
$t->ok(!file_exists(nbConfig::get('nb_test_plugin_dir').'/pluginName/otherDir'), 'otherDir folder removed');

$pluginOtherdir = nbConfig::get('nb_sandbox_dir').'/pluginOtherDir';
$cmd->run(new nbCommandLineParser(), 'pluginName --directory='.$pluginOtherdir);
$t->ok(file_exists($pluginOtherdir.'/pluginName/command'), 'Command create new command directory in pluginOtherDir/pluginName folder');
$t->ok(file_exists($pluginOtherdir.'/pluginName/config'), 'Command create new config directory in pluginOtherDir/pluginName folder');
$t->ok(file_exists($pluginOtherdir.'/pluginName/lib'), 'Command create new lib directory in pluginOtherDir/pluginName folder');
$t->ok(file_exists($pluginOtherdir.'/pluginName/test'), 'Command create new test directory in pluginOtherDir/pluginName folder');
$t->ok(file_exists($pluginOtherdir.'/pluginName/vendor'), 'Command create new vendor directory in pluginOtherDir/pluginName folder');

