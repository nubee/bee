<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test();

$fooArgument = new nbArgument('foo');
$barOption = new nbOption('bar');
$command1 = new DummyCommand("ns2:bas", new nbArgumentSet(array($fooArgument)), new nbOptionSet(array($barOption)));
$command1->setBriefDescription('brief description');

$aliasCommand = new nbAliasCommand('ns:alias', $command1);

$t->comment('nbAliasCommandTest - Test fullname');
$t->is($aliasCommand->getFullname(), "ns:alias", '->getFullName() returns a name without ":"');

$t->comment('nbAliasCommandTest - Test arguments');
$t->is($aliasCommand->getArguments()->count(), 1, '->getArguments() returns 1 argument');
$t->is($aliasCommand->getOptions()->count(), 1, '->getOptions() returns 1 options');

$t->comment('nbAliasCommandTest - Test description');
$t->is($aliasCommand->getBriefDescription(), "brief description", '->getBriefDescription() returns a the contained command description');

$t->comment('nbAliasCommandTest - Test run (success)');
$t->ok($aliasCommand->run(new nbCommandLineParser(), ''));
$t->ok($command1->hasExecuted());

$t->comment('nbAliasCommandTest - Test run (failure)');
$command1->failOnExecute();
$t->ok(!$aliasCommand->run(new nbCommandLineParser(), ''));
$t->ok($command1->hasExecuted());
