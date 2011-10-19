<?php
require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

sfServiceContainerAutoloader::register();

$t = new lime_test(6);

$t->comment('nbPluginLoader - Testing loadPlugins()');

$loader = $serviceContainer->pluginLoader;
$loader->addDir(nbConfig::get('nb_test_plugins_dir'));
$loader->loadPlugins(array('FirstPlugin'));
        
$t->ok(class_exists('FirstPluginLibClass'),'->loadPlugin() adds plugin/lib to autoload');
$t->ok(class_exists('FirstCommand'),'->loadPlugin() adds plugin/command to autoload');
$t->ok(!class_exists('SecondCommand'),'->loadPlugin() adds plugin/command to autoload');

$t->ok(key_exists('FirstPlugin', $loader->getPlugins()), '->getPlugins() returns an array of loaded plugin names');

$loader->loadAllPlugins();

$t->ok(class_exists('SecondPluginLibClass'),'->loadAllPlugins() loads all plugins');
$t->ok(class_exists('VendorClass'),'->loadPlugin() adds plugin/vendor to autoload');
