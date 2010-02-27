<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(24);

$foo = new nbArgument('foo');
$bar = new nbArgument('bar');
$bas = new nbArgument('bas');
$arrayArgument = new nbArgument('array', nbArgument::IS_ARRAY);
$requiredArgument = new nbArgument('array', nbArgument::REQUIRED);

// ->__construct()
$t->comment('nbArgumentSetTest - Test default constructor');
$set = new nbArgumentSet();
$t->is($set->getArgumentCount(), 0, '->__construct() has no arguments');
$t->is($set->getArgumentRequiredCount(), 0, '->__construct() has no required arguments');
$t->is($set->getArguments(), array(), '->__construct() builds an empty set');
$t->is($set->hasArgument('foo'), false, '->__construct() has no argument "foo"');

$t->comment('nbArgumentSetTest - Test constructor with an argument');
$set = new nbArgumentSet(array($foo));
$t->is($set->getArgumentCount(), 1, '->__construct() has 1 argument');
$t->is($set->getArgumentRequiredCount(), 0, '->__contruct() has no required arguments');
$t->is($set->hasArgument('foo'), true, '->__construct() built a set with argument "foo"');

// ->addArguments()
$t->comment('nbArgumentSetTest - Test add arguments array');
$set = new nbArgumentSet();
$set->addArguments(array($foo, $bar));
$t->is($set->getArguments(), array('foo' => $foo, 'bar' => $bar), '->addArguments() added an array of arguments');
$set->addArguments(array($bas));
$t->is($set->getArguments(), array('foo' => $foo, 'bar' => $bar, 'bas' => $bas), '->addArguments() does not clear previous arguments');

// ->addArgument()
$t->comment('nbArgumentSetTest - Test add argument');
$set = new nbArgumentSet();
$set->addArgument($foo);
$t->is($set->getArguments(), array('foo' => $foo), '->addArgument() added argument "foo"');

try {
  $set->addArgument($foo);
  $t->fail('->addArgument() throws an InvalidArgumentException if an argument with the same name is added');
}
catch(InvalidArgumentException $e) {
  $t->pass('->addArgument() throws an InvalidArgumentException if an argument with the same name is added');
}

$set = new nbArgumentSet(array($arrayArgument));
try {
  $set->addArgument($foo);
  $t->fail('->addArgument() throws an InvalidArgumentException if there is an array parameter already registered');
}
catch (InvalidArgumentException $e) {
  $t->pass('->addArgument() throws an InvalidArgumentException if there is an array parameter already registered');
}

// cannot add a required argument after an optional one
$set = new nbArgumentSet(array($foo));
try {
  $set->addArgument($requiredArgument);
  $t->fail('->addArgument() throws an InvalidArgumentException if you try to add a required argument after an optional one');
}
catch (InvalidArgumentException $e) {
  $t->pass('->addArgument() throws an InvalidArgumentException if you try to add a required argument after an optional one');
}

// ->getArgument()
$t->comment('nbArgumentSetTest - Test get argument');
$set = new nbArgumentSet(array($foo));
$t->is($set->getArgument('foo'), $foo, '->getArgument() returns an nbArgument by its name');
try {
  $set->getArgument('bar');
  $t->fail('->getArgument() throws a RangeException exception if the argument name does not exist');
}
catch (RangeException $e) {
  $t->pass('->getArgument() throws a RangeException exception if the argument name does not exist');
}

// ->hasArgument()
$t->comment('nbArgumentSetTest - Test has argument');
$set = new nbArgumentSet(array($foo));
$t->is($set->hasArgument('foo'), true, '->hasArgument() returns true if an nbArgument exists for the given name');
$t->is($set->hasArgument('bar'), false, '->hasArgument() returns false if an nbArgument does not exists for the given name');

// ->getArgumentRequiredCount()
$t->comment('nbArgumentSetTest - Test argument required count');
$set = new nbArgumentSet(array($requiredArgument));
$t->is($set->getArgumentRequiredCount(), 1, '->getArgumentRequiredCount() returns the number of required arguments');
$set->addArgument($foo);
$t->is($set->getArgumentRequiredCount(), 1, '->getArgumentRequiredCount() returns the number of required arguments');

// ->getArgumentCount()
$t->comment('nbArgumentSetTest - Test get argument count');
$set = new nbArgumentSet();
$set->addArgument($foo);
$t->is($set->getArgumentCount(), 1, '->getArgumentCount() returns the number of arguments');
$set->addArgument($bar);
$t->is($set->getArgumentCount(), 2, '->getArgumentCount() returns the number of arguments');

// ->getValues()
$t->comment('nbArgumentSetTest - Test get values()');
$set = new nbArgumentSet();
$set->addArguments(array(
  new nbArgument('foo1', nbArgument::OPTIONAL),
  new nbArgument('foo2', nbArgument::OPTIONAL, '', 'default'),
  new nbArgument('foo3', nbArgument::OPTIONAL | nbArgument::IS_ARRAY)
));
$t->is($set->getValues(), array(
  'foo1' => null,
  'foo2' => 'default',
  'foo3' => array()), '->getValues() returns the default values for each argument');

$set = new nbArgumentSet();
$set->addArguments(array(
  new nbArgument('foo4', nbArgument::OPTIONAL | nbArgument::IS_ARRAY, '', array(1, 2)),
));
$t->is($set->getValues(), array('foo4' => array(1, 2)), '->getValues() return the default values for each argument');

$set = new nbArgumentSet(array($requiredArgument));
try {
  $set->getValues();
  $t->fail('->getValues() throws a LogicException if a required argument is not set');
}
catch(LogicException $e) {
  $t->pass('->getValues() throws a LogicException if a required argument is not set');
}