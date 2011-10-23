<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(27);

$t->comment('nbConfigTest - Test get all');
$configuration = new nbConfiguration();

$t->is($configuration->getAll(), array(), '->getAll() returns all configuration keys');
$configuration->set('foo', 'fooValue');
$t->is($configuration->getAll(), array('foo' => 'fooValue'), '->getAll() returns all configuration keys');
$configuration->set('bar', 'barValue');


$t->comment('nbConfigTest - Test remove');
$configuration = new nbConfiguration();

$configuration->set('bar', 'barValue');
$configuration->remove('bar');
$t->is($configuration->getAll(), array(), '->remove() removes a sigle configuration key');


$t->comment('nbConfigTest - Test reset');
$configuration = new nbConfiguration();

$configuration->set('foo', 'fooValue');
$configuration->set('bar', 'barValue');
$t->is(count($configuration->getAll()), 2, '->reset() remove all keys');
$configuration->reset();
$t->is($configuration->getAll(), array(), '->reset() remove all keys');


$t->comment('nbConfigTest - Test has');
$configuration = new nbConfiguration();

$t->is($configuration->has('key'), false, '$configuration->has() returns false if key is not present');
$configuration->set('key', 'value');
$t->is($configuration->has('key'), true, '$configuration->has() returns true if key is present');

$key2 = array('foo' => 'fooValue');
$configuration->set('key2', $key2);
$t->is($configuration->has('key2_foo'), true, '$configuration->has() returns true if "key path" is present');


$t->comment('nbConfigTest - Test get');
$configuration = new nbConfiguration();

$configuration->set('key2', $key2);
$t->is($configuration->get('fake-key'), null, '$configuration->get() returns null if key in not present');
$t->is($configuration->get('fake-key', 'value'), 'value', '$configuration->get() returns default value if key in not present');

$t->is($configuration->get('key2'), $key2, '$configuration->get() returns an array if key has subkeys');
$t->is($configuration->get('key2_foo'), $key2['foo'], '$configuration->get() parse a "key path" and returns leaf value');


$t->comment('nbConfigTest - Test set');
$configuration = new nbConfiguration();

$configuration->set('bar', 'barValue');
$t->is($configuration->get('bar'), 'barValue', '$configuration->set() sets a new value for key');

$configuration->set('bar', 'newBarValue');
$t->is($configuration->get('bar'), 'newBarValue', '$configuration->set() sets a new value for key');

$configuration->set('bar_sub', 'subValue');
$t->is($configuration->get('bar'), array('sub' => 'subValue'), '$configuration->set() sets a new value for key');
$t->is($configuration->get('bar_sub'), 'subValue', '$configuration->set() sets a new value for key');

$configuration->set('bar_sub2', 'subValue2');
$t->is($configuration->get('bar'), array('sub' => 'subValue', 'sub2' => 'subValue2'), '$configuration->set() sets a new value for key');
$configuration = new nbConfiguration();


$t->comment('nbConfigTest - Test add');
$configuration = new nbConfiguration();

$key1 = array('foo' => 'fooValue');
$key2 = array('bar' => 'barValue');
$configuration->add($key1);
$configuration->add($key2);
$t->is($configuration->getAll(), array('foo' => 'fooValue', 'bar' => 'barValue'), '$configuration->add() old and new values');

$key2 = array('bar' => 'barVal', 'bar2' => 'barValue2');
$configuration->add($key2);
$t->is($configuration->getAll(), array('foo' => 'fooValue', 'bar' => 'barVal', 'bar2' => 'barValue2'), '$configuration->add() old and new values');

$key1 = array('foo' => 'fooVal', 'foo2' => 'fooValue2');
$configuration->add($key1);
$t->is($configuration->getAll(), array('foo' => 'fooVal', 'foo2' => 'fooValue2', 'bar' => 'barVal', 'bar2' => 'barValue2'), '$configuration->add() old and new values');

$key2 = array('foo2' => 'fooVal2', 'bar' => 'barVal', 'bar2' => 'barValue2');
$configuration->add($key2);
$t->is($configuration->getAll(), array('foo' => 'fooVal', 'foo2' => 'fooVal2', 'bar' => 'barVal', 'bar2' => 'barValue2'), '$configuration->add() old and new values');

$t->comment('nbConfigTest - Test add with prefix');
$configuration = new nbConfiguration();

$key1 = array('foo' => 'fooValue');
$key2 = array('bar' => 'barValue');
$result = array('myprefix' => array('foo' => 'fooValue', 'bar' => 'barValue'));
$configuration->add($key1, 'myprefix');
$configuration->add($key2, 'myprefix');
$t->is($configuration->get('myprefix_foo'), 'fooValue', '$configuration->add() can set a prefix for 1st level keys');
$t->is($configuration->getAll(), $result, '$configuration->add() can set a prefix for 1st level keys');


$t->comment('nbConfigTest - Test add with replace tokens');
$configuration = new nbConfiguration();

$key1 = array('foo' => 'bar');
$key2 = array('baz' => '%foo%');
$key3 = array('blu' => array('%foo%', '%baz%'));
$key4 = array('bup' => '%flo%');
$configuration->add($key1, '');
$configuration->add($key2, '', true);
$configuration->add($key3, '', true);
$configuration->add($key4, '', true);

$t->is($configuration->get('baz'), $configuration->get('foo'), '->add() can replace tokens');
$t->is($configuration->get('baz'), 'bar', '->add() can replace child tokens');
$t->is($configuration->get('blu'), array('bar', 'bar'), '->add() can replace array tokens');
$t->is($configuration->get('bup'), '%flo%', '->add() will not replace a value if not set');
