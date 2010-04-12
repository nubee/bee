<?php
require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

sfServiceContainerAutoloader::register();

$pluginName = 'tstPlugin';

$t = new lime_test(5);

$t->comment('nbPluginLoader - Testing loadPlugins()');

$loader = $serviceContainer->pluginLoader;
$loader->addDir(nbConfig::get('nb_test_plugin_dir'));
$loader->loadPlugins(array($pluginName));
        
$t->ok(class_exists('ClassInsideTstPluginLib'),'->loadPlugin() adds plugin/lib to autoload');
$t->ok(class_exists('TstPluginCommand'),'->loadPlugin() adds plugin/command to autoload');
$t->ok(class_exists('TstPluginVendorClass'),'->loadPlugin() adds plugin/vendor to autoload');

$t->ok(key_exists('tstPlugin', $loader->getPlugins()), '->getPlugins() returns an array of loaded plugin names');

//nbPluginLoader::getInstance()->loadPlugins(array($pluginName,'secondPlugin'));
$loader->loadAllPlugins();

$t->ok(class_exists('ClassInsideSecondPluginLib'),'->loadAllPlugins() loads all plugins');
