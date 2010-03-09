<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';
nbConfig::set('nb_command_dir', nbConfig::get('nb_sandbox_dir'));

$t = new lime_test(3);

$cmd = new nbGenerateCommandCommand();

//$t->comment('nbGenerateCommandCommand - Test get name');
//$t->is($cmd->getName(), 'generate:command', '->getName() is "generate:command"');

//$t->comment('nbGenerateCommandCommand - Test execute');
//try {
//  $cmd->execute(array(), array());
//  $t->fail('no code should be executed after throwing an exception');
//}
//catch (sfCommandArgumentsException $exc) {
//  $t->pass('exception caught successfully');
//}

//$t->ok($cmd->execute(array('ns', 'command_name', 'class_name'), array()));

$cmd->run(new nbCommandLineParser(), 'ns name1Command className');
$t->ok(file_exists(nbConfig::get('nb_command_dir'). '/ns/className.php'),'Command create new CommandFile in command folder');

$cmd->run(new nbCommandLineParser(), 'ns name1Command className');
$t->ok(file_exists(nbConfig::get('nb_command_dir'). '/ns/className.php'),'Command create new CommandFile in command folder');

$options = new nbOptionSet(array(new nbOption('file', 'f')));
$cmd->setOptions($options);

$cmd->run(new nbCommandLineParser(), 'ns name1Command className');
$t->ok(file_exists(nbConfig::get('nb_command_dir'). '/ns/className.php'),'Command create new CommandFile in command folder');
