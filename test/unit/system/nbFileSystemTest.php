<?php

//require_once dirname(__FILE__) . '/../../bootstrap/unit.php';
require_once dirname(__FILE__) . '/../../../vendor/lime/lime.php';
require_once dirname(__FILE__) . '/../../../lib/core/system/nbFileSystem.php';
require_once dirname(__FILE__) . '/../../../lib/core/util/nbGlob.php';

$t = new lime_test(1);

$dataDir = dirname(__FILE__) . '/../../data/system';

$t->is(nbFileSystem::getFileName($dataDir . '/Class1.php'), 'Class1.php', '->getFileName() returns "Class1.php"');
