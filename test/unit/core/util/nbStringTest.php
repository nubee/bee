<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(32);
$t->comment('String functions');

$t->comment('  1. Uncamelize with "_"');
$tests = array(
  'simpleTest' => 'simple_test',
  'easy' => 'easy',
  'HTML' => 'html',
  'simpleXML' => 'simple_xml',
  'PDFLoad' => 'pdf_load',
  'startMIDDLELast' => 'start_middle_last',
  'AString' => 'a_string',
  'Some4Numbers234' => 'some4_numbers234',
  'TEST123String' => 'test123_string',
);

foreach ($tests as $test => $result) {
  $output = nbString::uncamelize($test, '_');
  $t->is($output, $result, sprintf('Uncamelize %s => %s (separator: "_")', $test, $output));
}

$t->comment('  2. Camelize with "_"');
$tests = array(
  'simple_test' => 'simpleTest',
  'easy' => 'easy',
  'HTML' => 'html',
  'simple_xml' => 'simpleXml',
  'pdf_load' => 'pdfLoad',
  'start_Middle_Last' => 'startMiddleLast',
  'a_sTrInG' => 'aString',
  'some4_numbers234' => 'some4Numbers234',
  'test123_string' => 'test123String',
);

foreach ($tests as $test => $result) {
  $output = nbString::camelize($test, '_');
  $t->is($output, $result, sprintf('Camelize %s => %s (separator: "_")', $test, $output));
}

$t->comment('  3. Uncamelize with "-"');
$tests = array(
  'simpleTest' => 'simple-test',
  'simpleXML' => 'simple-xml',
  'PDFLoad' => 'pdf-load',
  'startMIDDLELast' => 'start-middle-last',
  'AString' => 'a-string',
  'Some4Numbers234' => 'some4-numbers234',
  'TEST123String' => 'test123-string',
);

foreach ($tests as $test => $result) {
  $output = nbString::uncamelize($test);
  $t->is($output, $result, sprintf('Uncamelize %s => %s (separator: "-")', $test, $output));
}

$t->comment('  4. Camelize with "-"');
$tests = array(
  'simple-test' => 'simpleTest',
  'SIMPLE-XML' => 'simpleXml',
  'pdf-Load' => 'pdfLoad',
  'start-middle-last' => 'startMiddleLast',
  'a-string' => 'aString',
  'some4-numbers234' => 'some4Numbers234',
  'test123-string' => 'test123String',
);

foreach ($tests as $test => $result) {
  $output = nbString::camelize($test);
  $t->is($output, $result, sprintf('Camelize %s => %s (separator: "-")', $test, $output));
}

