<?php

//require_once dirname(__FILE__) . '/../../bootstrap/unit.php';
require_once dirname(__FILE__) . '/../../../vendor/lime/lime.php';
require_once dirname(__FILE__) . '/../../../lib/core/util/nbGlob.php';

$t = new lime_test(1);

$t->comment('nbGlob - Test log');
$t->is(nbGlob::globToRegex('*.php'), '#^(?=[^\\.])[^/]*\\.php$#', '->globToRegex("*.php") returns regexp "#^(?=[^\\.])[^/]*\\.php$#"');