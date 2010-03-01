<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(16);

$t->comment('nbConfigurationTest - Test constructor');

$t->comment('->getAll()');

nbConfiguration::reset();
$t->is(nbConfiguration::getAll(), array(), '->getAll() returns all configuration keys');
nbConfiguration::add('foo', 'fooValue');
$t->is(nbConfiguration::getAll(), array('foo'=>'fooValue'), '->getAll() returns all configuration keys');
nbConfiguration::add('bar', 'barValue');

$t->comment('->remove()');

nbConfiguration::remove('bar');
$t->is(nbConfiguration::getAll(), array('foo'=>'fooValue'), '->remove() removes a sigle configuration key');

$t->comment('->reset()');

nbConfiguration::reset();
$t->is(nbConfiguration::getAll(), array(), '->reset() remove all keys');

$t->comment('->has()');

$t->is(nbConfiguration::has('key'), false, 'nbConfiguration::has() returns false if key is not present');
nbConfiguration::add('key', 'value');
$t->is(nbConfiguration::has('key'), true, 'nbConfiguration::has() returns true if key is present');

$key2 = array('foo' => 'fooValue');
nbConfiguration::add('key2', $key2);
$t->is(nbConfiguration::has('key2_foo'), true, 'nbConfiguration::has() returns true if "key path" is present');

$t->comment('->get()');

$t->is(nbConfiguration::get('fake-key'), null, 'nbConfiguration::get() returns null if key in not present');
$t->is(nbConfiguration::get('fake-key', 'value'), 'value', 'nbConfiguration::get() returns default value if key in not present');

$t->is(nbConfiguration::get('key2'), $key2 , 'nbConfiguration::get() returns an array if key has subkeys');
$t->is(nbConfiguration::get('key2_foo'), $key2['foo'] , 'nbConfiguration::get() parse a "key path" and returns leaf value');

$t->comment('->add()');

nbConfiguration::add('bar','barValue');
$t->is(nbConfiguration::get('bar'), 'barValue' , 'nbConfiguration::set() sets a new value for key');
nbConfiguration::add('bar','newBarValue');
$t->is(nbConfiguration::get('bar'), 'newBarValue' , 'nbConfiguration::set() sets a new value for key');

nbConfiguration::add('bar_sub','subValue');
$t->is(nbConfiguration::get('bar'), array('sub'=>'subValue') , 'nbConfiguration::set() sets a new value for key');
$t->is(nbConfiguration::get('bar_sub'), 'subValue' , 'nbConfiguration::set() sets a new value for key');
nbConfiguration::add('bar_sub2','subValue2');
$t->is(nbConfiguration::get('bar'), array('sub'=>'subValue', 'sub2'=>'subValue2') , 'nbConfiguration::set() sets a new value for key');
