<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(13);

$foo = new nbArgument('foo');
$command1 = new DummyCommand("dummy");
$command2 = new DummyCommand("ns:dummy", new nbArgumentSet(array($foo)));

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

$command = new DummyCommand('dummy');
$t->is($command->getArguments()->count(), 0, '->__construct() returns a command with no arguments');
//$t->is($command->getOptions()->count(), 00, '->__construct() returns a command with no options');

$t->comment('nbCommandTest - Test name');
$t->is($command1->getName(), "dummy", '->getName() returns a command with name "dummy"');
$t->is($command2->getName(), "dummy", '->getName() returns a command with name "dummy"');

$t->comment('nbCommandTest - Test namespace');
$t->is($command1->getNamespace(), "", '->getNamespace() returns an empty namespace');
$t->is($command2->getNamespace(), "ns", '->getNamespace() returns a namespace "ns"');

$t->comment('nbCommandTest - Test fullname');
$t->is($command1->getFullname(), "dummy", '->getFullName() returns a name without ":"');
$t->is($command2->getFullname(), "ns:dummy", '->__construct() returns "ns:dummy"');

$t->comment('nbCommandTest - Test arguments');
$t->is($command1->getArguments()->count(), 0, '->getArguments() returns 0 arguments');
$t->is($command2->getArguments()->count(), 1, '->getArguments() returns 1 argument');
//$t->is($command->getOptions()->count(), 00, '->__construct() returns a command with no options');

$t->comment('nbCommandTest - Test synopsys');
$t->is($command1->getSynopsys(), 'bee dummy', '->getSynopsys() is "bee dummy"');
$t->is($command2->getSynopsys(), 'bee ns:dummy [foo]', '->getSynopsys() is "bee ns:dummy [foo]"');
