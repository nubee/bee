<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(2);

$t->comment('nbStreamOutput - Test write');
$output = new nbStreamOutput();
$output->write('test');
$stream = $output->getStream();

$t->is($stream, 'test', '->write() has written "test"');
$t->is($output->getStream(), '', '->getStream() now is empty');
