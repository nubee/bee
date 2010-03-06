<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(30);

$t->comment('nbConfigTest - Test get all');
nbConfig::reset();
$t->is(nbConfig::getAll(), array(), '->getAll() returns all configuration keys');
nbConfig::set('foo', 'fooValue');
$t->is(nbConfig::getAll(), array('foo'=>'fooValue'), '->getAll() returns all configuration keys');
nbConfig::set('bar', 'barValue');

$t->comment('nbConfigTest - Test remove');
nbConfig::remove('bar');
$t->is(nbConfig::getAll(), array('foo'=>'fooValue'), '->remove() removes a sigle configuration key');

$t->comment('nbConfigTest - Test reset');
nbConfig::reset();
$t->is(nbConfig::getAll(), array(), '->reset() remove all keys');

$t->comment('nbConfigTest - Test has');
$t->is(nbConfig::has('key'), false, 'nbConfig::has() returns false if key is not present');
nbConfig::set('key', 'value');
$t->is(nbConfig::has('key'), true, 'nbConfig::has() returns true if key is present');

$key2 = array('foo' => 'fooValue');
nbConfig::set('key2', $key2);
$t->is(nbConfig::has('key2_foo'), true, 'nbConfig::has() returns true if "key path" is present');

$t->comment('nbConfigTest - Test get');
$t->is(nbConfig::get('fake-key'), null, 'nbConfig::get() returns null if key in not present');
$t->is(nbConfig::get('fake-key', 'value'), 'value', 'nbConfig::get() returns default value if key in not present');

$t->is(nbConfig::get('key2'), $key2 , 'nbConfig::get() returns an array if key has subkeys');
$t->is(nbConfig::get('key2_foo'), $key2['foo'] , 'nbConfig::get() parse a "key path" and returns leaf value');

$t->comment('nbConfigTest - Test set');
nbConfig::set('bar', 'barValue');
$t->is(nbConfig::get('bar'), 'barValue', 'nbConfig::set() sets a new value for key');

nbConfig::set('bar', 'newBarValue');
$t->is(nbConfig::get('bar'), 'newBarValue', 'nbConfig::set() sets a new value for key');

nbConfig::set('bar_sub', 'subValue');
$t->is(nbConfig::get('bar'), array('sub'=>'subValue'), 'nbConfig::set() sets a new value for key');
$t->is(nbConfig::get('bar_sub'), 'subValue', 'nbConfig::set() sets a new value for key');

nbConfig::set('bar_sub2', 'subValue2');
$t->is(nbConfig::get('bar'), array('sub'=>'subValue', 'sub2'=>'subValue2') , 'nbConfig::set() sets a new value for key');
nbConfig::reset();

$t->comment('nbConfigTest - Test getAssociative');

$key1 = array('foo' => 'fooValue');
$t->is(nbConfig::getAssociative($key1), $key1, 'nbConfig::getAssociative() returns an associative array of all config keys');

$key1 = array('foo' => array('bar', 'baz'));
$t->is(nbConfig::getAssociative($key1), $key1, 'nbConfig::getAssociative() returns an associative array of all config keys');

$key1 = array('foo' => array('bar' => 'baz'));
$result = array('foo_bar' => 'baz');
$t->is(nbConfig::getAssociative($key1), $result, 'nbConfig::getAssociative() returns an associative array of all config keys');

$key1 = array('foo' => array('bar' => array('baz' => 'baq')));
$result = array('foo_bar_baz' => 'baq');
$t->is(nbConfig::getAssociative($key1), $result, 'nbConfig::getAssociative() returns an associative array of all config keys');

$key1 = array('foo' => 'fooValue', 'bar' => 'barValue');
$t->is(nbConfig::getAssociative($key1), $key1, 'nbConfig::getAssociative() returns an associative array of all config keys');

$key1 = array(
  'foo' => 'fooValue',
  'bar' => array('barValue1', 'barValue2')
);
$t->is(nbConfig::getAssociative($key1), $key1, 'nbConfig::getAssociative() returns an associative array of all config keys');

$key1 = array(
  'foo' => 'fooValue',
  'bar' => array(
    'bar1' => 'barValue1',
    'bar2' => 'barValue2'
  )
);

$result = array(
  'foo' => 'fooValue',
  'bar_bar1' => 'barValue1',
  'bar_bar2' => 'barValue2'
);
$t->is(nbConfig::getAssociative($key1), $result, 'nbConfig::getAssociative() returns an associative array of all config keys');

$key1 = array(
  'foo' => 'fooValue',
  'bar' => array(
    'bar1' => 'barValue1',
    'bar2' => 'barValue2'
  ),
  'baz' => array(
    'baz1' => array('subbaz' => array('a', 'b', 'c'))
  )
);

$result = array(
  'foo' => 'fooValue',
  'bar_bar1' => 'barValue1',
  'bar_bar2' => 'barValue2',
  'baz_baz1_subbaz' => array('a', 'b', 'c')
);
$t->is(nbConfig::getAssociative($key1), $result, 'nbConfig::getAssociative() returns an associative array of all config keys');

$t->comment('nbConfigTest - Test add');

$key1 = array('foo' => 'fooValue');
$key2 = array('bar' => 'barValue');
nbConfig::add($key1);
nbConfig::add($key2);
$t->is(nbConfig::getAll(), array('foo' => 'fooValue', 'bar' => 'barValue') , 'nbConfig::add() old and new values');

$key2 = array('bar' => 'barVal', 'bar2' => 'barValue2');
nbConfig::add($key2);
$t->is(nbConfig::getAll(), array('foo' => 'fooValue', 'bar' => 'barVal', 'bar2' => 'barValue2') , 'nbConfig::add() old and new values');

$key1 = array('foo' => 'fooVal', 'foo2' => 'fooValue2');
nbConfig::add($key1);
$t->is(nbConfig::getAll(), array('foo' => 'fooVal', 'foo2' => 'fooValue2', 'bar' => 'barVal', 'bar2' => 'barValue2') , 'nbConfig::add() old and new values');

$key2 = array('foo2' => 'fooVal2', 'bar' => 'barVal', 'bar2' => 'barValue2');
nbConfig::add($key2);
$t->is(nbConfig::getAll(), array('foo' => 'fooVal', 'foo2' => 'fooVal2', 'bar' => 'barVal', 'bar2' => 'barValue2') , 'nbConfig::add() old and new values');

nbConfig::reset();
$key1 = array('foo' => 'fooValue');
$key2 = array('bar' => 'barValue');
$result =array('myprefix'=>array('foo' => 'fooValue','bar' => 'barValue'));
nbConfig::add($key1,'myprefix');
nbConfig::add($key2,'myprefix');
$t->is(nbConfig::get('myprefix_foo'), 'fooValue', 'nbConfig::add() can set a prefix for 1st level keys');
$t->is(nbConfig::getAll(), $result , 'nbConfig::add() can set a prefix for 1st level keys');
