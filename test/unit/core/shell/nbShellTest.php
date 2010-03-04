<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(6);

$t->comment('nbShellTest - Test execute');
$shell = new nbShell();
$ret = $shell->execute('dir');
$t->ok($ret, '->execute() has succeeded');

$t->comment('nbShellTest - Test execute retriving output');
$shell = new nbShell(true);
$ret = $shell->execute('dir');
$t->ok($ret, '->execute() has succeeded');
$t->ok(count($shell->getOutput()), '->getOutput() returns an array of messages');

$t->comment('nbShellTest - Test execute result');
$t->ok(!$shell->execute('unknown_command'), '->execute() returns false if executed with unknown command');
$t->ok(!$shell->execute('dir /e'), '->execute() returns false if command execution fails');

$t->comment('nbShellTest - Test redirect stderr to stdout');
$shell = new nbShell(true);
$shell->execute('dir /e');
$t->is(count($shell->getOutput()), 1, '->getOutput() returns an array');


