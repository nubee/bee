<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test();

$t->comment('nbProgressTest - Test 1');

$progress = new nbProgress(1, 1);
$t->is($progress->getProgress(0), 0, '->getProgress() with 0 is always 0');
$t->is($progress->getProgress(1), 100, '->getProgress() with maxValue is always 100');

$progress = new nbProgress(2, 2);
$t->is($progress->getProgress(1), 50, '->getProgress() returns 50');

$progress = new nbProgress(50, 4);
$t->is($progress->getProgress(0), 0, '->getProgress() with 0 is always 0');
$t->is($progress->getProgress(50), 100, '->getProgress() with maxValue is always 100');
$t->is($progress->getProgress(2), null, '->getProgress() returns null');
$t->is($progress->getProgress(25), 50, '->getProgress() returns 50');
$t->is($progress->getProgress(13), 25, '->getProgress() returns 25');
$t->is($progress->getProgress(38), 75, '->getProgress() returns 25');
