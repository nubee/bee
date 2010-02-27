<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(1);

$t->comment('nbStreamOutput - Test write');

ob_start();
$output = new nbConsoleOutput();
$output->write('test');
$text = ob_get_contents();
ob_end_clean();

$t->is($text, 'test', '->write() has written "test"');
