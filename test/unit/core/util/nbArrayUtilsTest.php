<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(8);

$t->comment('nbArrayUtils - Test getAssociative');

$key1 = array('foo' => 'fooValue');
$t->is(nbArrayUtils::getAssociative($key1), $key1, 'nbArrayUtils::getAssociative() returns an associative array of all config keys');

$key1 = array('foo' => array('bar', 'baz'));
$t->is(nbArrayUtils::getAssociative($key1), $key1, 'nbArrayUtils::getAssociative() returns an associative array of all config keys');

$key1 = array('foo' => array('bar' => 'baz'));
$result = array('foo_bar' => 'baz');
$t->is(nbArrayUtils::getAssociative($key1), $result, 'nbArrayUtils::getAssociative() returns an associative array of all config keys');

$key1 = array('foo' => array('bar' => array('baz' => 'baq')));
$result = array('foo_bar_baz' => 'baq');
$t->is(nbArrayUtils::getAssociative($key1), $result, 'nbArrayUtils::getAssociative() returns an associative array of all config keys');

$key1 = array('foo' => 'fooValue', 'bar' => 'barValue');
$t->is(nbArrayUtils::getAssociative($key1), $key1, 'nbArrayUtils::getAssociative() returns an associative array of all config keys');

$key1 = array(
  'foo' => 'fooValue',
  'bar' => array('barValue1', 'barValue2')
);
$t->is(nbArrayUtils::getAssociative($key1), $key1, 'nbArrayUtils::getAssociative() returns an associative array of all config keys');

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
$t->is(nbArrayUtils::getAssociative($key1), $result, 'nbArrayUtils::getAssociative() returns an associative array of all config keys');

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
$t->is(nbArrayUtils::getAssociative($key1), $result, 'nbArrayUtils::getAssociative() returns an associative array of all config keys');

