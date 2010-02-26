<?php

//require_once dirname(__FILE__) . '/../../bootstrap/unit.php';
require_once dirname(__FILE__) . '/../../../vendor/lime/lime.php';
require_once dirname(__FILE__) . '/../../../lib/core/argument/nbArgument.php';

$t = new lime_test(11);

$t->comment('nbArgument - Test required argument');
$arg = new nbArgument('command', nbArgument::REQUIRED, 'The command to execute');
$t->is($arg->getName(), 'command', '->getName() argument name is "command"');
$t->is($arg->isRequired(), true, '->isRequired() argument is required');
$t->is($arg->getHelp(), 'The command to execute', '->getHelp() argument description is "The command to execute"');
//$t->is($arg->getValue(),null,"Required Argument no setted returns null");

//$arg->setValue("list");
//$t->is($arg->getValue(), "list", "argument value is 'list'");

$t->comment('nbArgument - Test optional argument');
$arg = new nbArgument('command', nbArgument::OPTIONAL, 'The command to execute', 'help');
$t->is($arg->getName(), 'command', '->getName() argument name is "command"');
$t->is($arg->isRequired(), false, '->isRequired() argument is not required');
//$t->is($arg->getValue(), "help", "argument value is 'help'");
//$t->is($arg->toString(), "[command]", "argument toString is '[command]'");

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

$t->comment('nbArgument - Test default value (->__construct())');
$argument = new nbArgument('foo', nbArgument::OPTIONAL, 'help text', 'defaultvalue');
$t->is($argument->getDefault(), 'defaultvalue', '->getDefault() returns "defaultvalue"');

$t->comment('nbArgument - Test default value (->setDefault())');
$argument->setDefault('avalue');
$t->is($argument->getDefault(), 'avalue', '->getDefault() returns "avalue"');
