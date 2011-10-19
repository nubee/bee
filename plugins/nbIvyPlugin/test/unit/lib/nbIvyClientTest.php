<?php

require_once dirname(__FILE__) . '/../../../../../test/bootstrap/unit.php';

$serviceContainer->pluginLoader->loadPlugins(array('nbIvyPlugin'));


$t = new lime_test(2);

$t->comment('nbIvyClientTest - Test ');
$client = new nbIvyClient();
$pattern = '/java -jar ".+\.jar" -settings ".+\.xml" -retrieve .+ -ivy "ivy\.xml"/';
$t->todo('->getRetrieveCmdLine()');
//$t->is(preg_match($pattern, $client->getRetrieveCmdLine()),
//  1,
//  '->getRetrieveCmdLine() returns "' . $pattern . '"');
$pattern = '/java -jar ".+\.jar" -settings ".+\.xml" -retrieve .+ -ivy "customivy\.xml"/';
$t->todo('->getRetrieveCmdLine()');
//$t->is(preg_match($pattern, $client->getRetrieveCmdLine()),
//  1,
//  '->getRetrieveCmdLine() returns "' . $pattern . '"');
