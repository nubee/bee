<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(0);
/*
$cmd = new nbGenerateCommandCommand();

$t->comment('nbGenerateCommandCommand - Test get namespace');
$t->is($cmd->getNamespace(), 'templates', '->getNamespace() is "templates"');

$t->comment('nbGenerateCommandCommand - Test get name');
$t->is($cmd->getName(), '', '->getName() is "BeeTemplate"');

$t->comment('nbGenerateCommandCommand - Test execute');
try {
  $cmd->execute(array(), array());
  $t->fail('no code should be executed after throwing an exception');
}
catch (sfCommandArgumentsException $exc) {
  $t->pass('exception caught successfully');
}

$t->ok($cmd->execute(array('ns', 'command_name', 'class_name'), array()));
*/