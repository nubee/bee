<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$pluginName = 'tst';

$t = new lime_test(5);

$t->comment('nbPluginLoader - Testing loadPlugins()');

nbPluginLoader::getInstance()->loadPlugins(array($pluginName));

$t->ok(class_exists('ClassInsideTstPluginLib'),'->loadPlugin() adds plugin/lib to autoload');
$t->ok(class_exists('TstPluginCommand'),'->loadPlugin() adds plugin/command to autoload');
$t->ok(class_exists('TstPluginVendorClass'),'->loadPlugin() adds plugin/vendor to autoload');

$t->is(nbPluginLoader::getInstance()->getPlugins(), array('tst'), '->getPlugins() returns an array loaded plugin names');

nbPluginLoader::getInstance()->loadPlugins(array($pluginName,'second'));

$t->ok(class_exists('ClassInsideSecondPluginLib'),'->loadPlugins() loads all plugins');
