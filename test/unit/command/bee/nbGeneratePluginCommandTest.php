<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));
nbConfig::set('nb_plugin_dir', nbConfig::get('nb_test_plugin_dir'));

$pluginDir = nbConfig::get('nb_plugin_dir');

// Setup
$t = new lime_test(18);
$fs = nbFileSystem::getInstance();


$cmd = new nbGeneratePluginCommand();
$cmd->run(new nbCommandLineParser(), 'pluginName');
$t->ok(file_exists($pluginDir . '/pluginName'), 'Command create new pluginName directory in plugin folder');
$t->ok(file_exists($pluginDir . '/pluginName/command'), 'Command create new command directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/config'), 'Command create new config directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/lib'), 'Command create new lib directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/test'), 'Command create new test directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/vendor'), 'Command create new vendor directory in pluginName folder');
$fs->rmdir($pluginDir);

$fs->mkdir($pluginDir . '/pluginName/otherDir', true);
$t->ok(file_exists($pluginDir . '/pluginName/otherDir'), 'otherDir folder created');

$cmd->run(new nbCommandLineParser(), 'pluginName -f');
$t->ok(file_exists($pluginDir . '/pluginName/command'), 'Command create new command directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/config'), 'Command create new config directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/lib'), 'Command create new lib directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/test'), 'Command create new test directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/vendor'), 'Command create new vendor directory in pluginName folder');
$t->ok(!file_exists($pluginDir . '/pluginName/otherDir'), 'otherDir folder removed');
$fs->rmdir($pluginDir);

$pluginOtherDir = nbConfig::get('nb_sandbox_dir') . '/pluginOtherDir';
$cmd->run(new nbCommandLineParser(), 'pluginName --directory=' . $pluginOtherDir);
$t->ok(file_exists($pluginOtherDir . '/pluginName/command'), 'Command create new command directory in pluginOtherDir/pluginName folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/config'), 'Command create new config directory in pluginOtherDir/pluginName folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/lib'), 'Command create new lib directory in pluginOtherDir/pluginName folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/test'), 'Command create new test directory in pluginOtherDir/pluginName folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/vendor'), 'Command create new vendor directory in pluginOtherDir/pluginName folder');
$fs->rmdir($pluginOtherDir);
