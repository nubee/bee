<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$dataDir = dirname(__FILE__).'/../../../data/configuration';

$t = new lime_test(4);

$parser = new nbYamlConfigParser();

$t->comment('nbYamlConfigParserTest - Test get');

$yaml = <<<EOF
key: value
EOF;

nbConfig::reset();

$t->comment('nbYamlConfigParserTest - Test parse');
$parser->parse($yaml);
$t->is(nbConfig::get('key'),'value','->parse() parse a yaml string and set configuration keys');


$parser->parseFile($dataDir.'/application.yml');
$main = array('main' => array(
          'key1' => 'appValue1',
          'key2' => 'appValue2')
        );
$t->is(nbConfig::get('main'),$main['main'],'->parseFile() parse a yaml file and set configuration');

try {
  $parser->parseFile($dataDir.'/fake-file.yml');
  $t->fail("->parseFile() throws if file doesn\'t exist");
}
catch(InvalidArgumentException $e) {
  $t->pass("->parseFile() throws if file doesn\'t exist");
}


nbConfig::reset();

$t->comment('nbYamlConfigParserTest - Test parse');
$parser->parse($yaml, 'myprefix');
$t->is(nbConfig::get('myprefix_key'),'value','->parseFile() can accept a prefix for config keys');
