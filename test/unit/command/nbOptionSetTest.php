<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(24);

$foo = new nbOption('foo','f');
$bar = new nbOption('bar');
$bas = new nbOption('bas');

$requiredOption = new nbOption('require','r', nbOption::PARAMETER_REQUIRED);


$t->comment('nbOptionSetTest - Test default constructor');
$set = new nbOptionSet();
$t->is($set->getOptionCount(), 0, '->__construct() has no Options');
$t->is($set->getOptionRequiredCount(), 0, '->__construct() has no required Options');
$t->is($set->getOptions(), array(), '->__construct() builds an empty set');
$t->is($set->hasOption('foo'), false, '->__construct() has no Option "foo"');

$t->comment('nbOptionSetTest - Test constructor with options');
$set = new nbOptionSet(array($foo));
$t->is($set->getOptionCount(), 1, '->__construct() has 1 Option');
$t->is($set->getOptionRequiredCount(), 0, '->__contruct() has no required Options');
$t->is($set->hasOption('foo'), true, '->__construct() built a set with Option "foo"');

// ->addOptions()
$t->comment('nbOptionSetTest - Test add Options array');
$set = new nbOptionSet();
$set->addOptions(array($foo, $bar));
$t->is($set->getOptions(), array('foo' => $foo, 'bar' => $bar), '->addOptions() added an array of Options');
$set->addOptions(array($bas));
$t->is($set->getOptions(), array('foo' => $foo, 'bar' => $bar, 'bas' => $bas), '->addOptions() does not clear previous Options');

// ->addOption()
$t->comment('nbOptionSetTest - Test add Option');
$set = new nbOptionSet();
$set->addOption($foo);
$t->is($set->getOptions(), array('foo' => $foo), '->addOption() added Option "foo"');

try {
  $set->addOption($foo);
  $t->fail('->addOption() throws an InvalidArgumentException if an Option with the same name is added');
}
catch(InvalidArgumentException $e) {
  $t->pass('->addOption() throws an InvalidArgumentException if an Option with the same name is added');
}

// ->getOption()
$t->comment('nbOptionSetTest - Test get Option');
$set = new nbOptionSet(array($foo));
$t->is($set->getOption('foo'), $foo, '->getOption() returns an nbOption by its name');
try {
  $set->getOption('bar');
  $t->fail('->getOption() throws a RangeException exception if the Option name does not exist');
}
catch (RangeException $e) {
  $t->pass('->getOption() throws a RangeException exception if the Option name does not exist');
}

$t->is($set->getOption('f'), $foo, '->getOption() returns an nbOption by its shortcut');

// ->hasOption()
$t->comment('nbOptionSetTest - Test has Option');
$set = new nbOptionSet(array($foo));
$t->is($set->hasOption('foo'), true, '->hasOption() returns true if an nbOption exists for the given name');
$t->is($set->hasOption('f'), true, '->hasOption() returns true if an nbOption exists for the given shortcut');
$t->is($set->hasOption('bar'), false, '->hasOption() returns false if an nbOption does not exists for the given name');

// ->getOptionRequiredCount()
$t->comment('nbOptionSetTest - Test Option required count');
$set = new nbOptionSet(array($requiredOption));
$t->is($set->getOptionRequiredCount(), 1, '->getOptionRequiredCount() returns the number of required Options');
$set->addOption($foo);
$t->is($set->getOptionRequiredCount(), 1, '->getOptionRequiredCount() returns the number of required Options');

// ->getOptionCount()
$t->comment('nbOptionSetTest - Test get Option count');
$set = new nbOptionSet();
$set->addOption($foo);
$t->is($set->getOptionCount(), 1, '->getOptionCount() returns the number of Options');
$set->addOption($bar);
$t->is($set->getOptionCount(), 2, '->getOptionCount() returns the number of Options');

// ->getValues()
$t->comment('nbOptionSetTest - Test get values()');
$set = new nbOptionSet();
$set->addOptions(array(
  new nbOption('foo', '', nbOption::PARAMETER_REQUIRED, '', 'req'),
  new nbOption('foo1', '', nbOption::PARAMETER_OPTIONAL),
  new nbOption('foo2', '', nbOption::PARAMETER_OPTIONAL, '', 'default'),
  new nbOption('foo3', '', nbOption::PARAMETER_OPTIONAL | nbOption::IS_ARRAY)
));
$t->is($set->getValues(), array(
  'foo' => 'req',
  'foo1' => null,
  'foo2' => 'default',
  'foo3' => array()), '->getValues() returns the default values for each Option');

$set = new nbOptionSet();
$set->addOptions(array(
  new nbOption('foo4', '', nbOption::PARAMETER_OPTIONAL | nbOption::IS_ARRAY, '', array(1, 2)),
));
$t->is($set->getValues(), array('foo4' => array(1, 2)), '->getValues() return the default values for each Option');

$set = new nbOptionSet(array($requiredOption));
try {
  $set->getValues();
  $t->fail('->getValues() throws a LogicException if a required Option is not set');
}
catch(LogicException $e) {
  $t->pass('->getValues() throws a LogicException if a required Option is not set');
}
