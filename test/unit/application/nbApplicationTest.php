<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(14);

$fooArgument = new nbArgument('foo');
$barOption = new nbOption('bar');

$t->comment('nbApplicationTest - Test constructor');
$application = new DummyApplication();
$t->is($application->getName(), 'UNDEFINED', '->getName() is "UNDEFINED"');
$t->is($application->getVersion(), 'UNDEFINED', '->getVersion() is "UNDEFINED"');
$t->is($application->hasArguments(), true, '__construct() returns an application with an argument');
$t->is($application->hasOptions(), false, '__construct() returns an application without options');
$t->is($application->hasCommands(), false, '__construct() returns an application without commands');

$application = new DummyApplication(array($fooArgument));
$t->is($application->hasArguments(), true, '__construct() returns an application with an argument');
$t->is($application->getArguments()->count(), 2, '__construct() returns an application with 2 arguments');

$application = new DummyApplication(array(), array($barOption));
$t->is($application->hasOptions(), true, '__construct() returns an application with an option');
$t->is($application->getOptions()->count(), 1, '__construct() returns an application with an option');

$t->comment('ApplicationTest - Test run');
$application = new DummyApplication();
$foo = new DummyCommand('foo');
$bar = new DummyCommand('bar', new nbArgumentSet(array(new nbArgument('first', nbArgument::REQUIRED))));
$bas = new DummyCommand('bas', null, new nbOptionSet(array(new nbOption('first', 'f'))));

$application->setCommands(new nbCommandSet(array($foo, $bar, $bas)));
$application->run('foo');
$t->ok($foo->hasExecuted(), '->run() executes command "foo"');
$application->run('bar test');
$t->ok($bar->hasExecuted(), '->run() executes command "bar test"');
$t->is($bar->getArgument('first'), 'test', '->run() executes command "bar test"');
$application->run('bas -f');
$t->ok($bas->hasExecuted(), '->run() executes command "bas -f"');
$t->is($bas->getOption('first'), true, '->run() executes command "bas -f"');
