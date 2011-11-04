<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(40);

$fooArgument = new nbArgument('foo');
$barOption = new nbOption('bar');
$command1 = new DummyCommand("foo");
$command2 = new DummyCommand("ns:bar", new nbArgumentSet(array($fooArgument)));
$command3 = new DummyCommand("ns2:bas", null, new nbOptionSet(array($barOption)));

$t->comment('nbCommandTest - Test constructor');
try {
  new DummyCommand('');
  $t->fail('command name can\'t be empty');
}
catch(InvalidArgumentException $e) {
  $t->pass('command name can\'t be empty');
}
try {
  new DummyCommand('ns:');
  $t->fail('command name can\'t be empty');
}
catch(InvalidArgumentException $e) {
  $t->pass('command name can\'t be empty');
}

$command = new DummyCommand('foo');
$t->is($command->getArguments()->count(), 0, '->__construct() returns a command with no arguments');
$t->is($command->getOptions()->count(), 0, '->__construct() returns a command with no options');

$t->comment('nbCommandTest - Test name');
$t->is($command1->getName(), "foo", '->getName() returns a command with name "foo"');
$t->is($command2->getName(), "bar", '->getName() returns a command with name "bar"');

$t->comment('nbCommandTest - Test namespace');
$t->is($command1->getNamespace(), "", '->getNamespace() returns an empty namespace');
$t->is($command2->getNamespace(), "ns", '->getNamespace() returns a namespace "ns"');

$t->comment('nbCommandTest - Test fullname');
$t->is($command1->getFullname(), "foo", '->getFullName() returns a name without ":"');
$t->is($command2->getFullname(), "ns:bar", '->__construct() returns "ns:bar"');

$t->comment('nbCommandTest - Test arguments');
$t->is($command1->getArguments()->count(), 0, '->getArguments() returns 0 arguments');
$t->is($command2->getArguments()->count(), 1, '->getArguments() returns 1 argument');
$t->is($command3->getArguments()->count(), 0, '->getArguments() returns 0 arguments');

$command = new DummyCommand();
$command->addArgument(new nbArgument('foo'));
$t->is($command->getArguments()->count(), 1, '->addArgument() added 1 argument');

$t->comment('nbCommandTest - Test options');
$t->is($command1->getOptions()->count(), 0, '->getOptions() returns 0 options');
$t->is($command2->getOptions()->count(), 0, '->getOptions() returns 0 options');
$t->is($command3->getOptions()->count(), 1, '->getOptions() returns 1 option');

$command = new DummyCommand();
$command->addOption(new nbOption('foo'));
$t->is($command->getOptions()->count(), 1, '->addOption() added 1 option');

//$t->comment('nbCommandTest - Test synopsys');
//$t->is($command1->getSynopsys(), 'bee foo', '->getSynopsys() is "bee foo"');
//$t->is($command2->getSynopsys(), 'bee ns:bar [foo]', '->getSynopsys() is "bee ns:bar [foo]"');

$t->comment('nbCommandTest - Test shortcut');
$t->is($command1->hasShortcut('f'), true, '->hasShortcut() is true with "f"');
$t->is($command1->hasShortcut(':f'), true, '->hasShortcut() is true with ":f"');
$t->is($command1->hasShortcut('b'), false, '->hasShortcut() is true with "b"');
$t->is($command1->hasShortcut(':b'), false, '->hasShortcut() is true with ":b"');
$t->is($command2->hasShortcut('n:b'), true, '->hasShortcut() is true with "n:b"');
$t->is($command2->hasShortcut('ns:b'), true, '->hasShortcut() is true with "ns:b"');
$t->is($command2->hasShortcut('b'), true, '->hasShortcut() is true with "b"');
$t->is($command2->hasShortcut(':b'), true, '->hasShortcut() is true with ":b"');
$t->is($command2->hasShortcut('ns:d'), false, '->hasShortcut() is true with "ns:d"');

$t->comment('nbCommandTest - Test brief description');
$command = new DummyCommand('foo');
$t->is($command->getBriefDescription(), '', '->getBriefDescrition() is ""');
$command->setBriefDescription('command brief description');
$t->is($command->getBriefDescription(), 'command brief description', '->getBriefDescrition() is "command brief description"');

$t->comment('nbCommandTest - Test detailed description');
$command = new DummyCommand('foo');
$t->is($command->getDescription(), '', '->getDescription() is ""');
$command->setDescription('command description');
$t->is($command->getDescription(), 'command description', '->getDescription() is "command description"');

$t->comment('nbCommandTest - Test aliases');
$command = new DummyCommand("foo");
$t->ok(!$command->hasAliases(), '->hasAliases() returns false');
$t->ok(!$command->hasAlias('f'), '->hasAlias() returns false');
$command->setAlias('f');
$t->ok($command->hasAliases(), '->hasAliases() returns true');
$t->ok($command->hasAlias('f'), '->hasAlias() returns true');

$command = new DummyCommand("foo");
$command->setAliases(array('f', 'fo'));
$t->ok($command->hasAliases(), '->setAliases() sets 2 alias');
$t->ok($command->hasAlias('f'), '->hasAlias() returns true');
$t->ok($command->hasAlias('fo'), '->hasAlias() returns true');

try {
  $command->setAlias('f');
  $t->fail('->setAlias() throws an InvalidArgumentException if command alias already defined');
}
catch(InvalidArgumentException $e) {
  $t->pass('->setAlias() throws an InvalidArgumentException if command alias already defined');
}

$t->comment('nbCommandTest - Test command without arguments');
try {
  $command = new DummyNoArgsCommand();
  $t->pass('->new doesn\'t throws exception');
} catch (Exception $e) {
  $t->fail('->new doesn\'t throws exception');
}

/*
$t->comment('nbCommandTest - Test options defined in nbConfig');
$optionOptional = new nbOption('foo', '', nbOption::PARAMETER_OPTIONAL, '', 'defaultvalue');
$optionRequired = new nbOption('bar', '', nbOption::PARAMETER_REQUIRED, '');
$optionOptionalArray = new nbOption('cos', '', nbOption::PARAMETER_OPTIONAL | nbOption::IS_ARRAY, array('defaultvalue1', 'defaultvalue2'));
$optionRequiredArray = new nbOption('fas', '', nbOption::PARAMETER_REQUIRED | nbOption::IS_ARRAY);
nbConfig::set('nb_commands_ns_name_foo', 'foovalue');
nbConfig::set('nb_commands_ns_name_bar', 'barvalue');
nbConfig::set('nb_commands_ns_name_cos', array('cosvalue1', 'cosvalue2'));
nbConfig::set('nb_commands_ns_name_fas', array('fasvalue1', 'fasvalue2'));
$command = new DummyCommand("ns:name", null, new nbOptionSet(array($optionOptional, $optionRequired, $optionOptionalArray, $optionRequiredArray)));
$command->run(new nbCommandLineParser(), '');
$t->is($command->getOption('foo'), 'foovalue', '->getOption() returns "foovalue" ');
$t->is($command->getOption('bar'), 'barvalue', '->getOption() returns "barvalue" ');
$t->is($command->getOption('cos'), array('cosvalue1', 'cosvalue2'), '->getOption() returns "[cosvalue1, cosvalue2]" ');
$t->is($command->getOption('fas'), array('fasvalue1', 'fasvalue2'), '->getOption() returns "[fasvalue1, fasvalue2]" ');
$command->run(new nbCommandLineParser(), '--foo=cmdfoovalue --bar=cmdbarvalue --cos=cmdcosvalue1 --cos=cmdcosvalue2 --fas=cmdfasvalue1 --fas=cmdfasvalue2');
$t->is($command->getOption('foo'), 'cmdfoovalue', '->getOption() returns "cmdfoovalue" ');
$t->is($command->getOption('bar'), 'cmdbarvalue', '->getOption() returns "cmdbarvalue" ');
$t->is($command->getOption('cos'), array('cmdcosvalue1', 'cmdcosvalue2'), '->getOption() returns "[cmdcosvalue1, cmdcosvalue2]" ');
$t->is($command->getOption('fas'), array('cmdfasvalue1', 'cmdfasvalue2'), '->getOption() returns "[cmdfasvalue1, cmdfasvalue2]" ');
*/