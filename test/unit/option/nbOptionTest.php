<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(23);

$t->comment("nbOptionTest");

$t->comment("->getShortcut()");
$option = new nbOption('foo', 'f');
$t->is($option->getShortcut(), 'f', '->getShortcut() returns the shortcut');

$option = new nbOption('foo');
$t->is($option->getShortcut(), null, '->getShortcut() returns null if shortcut is not set');

$t->comment("->__constructor()");
try
{
  $option = new nbOption();
  $t->fail("Couldn't create Option without name");
}
catch (Exception $e)
{
    $t->pass("Couldn't create Option without name");
}

try
{
  $option = new nbOption('');
  $t->fail("Couldn't create Option with empty name");
}
catch (Exception $e)
{
    $t->pass("Couldn't create Option without name");
}

$t->comment("->hasOptionalParameter()");

$option = new nbOption('bar','b');
$t->is($option->hasOptionalParameter(), false, '->hasOptionalParameter() returns false if option hasn\'t parameters');
$t->is($option->hasRequiredParameter(), false, '->hasRequiredParameter() returns false if option hasn\'t parameters');

$option = new nbOption('foo','f', nbOption::PARAMETER_OPTIONAL);
$t->is($option->hasOptionalParameter(), true, '->hasOptionalParameter() returns true if option parameter is optional');

$t->comment("->hasRequiredParameter()");
$option = new nbOption('foo','f', nbOption::PARAMETER_REQUIRED);
$t->is($option->hasRequiredParameter(), true, '->hasRequiredParameter() returns true if option parameter is required');

$option = new nbOption('foo','f', nbOption::PARAMETER_NONE);
$t->is($option->hasRequiredParameter(), false, '->hasRequiredParameter() returns false if option parameter is not required');

try
{
  $option = new nbOption('foo','f', 27);
  $t->fail("__constructor() accept only defined modes");
}
catch(Exception $e)
{
  $t->pass("__constructor() accept only defined modes");
}

$t->comment("->isArray()");

$option = new nbOption('foo','f', nbOption::PARAMETER_OPTIONAL);
$t->is($option->isArray(), false, '->isArray() returns false if option parameter is not an array');

$option = new nbOption('foo','f', nbOption::PARAMETER_OPTIONAL | nbOption::IS_ARRAY);
$t->is($option->hasOptionalParameter(), true, '->hasOptionalParameter() returns true if option has optional array parameter');
$t->is($option->isArray(), true, '->isArray() returns true if option parameter is an array');

$option = new nbOption('foo','f', nbOption::PARAMETER_REQUIRED | nbOption::IS_ARRAY);
$t->is($option->hasRequiredParameter(), true, '->hasRequiredParameter() returns true if option has required array parameter');
$t->is($option->isArray(), true, '->isArray() returns true if option parameter is an array');

$option = new nbOption('foo','f', nbOption::PARAMETER_OPTIONAL);
$option->setValue("bar");
$t->is($option->getValue(), "bar", '->getValue() returns a value if it is set');

$option = new nbOption('foo','f', nbOption::PARAMETER_OPTIONAL);
$t->is($option->getValue(), null, '->getValue() returns a value if it is set');

$option = new nbOption('foo','f', nbOption::PARAMETER_OPTIONAL);
$option->setValue("bar car");
$t->is($option->getValue(), "bar car", '->getValue() returns a value if it is set');

$option = new nbOption('foo','f', nbOption::PARAMETER_OPTIONAL);
$t->is($option->getValue(), null, '->getValue() returns null if it is not set');

$option = new nbOption('foo','f', nbOption::PARAMETER_OPTIONAL, 'default');
$t->is($option->getValue(), 'default', '->getValue() returns a value if it is set');

try
{
  $option = new nbOption('foo','f', nbOption::PARAMETER_NONE, 'default');
  $t->fail("Can't pass default value for non optional parameter");
}
catch(Exception $e)
{
  $t->pass("Can't pass default value for non optional parameter");
}

try
{
  $option = new nbOption('foo','f', nbOption::PARAMETER_REQUIRED, 'default');
  $t->fail("Can't pass default value for non optional parameter");
}
catch(Exception $e)
{
  $t->pass("Can't pass default value for non optional parameter");
}
