<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';
//require_once dirname(__FILE__) . '/../../../data/core/command/EmptyCommand.php';

$t = new lime_test();

nbConfig::set('nb_command_dir', 'test/data/core/command/empty');
nbConfig::set('nb_plugin_dir', 'test/data/core/command/empty');
nbConfig::set('proj_commands', array());

$t->comment('nbCommandLoaderTest - test ctor');
$commandLoader = new nbCommandLoader();
$t->is($commandLoader->getCommands()->count(), 0, '->getCommands() after ctor is empty');

$t->comment('nbCommandLoaderTest - load commands');
$commandLoader->loadCommands();
$t->is($commandLoader->getCommands()->count(), 0, '->loadCommands() has loaded 0 commands');
nbConfig::set('nb_command_dir', 'test/data/core/command');
$commandLoader->loadCommands();
$t->is($commandLoader->getCommands()->count(), 1, '->loadCommands() has loaded 1 commands');
$t->ok($commandLoader->getCommands()->hasCommand(EmptyCommand::Name()), '->loadCommands() has loaded 1 commands');

$t->comment('nbCommandLoaderTest - load command aliases');
nbConfig::set('proj_commands_namespace_aliascmd', EmptyCommand::Name());
$commandLoader->loadCommandAliases();
$t->ok($commandLoader->getCommands()->hasCommand('namespace:aliascmd'), '->loadCommandAliases() has loaded 1 alias');
nbConfig::set('nb_command_dir', 'test/data/core/command');
nbConfig::set('proj_commands_default_aliascmd1', EmptyCommand::Name());
$commandLoader = new nbCommandLoader();
$commandLoader->loadCommands();
$commandLoader->loadCommandAliases();
$t->ok($commandLoader->getCommands()->hasCommand(':aliascmd1'), '->loadCommandAliases() has loaded 1 alias with no namespace');
