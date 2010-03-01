<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(30);

$foo = new DummyCommand("foo");
$bar = new DummyCommand("ns:bar");
$bas = new DummyCommand("ns2:bas");

$t->comment('nbCommandSetTest - Test default constructor');
$set = new nbCommandSet();
$t->is($set->count(), 0, '->__construct() has no commands');
$t->is($set->getCommands(), array(), '->__construct() builds an empty set');
$t->is($set->hasCommand('foo'), false, '->__construct() has no command "foo"');

$t->comment('nbCommandSetTest - Test constructor with commands');
$set = new nbCommandSet(array($foo));
$t->is($set->count(), 1, '->__construct() has 1 command');
$t->is($set->hasCommand('foo'), true, '->__construct() built a set with command "foo"');
$t->is($set->hasCommand(':foo'), true, '->__construct() built a set with command ":foo"');
$t->isa_ok($set->getCommand('foo'), 'DummyCommand', '->__construct() built a set with command "foo"');
$t->isa_ok($set->getCommand(':foo'), 'DummyCommand', '->__construct() built a set with command ":foo"');

// ->addCommands()
$t->comment('nbCommandSetTest - Test add commands array');
$set = new nbCommandSet();
$set->addCommands(array($foo, $bar));
$t->is($set->getCommands(), array('foo' => $foo, 'ns:bar' => $bar), '->addCommands() added an array of commands');
$set->addCommands(array($bas));
$t->is($set->getCommands(), array('foo' => $foo, 'ns:bar' => $bar, 'ns2:bas' => $bas), '->addCommands() does not clear previous commands');

// ->addCommand()
$t->comment('nbCommandSetTest - Test add command');
$set = new nbCommandSet();
$set->addCommand($foo);
$t->is($set->getCommands(), array('foo' => $foo), '->addCommand() added command "foo"');

try {
  $set->addCommand($foo);
  $t->fail('->addCommand() throws an InvalidCommandException if an command with the same name is added');
}
catch(InvalidArgumentException $e) {
  $t->pass('->addCommand() throws an InvalidCommandException if an command with the same name is added');
}

// ->getCommand()
$t->comment('nbCommandSetTest - Test get command');
$set = new nbCommandSet(array($foo));
$t->is($set->getCommand('foo'), $foo, '->getCommand() returns an nbCommand by its name');
try {
  $set->getCommand('bar');
  $t->fail('->getCommand() throws a RangeException exception if the command name does not exist');
}
catch (RangeException $e) {
  $t->pass('->getCommand() throws a RangeException exception if the command name does not exist');
}

$t->is($set->getCommand('f'), $foo, '->getCommand() returns an nbCommand by its shortcut');

// ->hasCommand()
$t->comment('nbCommandSetTest - Test has command');
$set = new nbCommandSet(array($foo));
$t->is($set->hasCommand('foo'), true, '->hasCommand() returns true if an nbCommand exists with name "foo"');
$t->is($set->hasCommand('f'), true, '->hasCommand() returns true if an nbCommand exists with shortcut "f"');
$t->is($set->hasCommand('bar'), false, '->hasCommand() returns false if an nbCommand does not exists with shortcut "bar"');

// ->count()
$t->comment('nbCommandSetTest - Test get command count');
$set = new nbCommandSet();
$set->addCommand($foo);
$t->is($set->count(), 1, '->count() returns the number of commands');
$set->addCommand($bar);
$t->is($set->count(), 2, '->count() returns the number of commands');

$t->comment('nbCommandSet - Test shortcuts');
$set = new nbCommandSet(array(
  new DummyCommand('foo'),
  new DummyCommand('ns:bar'),
  new DummyCommand('ns:bas')
));
$t->is($set->hasCommand('f'), true, '->hasCommand() returns true for shortcut "f"');
$t->is($set->hasCommand(':f'), true, '->hasCommand() returns true for shortcut "f"');
$t->is($set->hasCommand('u'), false, '->hasCommand() returns false for shortcut "u"');
$t->is($set->hasCommand('b'), false, '->hasCommand() returns false for shortcut "b" because ambiguous');
$t->is($set->hasCommand('ns:b'), false, '->hasCommand() returns false for shortcut "ns:b" because ambiguous');
$t->is($set->hasCommand('ns:bar'), true, '->hasCommand() returns true for shortcut "ns:bar"');
$t->isa_ok($set->getCommand('f'), 'DummyCommand', '->getCommand() returns a valid command for shortcut "f"');

try {
  $set->getCommand('u');
  $t->fail('->getByShortcut() throw a RangeException for shortcut "u"');
}
catch(RangeException $e) {
  $t->pass('->getByShortcut() throw a RangeException for shortcut "u"');
}

try {
  $set->getCommand('b');
  $t->fail('->getByShortcut() throw a RangeException for shortcut "b" because ambiguous');
}
catch(LogicException $e) {
  $t->pass('->getByShortcut() throw a RangeException for shortcut "b" because ambiguous');
}

try {
  $set->getCommand('ns:ba');
  $t->fail('->getByShortcut() throw a RangeException for shortcut "ns:ba" because ambiguous');
}
catch(LogicException $e) {
  $t->pass('->getByShortcut() throw a RangeException for shortcut "ns:ba" because ambiguous');
}
