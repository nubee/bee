<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));
nbConfig::set('nb_plugins_dir', nbConfig::get('nb_sandbox_dir'));

$pluginDir = nbConfig::get('nb_plugins_dir');

// Setup
$t = new lime_test(32);
$fs = nbFileSystem::getInstance();


$cmd = new nbGeneratePluginCommand();
$cmd->run(new nbCommandLineParser(), 'pluginName');
$t->ok(file_exists($pluginDir . '/pluginName'), 'Command create new pluginName directory in plugin folder');
$t->ok(file_exists($pluginDir . '/pluginName/command'), 'Command create new command directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/config'), 'Command create new config directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/lib'), 'Command create new lib directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/lib/vendor'), 'Command create new vendor directory in <pluginName>/lib folder');
$t->ok(file_exists($pluginDir . '/pluginName/test'), 'Command create new test directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/test/bootstrap'), 'Command create new bootstrap directory in <pluginName>/test folder');
$t->ok(file_exists($pluginDir . '/pluginName/test/data'), 'Command create new data directory in <pluginName>/test folder');
$t->ok(file_exists($pluginDir . '/pluginName/test/data/config'), 'Command create new config directory in <pluginName>/test/data folder');
$t->ok(file_exists($pluginDir . '/pluginName/test/unit'), 'Command create new unit directory in <pluginName/test> folder');
$fs->rmdir($pluginDir, true);

$fs->mkdir($pluginDir . '/pluginName/otherDir', true);
$t->ok(file_exists($pluginDir . '/pluginName/otherDir'), 'otherDir folder created');

$cmd->run(new nbCommandLineParser(), 'pluginName -f');
$t->ok(file_exists($pluginDir . '/pluginName'), 'Command create new pluginName directory in plugin folder');
$t->ok(file_exists($pluginDir . '/pluginName/command'), 'Command create new command directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/config'), 'Command create new config directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/lib'), 'Command create new lib directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/lib/vendor'), 'Command create new vendor directory in <pluginName>/lib folder');
$t->ok(file_exists($pluginDir . '/pluginName/test'), 'Command create new test directory in pluginName folder');
$t->ok(file_exists($pluginDir . '/pluginName/test/bootstrap'), 'Command create new bootstrap directory in <pluginName>/test folder');
$t->ok(file_exists($pluginDir . '/pluginName/test/data'), 'Command create new data directory in <pluginName>/test folder');
$t->ok(file_exists($pluginDir . '/pluginName/test/data/config'), 'Command create new config directory in <pluginName>/test/data folder');
$t->ok(file_exists($pluginDir . '/pluginName/test/unit'), 'Command create new unit directory in <pluginName/test> folder');
$t->ok(!file_exists($pluginDir . '/pluginName/otherDir'), 'otherDir folder removed');
$fs->rmdir($pluginDir, true);

$pluginOtherDir = nbConfig::get('nb_sandbox_dir') . '/pluginOtherDir';
$cmd->run(new nbCommandLineParser(), 'pluginName --directory=' . $pluginOtherDir);
$t->ok(file_exists($pluginOtherDir . '/pluginName'), 'Command create new pluginName directory in plugin folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/command'), 'Command create new command directory in pluginName folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/config'), 'Command create new config directory in pluginName folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/lib'), 'Command create new lib directory in pluginName folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/lib/vendor'), 'Command create new vendor directory in <pluginName>/lib folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/test'), 'Command create new test directory in pluginName folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/test/bootstrap'), 'Command create new bootstrap directory in <pluginName>/test folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/test/data'), 'Command create new data directory in <pluginName>/test folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/test/data/config'), 'Command create new config directory in <pluginName>/test/data folder');
$t->ok(file_exists($pluginOtherDir . '/pluginName/test/unit'), 'Command create new unit directory in <pluginName/test> folder');
$fs->rmdir($pluginOtherDir, true);
