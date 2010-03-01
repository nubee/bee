<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(6);

$fooArgument = new nbArgument('foo');
$barOption = new nbOption('bar');

$t->comment('nbApplicationTest - Test constructor');
$application = new nbApplication();
$t->is($application->hasArguments(), false, '__construct() returns an application without arguments');
$t->is($application->hasOptions(), false, '__construct() returns an application without options');
$t->is($application->hasCommands(), false, '__construct() returns an application without commands');

$application = new nbApplication(new nbArgumentSet(array($fooArgument)));
$t->is($application->hasArguments(), true, '__construct() returns an application with an argument');
$t->is($application->getArguments()->count(), 1, '__construct() returns an application with an argument');

$application = new nbApplication(null, new nbOptionSet(array($barOption)));
$t->is($application->hasOptions(), true, '__construct() returns an application with an option');
$t->is($application->getOptions()->count(), 1, '__construct() returns an application with an option');

