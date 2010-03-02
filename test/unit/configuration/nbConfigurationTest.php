<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(28);

$t->comment('nbConfigurationTest - Test constructor');

$t->comment('->getAll()');

nbConfiguration::reset();
$t->is(nbConfiguration::getAll(), array(), '->getAll() returns all configuration keys');
nbConfiguration::set('foo', 'fooValue');
$t->is(nbConfiguration::getAll(), array('foo'=>'fooValue'), '->getAll() returns all configuration keys');
nbConfiguration::set('bar', 'barValue');

$t->comment('->remove()');

nbConfiguration::remove('bar');
$t->is(nbConfiguration::getAll(), array('foo'=>'fooValue'), '->remove() removes a sigle configuration key');

$t->comment('->reset()');

nbConfiguration::reset();
$t->is(nbConfiguration::getAll(), array(), '->reset() remove all keys');

$t->comment('->has()');

$t->is(nbConfiguration::has('key'), false, 'nbConfiguration::has() returns false if key is not present');
nbConfiguration::set('key', 'value');
$t->is(nbConfiguration::has('key'), true, 'nbConfiguration::has() returns true if key is present');

$key2 = array('foo' => 'fooValue');
nbConfiguration::set('key2', $key2);
$t->is(nbConfiguration::has('key2_foo'), true, 'nbConfiguration::has() returns true if "key path" is present');

$t->comment('->get()');

$t->is(nbConfiguration::get('fake-key'), null, 'nbConfiguration::get() returns null if key in not present');
$t->is(nbConfiguration::get('fake-key', 'value'), 'value', 'nbConfiguration::get() returns default value if key in not present');

$t->is(nbConfiguration::get('key2'), $key2 , 'nbConfiguration::get() returns an array if key has subkeys');
$t->is(nbConfiguration::get('key2_foo'), $key2['foo'] , 'nbConfiguration::get() parse a "key path" and returns leaf value');

$t->comment('->set()');

nbConfiguration::set('bar', 'barValue');
$t->is(nbConfiguration::get('bar'), 'barValue', 'nbConfiguration::set() sets a new value for key');
nbConfiguration::set('bar', 'newBarValue');
$t->is(nbConfiguration::get('bar'), 'newBarValue', 'nbConfiguration::set() sets a new value for key');

nbConfiguration::set('bar_sub', 'subValue');
$t->is(nbConfiguration::get('bar'), array('sub'=>'subValue'), 'nbConfiguration::set() sets a new value for key');
$t->is(nbConfiguration::get('bar_sub'), 'subValue', 'nbConfiguration::set() sets a new value for key');
nbConfiguration::set('bar_sub2', 'subValue2');
$t->is(nbConfiguration::get('bar'), array('sub'=>'subValue', 'sub2'=>'subValue2') , 'nbConfiguration::set() sets a new value for key');
nbConfiguration::reset();

$t->comment('->getAssociative()');

$key1 = array('foo' => 'fooValue');
$t->is(nbConfiguration::getAssociative($key1), $key1, 'nbConfiguration::getAssociative() return an associative array of all config keys');

$key1 = array('foo' => array('bar', 'baz'));
$t->is(nbConfiguration::getAssociative($key1), $key1, 'nbConfiguration::getAssociative() return an associative array of all config keys');

$key1 = array('foo' => array('bar'=>'baz'));
$result = array('foo_bar' => 'baz');
$t->is(nbConfiguration::getAssociative($key1), $result, 'nbConfiguration::getAssociative() return an associative array of all config keys');

$key1 = array('foo' => array('bar'=>array('baz'=>'baq')));
$result = array('foo_bar_baz' => 'baq');
$t->is(nbConfiguration::getAssociative($key1), $result, 'nbConfiguration::getAssociative() return an associative array of all config keys');


$key1 = array('foo' => 'fooValue', 'bar' => 'barValue');
$t->is(nbConfiguration::getAssociative($key1), $key1, 'nbConfiguration::getAssociative() return an associative array of all config keys');
$key1 = array('foo' => 'fooValue',
              'bar' => array('barValue1', 'barValue2')
        );

$t->is(nbConfiguration::getAssociative($key1), $key1, 'nbConfiguration::getAssociative() return an associative array of all config keys');

$key1 = array('foo' => 'fooValue',
              'bar' => array('bar1' => 'barValue1',
                              'bar2' => 'barValue2')
        );

$result = array('foo' => 'fooValue',
                'bar_bar1' => 'barValue1',
                'bar_bar2' => 'barValue2');
$t->is(nbConfiguration::getAssociative($key1), $result, 'nbConfiguration::getAssociative() return an associative array of all config keys');

$key1 = array('foo' => 'fooValue',
              'bar' => array('bar1' => 'barValue1',
                              'bar2' => 'barValue2'),
              'baz' => array(
                        'baz1' => array('subbaz' => array('a', 'b', 'c'))
                          )
        );

$result = array('foo' => 'fooValue',
                'bar_bar1' => 'barValue1',
                'bar_bar2' => 'barValue2',
                'baz_baz1_subbaz' => array('a', 'b', 'c')
  );
$t->is(nbConfiguration::getAssociative($key1), $result, 'nbConfiguration::getAssociative() return an associative array of all config keys');

$t->comment('->add()');

$key1 = array('foo' => 'fooValue');
$key2 = array('bar' => 'barValue');
nbConfiguration::add($key1);
nbConfiguration::add($key2);
$t->is(nbConfiguration::getAll(), array('foo' => 'fooValue', 'bar' => 'barValue') , 'nbConfiguration::add() old and new values');

$key2 = array('bar' => 'barVal', 'bar2' => 'barValue2');
nbConfiguration::add($key2);
$t->is(nbConfiguration::getAll(), array('foo' => 'fooValue', 'bar' => 'barVal', 'bar2' => 'barValue2') , 'nbConfiguration::add() old and new values');

$key1 = array('foo' => 'fooVal', 'foo2' => 'fooValue2');
nbConfiguration::add($key1);
$t->is(nbConfiguration::getAll(), array('foo' => 'fooVal', 'foo2' => 'fooValue2', 'bar' => 'barVal', 'bar2' => 'barValue2') , 'nbConfiguration::add() old and new values');

$key2 = array('foo2' => 'fooVal2', 'bar' => 'barVal', 'bar2' => 'barValue2');
nbConfiguration::add($key2);
$t->is(nbConfiguration::getAll(), array('foo' => 'fooVal', 'foo2' => 'fooVal2', 'bar' => 'barVal', 'bar2' => 'barValue2') , 'nbConfiguration::add() old and new values');

