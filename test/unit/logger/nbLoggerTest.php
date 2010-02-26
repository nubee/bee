<?php

//require_once dirname(__FILE__) . '/../../bootstrap/unit.php';
require_once dirname(__FILE__) . '/../../../vendor/lime/lime.php';
require_once dirname(__FILE__) . '/../../../lib/core/output/nbOutput.php';
require_once dirname(__FILE__) . '/../../../lib/core/output/nbConsoleOutput.php';
require_once dirname(__FILE__) . '/../../../lib/core/output/nbStreamOutput.php';
require_once dirname(__FILE__) . '/../../../lib/core/logger/nbLogger.php';

$t = new lime_test(5);

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