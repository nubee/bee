<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(36);

$t->comment('nbOptionTest - Test constructor');
try {
  $option = new nbOption();
  $t->fail('Couldn\'t create option without name');
}
catch(InvalidArgumentException $e)
{
  $t->pass('Couldn\'t create option without name');
}

try {
  $option = new nbOption('n');
  $t->fail('Couldn\'t create option with name shorter than 3 letters');
}
catch(InvalidArgumentException $e)
{
  $t->pass('Couldn\'t create option with name shorter than 3 letters');
}

try {
  $option = new nbOption('foo', 'bar');
  $t->fail('Couldn\'t create option with shortcut longer than 1 letter');
}
catch(InvalidArgumentException $e)
{
  $t->pass('Couldn\'t create option with shortcut longer than 1 letter');
}

$t->comment('nbOptionTest - Test getters');

$option = new nbOption('foo');
$t->is($option->getName(), 'foo', '->getName() is "foo"');
$t->is($option->getShortcut(), null, 'shortcut is optional');
$t->is($option->getDescritpion(), '', 'default description is empty string');
$t->is($option->hasParameter(), false, 'default mode is "nbOption::PARAMETER_NONE"');
$t->is($option->hasShortcut(), false, '->hasShortcut() returns false if option has not shortcut');

$foo = new nbOption('foo', 'f', nbOption::PARAMETER_OPTIONAL, 'foo description');
$bar = new nbOption('bar', 'b', nbOption::PARAMETER_REQUIRED);
$t->is($foo->hasShortcut(), true, '->hasShortcut() returns true if option has shortcut');
$t->is($foo->getShortcut(), 'f', '->getShortcut() returns shortcut if present');
$t->is($foo->getDescritpion(), 'foo description', '->getDescritpion() returns the description');

$t->comment('nbOptiontest - test mode');

$t->is($foo->hasParameter(), true, '->hasParameter() returns true if mode is "nbOption::PARAMETER_OPTIONAL" or "nbOption::PARAMETER_REQUIRED"');
$t->is($foo->hasOptionalParameter(), true, '->hasOptionalParameter() returns true if mode is "nbOption::PARAMETER_OPTIONAL"');
$t->is($bar->hasRequiredParameter(), true, '->hasRequiredParameter() returns true if mode is "nbOption::PARAMETER_REQUIRED"');

try {
  $option = new nbOption('foo', 'f', 55);
  $t->fail('__construct() throws "InvalidArgumentexception" if mode is not valid');
}
catch(InvalidArgumentException $e) {
  $t->pass('__construct() throws "InvalidArgumentexception" if mode is not valid');
}

$t->comment('->getValue()');

try {
  $option = new nbOption('foo', '', nbOption::PARAMETER_NONE);
  $option->getValue();
  $t->fail('->getValue() throws exception if option has "nbOption::PARAMETER_NONE" mode');
}
catch(LogicException $e) {
  $t->pass('->getValue() throws LogicException if option has "nbOption::PARAMETER_NONE" mode');
}

try {
  $option = new nbOption('foo', '', nbOption::PARAMETER_REQUIRED);
  $option->getValue();
  $t->fail('->getValue() throws exception if option has "nbOption::PARAMETER_REQUIRED" mode and null value');
}
catch(LogicException $e) {
  $t->pass('->getValue() throws LogicException if option has "nbOption::PARAMETER_REQUIRED" mode and null value');
}

$option = new nbOption('foo', '', nbOption::PARAMETER_OPTIONAL);
$t->is($option->getValue(), null, '->getValue() returns "$value" if option has "nbOption::PARAMETER_OPTIONAL"');

$t->comment('->setValue()');
try {
  $option = new nbOption('foo', '', nbOption::PARAMETER_NONE);
  $option->setValue('a value');
  $t->fail('->setValue() throws exception if option has "nbOption::PARAMETER_NONE" mode');
}
catch(LogicException $e) {
  $t->pass('->setValue() throws LogicException if option has "nbOption::PARAMETER_NONE" mode');
}

$option = new nbOption('foo', '', nbOption::PARAMETER_REQUIRED);
$option->setValue('a value');
$t->is($option->getValue(), 'a value', '->setValue() sets correct value');

$t->comment('nbOptionTest - Test "default" value');
try {
  $foo = new nbOption('foo', 'f', nbOption::PARAMETER_NONE, 'foo description', 'defaultValue');
  $t->fail('"nbOption with mode "nbOption::PARAMETER_NONE" couldn\'t have default value');
}
catch(InvalidArgumentException $e) {
  $t->pass('"nbOption with mode "nbOption::PARAMETER_NONE" couldn\'t have default value');
}

$option = new nbOption('foo', 'f', nbOption::PARAMETER_OPTIONAL, 'foo description', 'defaultValue');
$t->is($option->getValue(), 'defaultValue', '"ctor" sets default value');
$option->setValue('a value');
$t->is($option->getValue(), 'a value', '->setValue() overwrites default value');

$foo = new nbOption('foo', 'f', nbOption::PARAMETER_OPTIONAL, 'foo description', 'defaultValue');

$t->comment('nbOptionTest - Test if an argument is an array');

$foo = new nbOption('foo', '', nbOption::IS_ARRAY);
$t->is($foo->hasParameter(), false, 'option with array without mode has no parameter');

$foo = new nbOption('foo', '', nbOption::PARAMETER_NONE | nbOption::IS_ARRAY);
$t->is($foo->hasParameter(), false, 'option can be array with no parameter');
$t->is($foo->isArray(), true, 'option can be array with no parameter');

$foo = new nbOption('foo', 'f', nbOption::PARAMETER_OPTIONAL | nbOption::IS_ARRAY, 'foo description');
$t->ok($foo->isArray(), '->isArray() returns true if the argument can be an array');

$foo = new nbOption('foo', 'f', nbOption::PARAMETER_OPTIONAL, 'foo description');
$t->ok(! $foo->isArray(), '->isArray() returns false if the argument cannot be an array');

$foo = new nbOption('foo', 'f', nbOption::PARAMETER_REQUIRED | nbOption::IS_ARRAY, 'foo description');
try {
  $foo->setValue('a value');
  $t->fail('->setValue() must receive an array if option mode is "nbOption::IS_ARRAY"');
}
catch(InvalidArgumentException $e) {
  $t->pass('->setValue() must receive an array if option mode is "nbOption::IS_ARRAY"');
}

$foo->setValue(array());
$t->is($foo->getValue(), array(), '->getValue() returns an empty array if option has IS_ARRAY mode');
$foo->setValue(array("val1", "val2", "val3"));
$t->is($foo->getValue(), array("val1", "val2", "val3"), '->setValue() append parameter to value if option has IS_ARRAY mode');

$t->comment('nbOptionTest - Test to string');
$Option = new nbOption('foo', '', nbOption::PARAMETER_NONE);
$t->is((string)$Option, '[--foo]', '->__toString() returns "[--foo]"');

$Option = new nbOption('foo', '', nbOption::PARAMETER_OPTIONAL);
$t->is((string)$Option, '[--foo=[FOO]]', '->__toString() returns "[--foo=[FOO]]"');

$Option = new nbOption('foo', '', nbOption::PARAMETER_REQUIRED);
$t->is((string)$Option, '[--foo=FOO]', '->__toString() returns "[--foo=FOO]"');

$Option = new nbOption('foo', '', nbOption::PARAMETER_NONE | nbOption::IS_ARRAY);
$t->is((string)$Option, '[--foo] ... [--foo]', '->__toString() returns "[--foo] ... [--foo]"');

$Option = new nbOption('foo', 'f', nbOption::PARAMETER_NONE);
$t->is((string)$Option, '[-f|--foo]', '->__toString() returns "[-f|--foo]"');
