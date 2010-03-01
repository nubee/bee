<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(30);

$foo = new nbOption('foo', 'f');
$bar = new nbOption('bar');
$bas = new nbOption('bas');

$requiredOption = new nbOption('require','r', nbOption::PARAMETER_REQUIRED);


$t->comment('nbOptionSetTest - Test default constructor');
$set = new nbOptionSet();
$t->is($set->count(), 0, '->__construct() has no options');
$t->is($set->countRequired(), 0, '->__construct() has no required options');
$t->is($set->getOptions(), array(), '->__construct() builds an empty set');
$t->is($set->hasOption('foo'), false, '->__construct() has no option "foo"');

$t->comment('nbOptionSetTest - Test constructor with options');
$set = new nbOptionSet(array($foo));
$t->is($set->count(), 1, '->__construct() has 1 option');
$t->is($set->countRequired(), 0, '->__contruct() has no required options');
$t->is($set->hasOption('foo'), true, '->__construct() built a set with option "foo"');

// ->addOptions()
$t->comment('nbOptionSetTest - Test add options array');
$set = new nbOptionSet();
$set->addOptions(array($foo, $bar));
$t->is($set->getOptions(), array('foo' => $foo, 'bar' => $bar), '->addOptions() added an array of options');
$set->addOptions(array($bas));
$t->is($set->getOptions(), array('foo' => $foo, 'bar' => $bar, 'bas' => $bas), '->addOptions() does not clear previous options');

// ->addOption()
$t->comment('nbOptionSetTest - Test add option');
$set = new nbOptionSet();
$set->addOption($foo);
$t->is($set->getOptions(), array('foo' => $foo), '->addOption() added option "foo"');

try {
  $set->addOption($foo);
  $t->fail('->addOption() throws an InvalidOptionException if an option with the same name is added');
}
catch(InvalidArgumentException $e) {
  $t->pass('->addOption() throws an InvalidOptionException if an option with the same name is added');
}

// ->getOption()
$t->comment('nbOptionSetTest - Test get option');
$set = new nbOptionSet(array($foo));
$t->is($set->getOption('foo'), $foo, '->getOption() returns an nbOption by its name');
try {
  $set->getOption('bar');
  $t->fail('->getOption() throws a RangeException exception if the option name does not exist');
}
catch (RangeException $e) {
  $t->pass('->getOption() throws a RangeException exception if the option name does not exist');
}

$t->is($set->getOption('f'), $foo, '->getOption() returns an nbOption by its shortcut');

// ->hasOption()
$t->comment('nbOptionSetTest - Test has option');
$set = new nbOptionSet(array($foo));
$t->is($set->hasOption('foo'), true, '->hasOption() returns true if an nbOption exists for the given name');
$t->is($set->hasOption('f'), true, '->hasOption() returns true if an nbOption exists for the given shortcut');
$t->is($set->hasOption('bar'), false, '->hasOption() returns false if an nbOption does not exists for the given name');

// ->countRequired()
$t->comment('nbOptionSetTest - Test option required count');
$set = new nbOptionSet(array($requiredOption));
$t->is($set->countRequired(), 1, '->countRequired() returns the number of required options');
$set->addOption($foo);
$t->is($set->countRequired(), 1, '->countRequired() returns the number of required options');

// ->count()
$t->comment('nbOptionSetTest - Test get option count');
$set = new nbOptionSet();
$set->addOption($foo);
$t->is($set->count(), 1, '->count() returns the number of options');
$set->addOption($bar);
$t->is($set->count(), 2, '->count() returns the number of options');

// ->getValues()
$t->comment('nbOptionSetTest - Test get values');
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
  'foo3' => array()), '->getValues() returns the default values for each option');

$set = new nbOptionSet();
$set->addOptions(array(
  new nbOption('foo4', '', nbOption::PARAMETER_OPTIONAL | nbOption::IS_ARRAY, '', array(1, 2)),
));
$t->is($set->getValues(), array('foo4' => array(1, 2)), '->getValues() return the default values for each option');

$set = new nbOptionSet(array($requiredOption));
try {
  $set->getValues();
  $t->fail('->getValues() throws a LogicException if a required option is not set');
}
catch(LogicException $e) {
  $t->pass('->getValues() throws a LogicException if a required option is not set');
}

$t->comment('nbOptionSet - Test shortcuts');
$set = new nbOptionSet(array(
  new nbOption('foo', 'f'),
  new nbOption('bar', 'b'),
  new nbOption('bas', '')
));
$t->is($set->hasShortcut('f'), true, '->hasShortcut() returns true for shortcut "f"');
$t->is($set->hasShortcut('u'), false, '->hasShortcut() returns false for shortcut "u"');
$t->isa_ok($set->getByShortcut('f'), 'nbOption', '->getByShortcut() returns a valid option for shortcut "f"');

try {
  $set->getByShortcut('u');
  $t->fail('->getByShortcut() throw a RangeException for shortcut "u"');
}
catch(RangeException $e) {
  $t->pass('->getByShortcut() throw a RangeException for shortcut "u"');
}

$t->comment('nbOptionSet - Test to string');
$set = new nbOptionSet();
$t->is((string)$set, '', '->__toString() returns ""');
$set = new nbOptionSet(array($foo));
$t->is((string)$set, ' [-f|--foo]', '->__toString() returns " [-f|--foo]"');
