<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(1);

$t->comment('nbStreamOutput - Test write');
$output = new nbStreamOutput();
$output->write('test');
$stream = $output->getStream();

$t->is($stream, 'test', '->write() has written "test"');
