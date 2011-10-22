<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$dataDir = dirname(__FILE__) . '/../../../data/config';
$applicationFile = $dataDir . '/application.config.yml';
$machineFile     = $dataDir . '/machine.config.yml';
$configFile1     = $dataDir . '/parser1.config.yml';
$configFile2     = $dataDir . '/parser2.config.yml';

$t = new lime_test(16);

$configuration = new nbConfiguration();
$parser = new nbYamlConfigParser($configuration);

$t->comment('Test get');

$yaml = <<<EOF
key1: value
key2: %key1%
key3_subkey1: %key2%
EOF;

$t->comment('Test parse');
$parser->parse($yaml);
$t->is($configuration->get('key1'), 'value', '->parse() parse a yaml string and set configuration keys');
$t->is($configuration->get('key2'), '%key1%', '->parse() by default does not replace tokens');
$t->is($configuration->get('key3_subkey1'), '%key2%', '->parse() by default does not replace tokens');
$configuration->reset();

$parser->parse($yaml, 'myprefix');
$t->is($configuration->get('myprefix_key1'), 'value', '->parse() can accept a prefix for config keys');
$t->is($configuration->get('myprefix_key2'), '%key1%', '->parse() can accept a prefix for config keys');
$configuration->reset();

$parser->parse($yaml, '', true);
$t->is($configuration->get('key2'), $configuration->get('key1'), '->parse() can replace tokens');
$t->is($configuration->get('key2'), 'value', '->parse() can replace tokens');
$t->is($configuration->get('key3_subkey1'), $configuration->get('key1'), '->parse() can replace tokens');
$t->is($configuration->get('key3_subkey1'), 'value', '->parse() can replace tokens');
$configuration->reset();

$parser->parse($yaml, 'myprefix', true);
$t->is($configuration->get('myprefix_key2'), 'value', '->parse() can replace tokens with prefix');
$t->is($configuration->get('myprefix_key3_subkey1'), 'value', '->parse() can replace tokens with prefix');
$configuration->reset();


$t->comment('Test parseFile');
$parser->parseFile($applicationFile);
$main = array('main' => array(
    'key1' => 'appValue1',
    'key2' => 'appValue2')
);
$t->is($configuration->get('main'), $main['main'], '->parseFile() parse a yaml file and set configuration');
$configuration->reset();

$parser->parseFile($configFile1);
$t->is($configuration->get('app_plugin1_server'), '%app_server%', '->parseFile() by default does not replace tokens');

try {
  $parser->parseFile($dataDir . '/fake-file.yml');
  $t->fail("->parseFile() throws if file doesn\'t exist");
}
catch (InvalidArgumentException $e) {
  $t->pass("->parseFile() throws if file doesn\'t exist");
}
$configuration->reset();


$t->comment('->parseFile() and replace tokens');
$parser->parseFile($configFile1, '', true);

$t->is($configuration->get('app_plugin1_server'), $configuration->get('app_server'), '->parseFile() can replace tokens');
$t->is($configuration->get('app_plugin1_server'), 'myserver', '->parseFile() can replace tokens');
$configuration->reset();
