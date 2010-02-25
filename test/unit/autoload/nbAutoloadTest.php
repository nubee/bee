<?php

require_once dirname(__FILE__) . '/../../../vendor/lime/lime.php';
require_once dirname(__FILE__) . '/../../../lib/core/autoload/nbAutoload.php';

$t = new lime_test(4);

$autoload = nbAutoload::getInstance();
$autoload->unregister();

$dir = dirname(__FILE__);
$autoload->addDirectory($dir, '*.php', false);
$t->is(class_exists('TestClass'), false, 'class "TestClass" not found before "register"');

$autoload->register();
$t->is(class_exists('TestClass'), true, 'autoload included "TestClass" file');
$t->is(class_exists('SubClass') , false, 'autoload didn\'t included files recursively ');

$autoload->addDirectory($dir, '*.php', true);
$t->is(class_exists('SubClass') , true, 'autoload included "SubClass" recursively');
