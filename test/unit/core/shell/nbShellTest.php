<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(9);

$t->comment('nbShellTest - Test execute');
$shell = new nbShell(true);
$t->ok($shell->execute('dir'), '->execute() has succeeded');
$t->isnt($shell->getOutput(), '', '->getOutput() returns a not empty string');
$t->is($shell->getError(), '', '->getError() returns an empty string');

$t->comment('nbShellTest - Test execute retriving output');
$shell = new nbShell(true);
$t->ok($shell->execute('dir'), '->execute() has succeeded');

$t->comment('nbShellTest - Test execute result');
$shell = new nbShell(true);
try {
  $shell->execute('unknown_command');
  $t->fail('->execute() throws a LogicException if executed with unknown command');
}
catch(LogicException $e) {
  $t->pass('->execute() throws a LogicException if executed with unknown command');
}
$t->is($shell->getReturnCode(), 1, '->execute() return code is 1');
$t->isnt($shell->getError(), '', '->getError() returns an array with 3 items');

$t->comment('nbShellTest - Test redirect stderr to stdout');
$shell = new nbShell(true);

try {
  $shell->execute('dir /e');
  $t->fail('->execute() throws if a command returns an error code');
}
catch(Exception $e) {
  $t->pass('->execute() throws if a command returns an error code');
}
$t->is($shell->getReturnCode(), 1, '->execute() return code is 1');


