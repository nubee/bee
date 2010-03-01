<?php
require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$dataDir = dirname(__FILE__).'/../../data/configuration';

$t = new lime_test(5);

$parser = new nbYamlConfigurationParser();

$t->comment('nbYamlConfigurationParserTest - ');

$t->comment('->get()');
$t->is($parser->get(),array(),'->get() returns parsed files as an array');

$t->comment('->get()');
$yaml = <<<EOF
key: value
EOF;

$t->comment('->parse()');
$parser->parse($yaml);
$t->is($parser->get(),array('key' => 'value'),'->get() returns parsed files as an array');

$yaml = <<<EOF
key2: value2
EOF;

$parser->parse($yaml);
$t->is($parser->get(),array('key' => 'value','key2' => 'value2'),'->get() returns parsed files as an array');

$t->comment('->clear()');
$parser->clear();
$t->is($parser->get(),array(),'->clear() clear parsed files and strings');

$parser->parseFile($dataDir.'/application.yml');
$main = array('main' => array(
                    'key1' => 'appValue1',
                    'key2' => 'appValue2')
        );
$t->is($parser->get(),$main,'->parseFile() parse a yaml file and merge result with others');

//$parser->parseFile($dataDir.'/machine.yml');
//
//$machine = array('main' => array(
//                    'key1' => 'appValue1',
//                    'key2' => 'machineValue2',
//                    'key3' => 'machineValue3')
//        );
//$t->is($parser->get(),$machine,'->parseFile() parse a yaml file and merge result with others');
