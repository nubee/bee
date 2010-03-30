<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(31);

$fooArgument = new nbArgument('foo');
$barOption = new nbOption('bar');

$t->comment('nbApplicationTest - Test constructor');
$application = new DummyApplication();
$t->is($application->getName(), 'UNDEFINED', '->getName() is "UNDEFINED"');
$t->is($application->getVersion(), 'UNDEFINED', '->getVersion() is "UNDEFINED"');
$t->is($application->hasArguments(), true, '__construct() returns an application with arguments');
$t->is($application->hasOptions(), true, '__construct() returns an application with options');
$t->is($application->hasCommands(), false, '__construct() returns an application without commands');

$application = new DummyApplication(array($fooArgument));
$t->is($application->hasArguments(), true, '__construct() returns an application with an argument');
$t->is($application->getArguments()->count(), 2, '__construct() returns an application with 2 arguments');

$application = new DummyApplication(array(), array($barOption));
$t->is($application->hasOptions(), true, '__construct() returns an application with an option');
$t->is($application->getOptions()->count(), 6, '__construct() returns an application with 5 options');

$t->comment('nbApplicationTest - Test run');
$foo = new DummyCommand('foo', null, new nbOptionSet(array(new nbOption('first', 'f'))));
$bar = new DummyCommand('bar', new nbArgumentSet(array(new nbArgument('first', nbArgument::REQUIRED))));
$bas = new DummyCommand('bas', null, new nbOptionSet(array(new nbOption('first', 'f'))));
$fas = new DummyCommand('fas');
$fas->setAlias('fou');

$application = new DummyApplication();
$application->setCommands(new nbCommandSet(array($foo, $bar, $bas, $fas)));
$application->run('foo');
$t->ok($foo->hasExecuted(), '->run() executes command "foo"');
$application->run('bar test');
$t->ok($bar->hasExecuted(), '->run() executes command "bar test"');
$t->is($bar->getArgument('first'), 'test', '->run() executes command "bar test"');
$application->run('bas -f');
$t->ok($bas->hasExecuted(), '->run() executes command "bas -f"');
$t->is($bas->getOption('first'), true, '->run() executes command "bas -f"');
$application->run('fou');
$t->ok($fas->hasExecuted(), '->run() executes command "fas" with alias "fou"');

$t->comment('ApplicationTest - Test VerifyOption');
$application = new DummyApplication(array(), array(new nbOption('option1')));

$foo = new DummyCommand('foo');
$foo->addOption(new nbOption('option1'));
$bar = new DummyCommand('bar');
$bar->addOption(new nbOption('option2'));
$list = new DummyCommand('list');


$application->setCommands(new nbCommandSet(array($foo, $bar, $list)));

try {
  $application->run('foo');
  $t->fail('nbApplication::verifyOption() throws if there are some options in beeApplication and in new command');
}
catch(Exception $e) {
  $t->pass('nbApplication::verifyOption() throws if there are some options in beeApplication and in new command');
}

try {
  $application->run('bar');
  $t->pass('nbApplication::verifyOption() doesn\'t throw if there are different options in beeApplication and in new command');
}
catch(Exception $e) {
  $t->fail('nbApplication::verifyOption() doesn\'t throw if there are different options in beeApplication and in new command');
}

try {
  $application->run();
  $t->pass('nbApplication::verifyOption() doesn\'t throw if there are different options in beeApplication and in new command');
}
catch(Exception $e) {
  $t->fail('nbApplication::verifyOption() doesn\'t throw if there are different options in beeApplication and in new command');
}

$t->comment('nbApplicationTest - Test --config option');
nbConfig::set('nb_pathtest', 'valuetest');

$application = new DummyApplication();
$foo = new DummyCommand('foo', null, new nbOptionSet(array(new nbOption('first', 'f'))));
$application->setCommands(new nbCommandSet(array($foo)));
$application->run('--config=nb_pathtest=cmdvaluetest foo');
$t->is(nbConfig::get('nb_pathtest'), 'cmdvaluetest', 'option "--config" overrides nbConfig property');
$t->ok($foo->hasExecuted(), 'command "foo" has executed');

$application = new DummyApplication();
$foo = new DummyCommand('foo', null, new nbOptionSet(array(new nbOption('first', 'f'))));
$application->setCommands(new nbCommandSet(array($foo)));
$application->run('--config="nb_pathtest = cmdvaluetest" foo');
$t->is(nbConfig::get('nb_pathtest'), 'cmdvaluetest', 'option "--config" overrides nbConfig property');
$t->ok($foo->hasExecuted(), 'command "foo" has executed');

$application = new DummyApplication();
$foo = new DummyCommand('foo', null, new nbOptionSet(array(new nbOption('first', 'f'))));
$application->setCommands(new nbCommandSet(array($foo)));
$application->run('--config="nb_pathtest = cmd value test" foo');
$t->is(nbConfig::get('nb_pathtest'), 'cmd value test', 'option "--config" overrides nbConfig property');
$t->ok($foo->hasExecuted(), 'command "foo" has executed');

$application = new DummyApplication();
$foo = new DummyCommand('foo', null, new nbOptionSet(array(new nbOption('first', 'f'))));
$application->setCommands(new nbCommandSet(array($foo)));
$application->run('--config="nb_pathtest = cmd value = test" foo');
$t->is(nbConfig::get('nb_pathtest'), 'cmd value = test', 'option "--config" overrides nbConfig property');
$t->ok($foo->hasExecuted(), 'command "foo" has executed');

$application = new DummyApplication();
$foo = new DummyCommand('foo', null, new nbOptionSet(array(new nbOption('first', 'f'))));
$application->setCommands(new nbCommandSet(array($foo)));
$application->run('--config=nb_pathtest2=cmdvaluetest2 foo');
$t->is(nbConfig::get('nb_pathtest2'), 'cmdvaluetest2', 'option "--config" creates nbConfig property');
$t->ok($foo->hasExecuted(), 'command "foo" has executed');

$application = new DummyApplication();
$foo = new DummyCommand('foo', null, new nbOptionSet(array(new nbOption('first', 'f'))));
$application->setCommands(new nbCommandSet(array($foo)));
$application->run('--config=nb_path1=cmdvalue1 --config=nb_path2=cmdvalue2 foo');
$t->is(nbConfig::get('nb_path1'), 'cmdvalue1', 'option "--config" creates multiple nbConfig properties');
$t->is(nbConfig::get('nb_path2'), 'cmdvalue2', 'option "--config" creates multiple nbConfig properties');
$t->ok($foo->hasExecuted(), 'command "foo" has executed');
