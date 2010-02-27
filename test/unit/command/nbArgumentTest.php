<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(14);

$t->comment('nbArgumentTest - Test constructor');
$argument = new nbArgument('foo', nbArgument::OPTIONAL, 'The argument', 'default');
$t->is($argument->getName(), 'foo', '->getName() is "foo"');
$t->is($argument->getValue(), 'default', '->getValue() returns "default"');
$t->is($argument->getDescription(), 'The argument', '->getDescription() is "The argument"');

$t->comment('nbArgument - Test required argument');
$argument = new nbArgument('command', nbArgument::REQUIRED, 'The command to execute');
$t->is($argument->getName(), 'command', '->getName() argument name is "command"');
$t->is($argument->isRequired(), true, '->isRequired() argument is required');

try {
  $argument->getValue();
  $t->fail('->getValue() throws if required argument has not been set');
}
catch(LogicException $e) {
  $t->pass('->getValue() throws if required argument has not been set');
}

$argument->setValue("list");
$t->is($argument->getValue(), "list", "->getValue() returns 'list'");

$t->comment('nbArgument - Test optional argument');
$argument = new nbArgument('command', nbArgument::OPTIONAL, 'The command to execute', 'description');
$t->is($argument->getName(), 'command', '->getName() argument name is "command"');
$t->is($argument->isRequired(), false, '->isRequired() argument is not required');
//$t->is($argument->getValue(), "description", "argument value is 'description'");
//$t->is($argument->toString(), "[command]", "argument toString is '[command]'");

$t->comment('nbArgument - Test ctor');
try {
  $argument = new nbArgument('foo', 'ANOTHER_ONE');
  $t->fail('->__construct() throws an Exception if the mode is not valid');
} catch (Exception $e) {
  $t->pass('->__construct() throws an Exception if the mode is not valid');
}

$t->comment('nbArgument - Test if an argument is an array');
$argument = new nbArgument('foo', nbArgument::IS_ARRAY);
$t->ok($argument->isArray(), '->isArray() returns true if the argument can be an array');
$argument = new nbArgument('foo', nbArgument::OPTIONAL | nbArgument::IS_ARRAY);
$t->ok($argument->isArray(), '->isArray() returns true if the argument can be an array');
$argument = new nbArgument('foo', nbArgument::OPTIONAL);
$t->ok(!$argument->isArray(), '->isArray() returns false if the argument can not be an array');

$t->comment('nbArgument - Test default value (->setDefault())');
$argument->setValue('value');
$t->is($argument->getValue(), 'value', '->getValue() returns "value"');
