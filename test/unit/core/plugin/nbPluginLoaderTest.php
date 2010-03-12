<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$pluginName = 'tst';

$t = new lime_test(5);

$t->comment('nbPluginLoader - Testing addPlugin()');

nbPluginLoader::getInstance()->addPlugin($pluginName);

$t->ok(class_exists('ClassInsideTstPluginLib'),'->addPlugin() adds plugin/lib to autoload');
$t->ok(class_exists('TstPluginCommand'),'->addPlugin() adds plugin/command to autoload');
$t->ok(class_exists('TstPluginVendorClass'),'->addPlugin() adds plugin/vendor to autoload');

$t->is(nbConfig::get($pluginName.'_foo'),'bar','->addPlugin() loads plugin configuration keys with key prefix "$pluginName" ');

$t->is(nbPluginLoader::getInstance()->getPlugins(), array('tst'), '->getPlugins() returns an array loaded plugin names');