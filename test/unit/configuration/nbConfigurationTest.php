<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(4);

$t->comment('nbConfigurationTest - Test constructor');


$t->is(nbConfiguration::has('key'),false,'nbConfiguration::has() returns false if key is not present');

nbConfiguration::add('key','value');
$t->is(nbConfiguration::has('key'),true,'nbConfiguration::add() adds a new configuration key');

$t->is(nbConfiguration::get('fake-key'),null,'nbConfiguration::get() returns null if key in not present');
$t->is(nbConfiguration::get('fake-key','value'),'value','nbConfiguration::get() returns default value if key in not present');
