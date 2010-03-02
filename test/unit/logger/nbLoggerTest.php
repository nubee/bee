<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(6);

$t->comment('nbLoggerTest - Test log');
$output = new nbStreamOutput();

$logger = nbLogger::getInstance();
$logger->setOutput($output);
$logger->log("test");

$t->is($output->getStream(), "test", '->log() has written "test"');

$t->comment('nbLoggerTest - Test format levels');
$t->is(nbLogger::formatLevel(nbLogger::ERROR), 'ERROR', '->formatLevel() has ERROR level');
$t->is(nbLogger::formatLevel(nbLogger::INFO), 'INFO', '->formatLevel() has INFO level');
$t->is(nbLogger::formatLevel(nbLogger::COMMENT), 'COMMENT', '->formatLevel() has COMMENT level');
$t->is(nbLogger::formatLevel(nbLogger::QUESTION), 'QUESTION', '->formatLevel() has QUESTION level');

$t->is($logger->format('text', 'info'), '<info>text</info>', '->format() formats "text" as "<info>text</info"');