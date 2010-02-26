<?php

//require_once dirname(__FILE__) . '/../../bootstrap/unit.php';
require_once dirname(__FILE__) . '/../../../vendor/lime/lime.php';
require_once dirname(__FILE__) . '/../../../core/formatter/Formatter.php';

$t = new lime_test(3);

$t->comment('FormatterTest');

$formatter = new nbFormatter();
$t->comment('->format()');
$t->is($formatter->format("test"), "test", '->format() formats "test" as "test"');
$t->is($formatter->formatLine("test"), "test", '->formatLine() formats "test" as "test\n"');
$t->is($formatter->formatText("<info>test</info>"), "test", '->formatText() formats "[test|INFO]" as "test"');

try {
  throw new Exception('');
  $t->fail('exception thrown');
}
catch(Exception $e) {
  $t->pass('exception thrown');
}
