<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';

nbPluginLoader::getInstance()->loadPlugins(array('nbAnt'));

$t = new lime_test();

$client = new nbAntClient();

$t->comment('nbAntClientTest - Test command without options');
$t->is($client->getCommandLine('mycmd', array(), array()), 'ant mycmd');

$t->comment('nbAntClientTest - Test command with options');
$options = array('option' => 'value');
$t->is($client->getCommandLine('mycmd', array(), $options), 'ant mycmd -Doption=value');

// test command with arguments
$args = array('arg' => 'value');
$t->is($client->getCommandLine('mycmd', $args, array()), 'ant mycmd -Darg=value');

// test ANT cmd line options (library path)
$client->setLibraryPath("C:\AntExtensions\lib");
$t->is($client->getCommandLine('mycmd', $args, $options),
        'ant -lib C:\AntExtensions\lib mycmd -Darg=value -Doption=value');

// test ANT cmd line options (property file)
$client->setPropertyFile("C:\AntExtensions\lib\myfile.properties");
$t->is($client->getCommandLine('mycmd', $args, $options),
        'ant -lib C:\AntExtensions\lib -propertyfile C:\AntExtensions\lib\myfile.properties mycmd -Darg=value -Doption=value');

// test options with no value
// test ANT cmd line options (property file)
$options['incremental'] = '';
$t->is($client->getCommandLine('mycmd', $args, $options),
        'ant -lib C:\AntExtensions\lib -propertyfile C:\AntExtensions\lib\myfile.properties mycmd -Darg=value -Doption=value -Dincremental=true');
