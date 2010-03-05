<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(16);

$shell = new nbShell();
$shell->execute('ls');

die;
$t->comment('nbShellTest - Test execute (success) without output redirecting');
$shell = new nbShell();
ob_start();
$ret = $shell->execute('dir');
$contents = ob_get_contents();
ob_end_clean();
$t->ok($ret, '->execute() has succeeded');
$t->ok(strlen($contents) > 0, '->execute() writes to console');
$t->ok(strlen($shell->getOutput()) > 0, '->getOutput() returns a message');
$t->is(strlen($shell->getError()), 0, '->getError() returns an empty message');

$t->comment('nbShellTest - Test execute (success) with output redirecting');
$shell = new nbShell(true);
ob_start();
$ret = $shell->execute('ls');
$contents = ob_get_contents();
ob_end_clean();
$t->ok($ret, '->execute() has succeeded');
$t->is(strlen($contents), 0, '->execute() don\'t writes to console');
$t->ok(strlen($shell->getOutput()) > 0, '->getOutput() returns a message');
$t->is(strlen($shell->getError()), 0, '->getError() returns an empty message');

$t->comment('nbShellTest - Test execute (error) without output redirecting');
$shell = new nbShell();
ob_start();
$ret = $shell->execute('ls --unknown');
$contents = ob_get_contents();
ob_end_clean();
$t->ok(!$ret, '->execute() has failed');
$t->ok(strlen($contents) > 0, '->execute() writes to console');
$t->is(strlen($shell->getOutput()), 0, '->getOutput() returns an empty message');
$t->ok(strlen($shell->getError()) > 0, '->getError() returns a message');

$t->comment('nbShellTest - Test execute (error) with output redirecting');
$shell = new nbShell(true);
ob_start();
$ret = $shell->execute('ls --unknown');
$contents = ob_get_contents();
ob_end_clean();
$t->ok(!$ret, '->execute() has failed');
$t->is(strlen($contents), 0, '->execute() don\'t writes to console');
$t->is(strlen($shell->getOutput()), 0, '->getOutput() returns an empty message');
$t->ok(strlen($shell->getError()), '->getError() returns an empty message');
