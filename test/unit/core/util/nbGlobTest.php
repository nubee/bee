<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(1);

$t->comment('nbGlob - Test log');
$t->is(nbGlob::globToRegex('*.php'), '#^(?=[^\\.])[^/]*\\.php$#', '->globToRegex("*.php") returns regexp "#^(?=[^\\.])[^/]*\\.php$#"');