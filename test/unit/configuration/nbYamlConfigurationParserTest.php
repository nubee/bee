<?php
require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$dataDir = dirname(__FILE__).'/../../data/configuration';

$t = new lime_test(2);

$parser = new nbYamlConfigurationParser();

$t->comment('nbYamlConfigurationParserTest - ');

$t->comment('->get()');
$yaml = <<<EOF
key: value
EOF;

nbConfiguration::reset();

$t->comment('->parse()');
$parser->parse($yaml);
$t->is(nbConfiguration::get('key'),'value','->parse() parse a yaml string and set configuration keys');


$parser->parseFile($dataDir.'/application.yml');
$main = array('main' => array(
                    'key1' => 'appValue1',
                    'key2' => 'appValue2')
        );
$t->is(nbConfiguration::get('main'),$main['main'],'->parseFile() parse a yaml file and set configuration');
