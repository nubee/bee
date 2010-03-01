<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(27);

$fooArgument = new nbArgument('foo');
$barOption = new nbOption('bar');
$command1 = new DummyCommand("foo");
$command2 = new DummyCommand("ns:bar", new nbArgumentSet(array($fooArgument)));
$command3 = new DummyCommand("ns2:bas", null, new nbOptionSet(array($barOption)));

$t->comment('nbCommandTest - Test constructor');
try {
  new DummyCommand();
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

$t->comment('nbCommandTest - Test options');
$t->is($command1->getOptions()->count(), 0, '->getOptions() returns 0 options');
$t->is($command2->getOptions()->count(), 0, '->getOptions() returns 0 options');
$t->is($command3->getOptions()->count(), 1, '->getOptions() returns 1 option');

$t->comment('nbCommandTest - Test synopsys');
$t->is($command1->getSynopsys(), 'bee foo', '->getSynopsys() is "bee foo"');
$t->is($command2->getSynopsys(), 'bee ns:bar [foo]', '->getSynopsys() is "bee ns:bar [foo]"');

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
