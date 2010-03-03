<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(2);

$t->comment('FormatterTest');

$formatter = new nbFormatter();
$t->comment('->format()');
$t->is($formatter->format("<info>test</info>"), "test", '->format() formats "<info>test</info>" as "test"');
$t->is($formatter->formatLine("test"), "test", '->formatLine() formats "test" as "test\n"');
