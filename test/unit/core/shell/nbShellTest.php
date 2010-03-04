<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(7);

$t->comment('nbShellTest - Test execute');
$shell = new nbShell();
$ret = $shell->execute('dir');
$t->ok($ret, '->execute() has succeeded');

$t->comment('nbShellTest - Test execute retriving output');
$shell = new nbShell(true);
$ret = $shell->execute('dir');
$t->ok($ret, '->execute() has succeeded');
$t->ok(count($shell->getOutput()), '->getOutput() returns an array of messages');

$t->comment('nbShellTest - Test execute unknown command');
$ret = $shell->execute('unknown_command');
$t->ok($ret === false);

$t->comment('nbShellTest - Test error executing command');
$ret = $shell->execute('dir /e');
$t->ok($ret === false);

$t->comment('nbShellTest - Test redirect stderr to stdout');
$out = array();
$ret = $shell->execute('dir /e', $out);
$t->ok($ret === false);
$t->ok(count($out) > 0);

