<?php

//require_once dirname(__FILE__) . '/../../bootstrap/unit.php';
require_once dirname(__FILE__) . '/../../../vendor/lime/lime.php';
require_once dirname(__FILE__) . '/../../../lib/core/output/nbOutput.php';
require_once dirname(__FILE__) . '/../../../lib/core/output/nbStreamOutput.php';

$t = new lime_test(1);

$t->comment('nbStreamOutput - Test write');
$output = new nbStreamOutput();
$output->write('test');
$stream = $output->getStream();

$t->is($stream, 'test', '->write() has written "test"');
