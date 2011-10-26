<?php

require_once(dirname(__FILE__) . '/../../../bootstrap/unit.php');

$t = new lime_test(108);

// __construct()
$t->comment('nbCommandLineParserTest - Test constructor');

$parser = new nbCommandLineParser();
$t->isa_ok($parser->getArguments(), 'nbArgumentSet', '__construct() creates a new nbArgumentsSet if none given');
$t->isa_ok($parser->getOptions(), 'nbOptionSet', '__construct() creates a new nbOptionSet if none given');

$parser = new nbCommandLineParser(array());
$t->pass('__construct() takes an array as its first argument');
$t->isa_ok($parser->getArguments(), 'nbArgumentSet', '__construct()  builds an argument set with an empty array as its first argument');

$parser = new nbCommandLineParser(array(), array());
$t->pass('__construct() takes a nbOptionSet as its second argument');
$t->isa_ok($parser->getOptions(), 'nbOptionSet', '__construct() builds an option set with an empty array as its second argument');

// ->setArguments() ->getArguments()
$t->comment('nbCommandLineParserTest - Test set and get arguments');
$parser = new nbCommandLineParser();
$parser->setArguments(array());
$t->isa_ok($parser->getArguments(), 'nbArgumentSet', '->setArguments() sets the parser argument set');

// ->addArguments()
$t->comment('nbCommandLineParserTest - Test add arguments');
$parser = new nbCommandLineParser(array(new nbArgument('foo1')));
$parser->addArguments(new nbArgumentSet(array(new nbArgument('foo2'))));
$t->is($parser->getArguments()->count(), 2, '->addArguments() does not clear the argument set');

// ->setOptions() ->getOptions()
$t->comment('nbCommandLineParserTest - Test set and get options');
$parser = new nbCommandLineParser();
$parser->setOptions(array());
$t->isa_ok($parser->getOptions(), 'nbOptionSet', '->setOptions() sets the parser option set');

// ->addOptions()
$t->comment('nbCommandLineParserTest - Test add options');
$parser = new nbCommandLineParser(array(), array(new nbOption('foo1')));
$parser->addOptions(new nbOptionSet(array(new nbOption('foo2'))));
$t->is($parser->getOptions()->count(), 2, '->addOptions() does not clear the option set');

// ->parse()
$t->comment('nbCommandLineParserTest - Test parse');
$argumentSet = array(
  new nbArgument('foo1', nbArgument::REQUIRED),
  new nbArgument('foo2', nbArgument::OPTIONAL | nbArgument::IS_ARRAY),
);
$optionSet = array(
  new nbOption('foo1', '', nbOption::PARAMETER_NONE),
  new nbOption('foo2', 'f', nbOption::PARAMETER_NONE),
  new nbOption('foo3', '', nbOption::PARAMETER_OPTIONAL, '', 'default3'),
  new nbOption('foo4', '', nbOption::PARAMETER_OPTIONAL, '', 'default4'),
  new nbOption('foo5', '', nbOption::PARAMETER_OPTIONAL, '', 'default5'),
  new nbOption('foo6', 'r', nbOption::PARAMETER_REQUIRED),
  new nbOption('foo7', 't', nbOption::PARAMETER_REQUIRED),
  new nbOption('foo8', '', nbOption::PARAMETER_REQUIRED | nbOption::IS_ARRAY),
  new nbOption('foo9', 's', nbOption::PARAMETER_OPTIONAL, '', 'default9'),
  new nbOption('foo10', 'u', nbOption::PARAMETER_OPTIONAL, '', 'default10'),
  new nbOption('foo11', 'v', nbOption::PARAMETER_OPTIONAL, '', 'default11'),
  new nbOption('foo12', 'w', nbOption::PARAMETER_NONE),
  new nbOption('foo13', 'x', nbOption::PARAMETER_REQUIRED),
);

$parser = new nbCommandLineParser($argumentSet, $optionSet);
$parser->parse('--foo1 -f --foo3 --foo4="foo4" --foo5=foo5 -r "foo6 foo6" -t foo7 --foo8="foo" --foo8=bar -s -u foo10 -v foo11 -w -x foo13 foo1 foo2 foo3 foo4 "foo5 foo5"');
$arguments = array(
  'foo1' => 'foo1',
  'foo2' => array('foo2', 'foo3', 'foo4', 'foo5 foo5')
);
$options = array(
  'foo1' => true,
  'foo2' => true,
  'foo3' => 'default3',
  'foo4' => 'foo4',
  'foo5' => 'foo5',
  'foo6' => 'foo6 foo6',
  'foo7' => 'foo7',
  'foo8' => array('foo', 'bar'),
  'foo9' => 'default9',
  'foo10' => 'foo10',
  'foo11' => 'foo11',
  'foo12' => true,
  'foo13' => 'foo13',
);
$t->ok($parser->isValid(), '->parse() parses CLI options');
$t->is($parser->getOptionValues(), $options, '->parse() parses CLI options');
$t->is($parser->getArgumentValues(), $arguments, '->parse() parses CLI options');

// ->parse
$parser = new nbCommandLineParser($argumentSet, $optionSet);
$parser->parse('foo1 --foo3 --foo4 --foo5');
$arguments = array(
  'foo1' => 'foo1',
  'foo2' => array()
);
$options = array(
  'foo3' => 'default3',
  'foo4' => 'default4',
  'foo5' => 'default5',
//  'foo9' => 'default9', not included in command line
//  'foo10' => 'default10', not included in command line
//  'foo11' => 'default11', not included in command line
);
$t->ok($parser->isValid(), '->parse() parses CLI options');
$t->is($parser->getOptionValues(), $options, '->parse() parses CLI options');
$t->is($parser->getArgumentValues(), $arguments, '->parse() parses CLI options');

// ->getOptionValue()
$t->comment('nbCommandLineParserTest - Test get option value');
foreach($options as $name => $value)
  $t->is($parser->getOptionValue($name), $value, '->getOptionValue() returns the value for the given option name');

try {
  $parser->getOptionValue('undefined');
  $t->fail('->getOptionValue() throws a nbCommandException if the option name does not exist');
}
catch(RangeException $e) {
  $t->pass('->getOptionValue() throws a nbCommandException if the option name does not exist');
}

// ->getArgumentValue()
$t->comment('nbCommandLineParserTest - Test get argument value');
foreach($arguments as $name => $value)
  $t->is($parser->getArgumentValue($name), $value, '->getArgumentValue() returns the value for the given argument name');

try {
  $parser->getArgumentValue('undefined');
  $t->fail('->getArgumentValue() throws a nbCommandException if the argument name does not exist');
}
catch(RangeException $e) {
  $t->pass('->getArgumentValue() throws a nbCommandException if the argument name does not exist');
}

$t->comment('nbCommandLineParserTest - Test has option value');
$t->ok($parser->hasOptionValue('foo3'), '->hasOptionValue() returns true for the option "foo3"');
$t->ok(!$parser->hasOptionValue('foo15'), '->hasOptionValue() returns false for the argument "foo15"');

$t->comment('nbCommandLineParserTest - Test has argument value');
$t->ok($parser->hasArgumentValue('foo1'), '->hasArgumentValue() returns true for the argument "foo1"');
$t->ok(!$parser->hasArgumentValue('foo3'), '->hasArgumentValue() returns false for the argument "foo3"');

// ->isValid() ->getErrors()
$t->comment('nbCommandLineParserTest - Test validity and errors');
$parser = new nbCommandLineParser();
$parser->parse('foo');
$t->ok(!$parser->isValid(), '->isValid() returns false if the arguments are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$arguments = array(new nbArgument('foo', nbArgument::REQUIRED));
$parser = new nbCommandLineParser($arguments);
$parser->parse('');
$t->ok(!$parser->isValid(), '->isValid() returns false if the arguments are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$options = array(new nbOption('foo', '', nbOption::PARAMETER_REQUIRED));
$parser = new nbCommandLineParser(array(), $options);
$parser->parse('--foo');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$options = array(new nbOption('foo', 'f', nbOption::PARAMETER_REQUIRED));
$parser = new nbCommandLineParser(array(), $options);
$parser->parse('-f=bar');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->ok(count($parser->getErrors()) > 1, '->getErrors() returns an array of errors');

$options = array(
  new nbOption('foo', 'f', nbOption::PARAMETER_REQUIRED),
  new nbOption('bar', 'b', nbOption::PARAMETER_OPTIONAL, '', ''),
  new nbOption('cos', 'c', nbOption::PARAMETER_NONE)
);
$parser = new nbCommandLineParser(array(), $options);
$parser->parse('-fb argfoo argbar');
$t->ok($parser->isValid(), '->isValid() returns true if the options are valid');
$t->is($parser->getOptionValue('foo'), 'argfoo', '->getOptionValue() returns "argfoo" if param is "foo"');
$t->is($parser->getOptionValue('bar'), 'argbar', '->getOptionValue() returns "argbar" if param is "bar"');
$parser = new nbCommandLineParser(array(), $options);
$parser->parse('-fb argfoo');
$t->ok($parser->isValid(), '->isValid() returns true if the options are valid');
$t->is($parser->getOptionValue('foo'), 'argfoo', '->getOptionValue() returns "argfoo" if param is "foo"');
$t->is($parser->getOptionValue('bar'), '', '->getOptionValue() returns "" if param is "bar"');

$parser = new nbCommandLineParser(array(), $options);
$parser->parse('-cfb argfoo');
$t->ok($parser->isValid(), '->isValid() returns true if the options are valid');

$t->is($parser->getOptionValue('foo'), 'argfoo', '->getOptionValue() returns "argfoo" if param is "foo"');
$t->is($parser->getOptionValue('bar'), '', '->getOptionValue() returns "" if param is "bar"');
$parser = new nbCommandLineParser(array(), $options);
$parser->parse('-bf argbar');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');

$options = array(new nbOption('foo', 'f', nbOption::PARAMETER_REQUIRED));
$parser = new nbCommandLineParser(array(), $options);
$parser->parse('-f');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$options = array(new nbOption('foo', '', nbOption::PARAMETER_NONE));
$parser = new nbCommandLineParser(array(), $options);
$parser->parse('--foo="bar"');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$parser = new nbCommandLineParser();
$parser->parse('--bar');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$parser = new nbCommandLineParser();
$parser->parse('-b');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$parser = new nbCommandLineParser();
$parser->parse('--bar="foo"');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

// ->getArgumentValue()
$t->comment('nbCommandLineParserTest - Test -- as last option');
$parser = new nbCommandLineParser();
$parser->parse('-- bar');
$t->is($parser->isValid(), true, '->parse() with "--" stops parsing the command line');

$t->comment('nbCommandLineParserTest - Test pass commandline as array');
$argumentSet = array(
  new nbArgument('foo1', nbArgument::REQUIRED),
  new nbArgument('foo2', nbArgument::OPTIONAL | nbArgument::IS_ARRAY),
);
$optionSet = array(
  new nbOption('foo1', '', nbOption::PARAMETER_NONE),
  new nbOption('foo2', 'f', nbOption::PARAMETER_NONE)
);
$parser = new nbCommandLineParser($argumentSet, $optionSet);
$parser->parse(array('foo1Value'));
$t->is($parser->isValid(), true, '->parse() with command line set as array');

$t->comment('nbCommandLineParserTest - Test parse commandline with quote and double quote');
$argumentSet = array(
  new nbArgument('foo1', nbArgument::REQUIRED),
  new nbArgument('foo2', nbArgument::OPTIONAL),
);
$optionSet = array(
  new nbOption('foo1', '', nbOption::PARAMETER_NONE),
  new nbOption('foo2', 'f', nbOption::PARAMETER_NONE)
);
$parser = new nbCommandLineParser($argumentSet, $optionSet);
$parser->parse(array('"foo1\'arg"'));
$t->is($parser->isValid(), true, '->parse() parse with success the commandline "foo1\'arg"');
$t->is($parser->getArgumentValue('foo1'), '"foo1\'arg"', '->getArgumentValue() return "foo1\'arg"');

$parser->parse(array('\'foo1"arg\''));
$t->is($parser->isValid(), true, '->parse() parse with success the commandline \'foo1"arg\'');
$t->is($parser->getArgumentValue('foo1'), '\'foo1"arg\'', '->parse() return \'foo1"arg\'');

$parser->parse(array('\'foo1"arg'));
$t->todo('how is work?');

$t->comment('nbCommandLineParserTest - Test parse an empty commandline');
$argumentSet = array(
  new nbArgument('arg2', nbArgument::OPTIONAL)
);
$optionSet = array(
  new nbOption('opt1', '', nbOption::PARAMETER_OPTIONAL, '', 'defaultvalue')
);
$parser = new nbCommandLineParser($argumentSet, $optionSet);
$parser->parse('');
$t->is($parser->isValid(), true, '->parse() parse with success the empty commandline');
$t->is($parser->hasOptionValue('opt1'), false, '->hasOptionValue() returns "false"');

$parser = new nbCommandLineParser($argumentSet, $optionSet);
$parser->parse('--opt1');
$t->is($parser->hasOptionValue('opt1'), true, '->hasOptionValue() returns "true"');
$t->is($parser->getOptionValue('opt1'), 'defaultvalue', '->getOptionValue() returns "defaultvalue"');

$parser = new nbCommandLineParser($argumentSet, $optionSet);
$parser->parse('--opt1=avalue');
$t->is($parser->hasOptionValue('opt1'), true, '->hasOptionValue() returns "true"');
$t->is($parser->getOptionValue('opt1'), 'avalue', '->getOptionValue() returns "avalue"');

// test config file
$t->comment('nbCommandLineParserTest - Test option --config-file');
$optionSet = array(
  new nbOption('config-file', '',  nbOption::PARAMETER_OPTIONAL, 'Config file option', 'myns-mycommand.yml'),
);
$parser = new nbTestCommandLineParser(array(), $optionSet);

$commandLine = '--config-file';
$parser->parse($commandLine, 'myNS', 'myCommand');
$t->is($parser->isValid(), true, '->parse() with config file option and default value');

$commandLine = ' --config-file=config.yml';
$parser->parse($commandLine, 'myNS', 'myCommand');
$t->is($parser->isValid(), true, '->parse() with config file option and set value');

// test config file errors
$t->comment('nbCommandLineParserTest - Test option --config-file errors');
$optionSet = array(
  new nbOption('config-file', '',  nbOption::PARAMETER_OPTIONAL, 'Config file option', ''),
);
$parser = new nbTestCommandLineParser(array(), $optionSet);

$commandLine = '--config-file';
try {
  $parser->parse($commandLine, 'myNS', 'myCommand');
  $parser->isValid();
  $t->fail('->parse() error when config file has no default value');
}
catch(InvalidArgumentException $e)
{
  $t->pass('->parse() error when config file has no default value');
}

// ->parse()
$t->comment('nbCommandLineParserTest - Test retrieving argument and option from config file passed by option --config-file');
$argumentSet = array(
  new nbArgument('argumentRequired', nbArgument::REQUIRED, 'argument required'),
  new nbArgument('argumentOptional', nbArgument::OPTIONAL, 'argument optional', 'fooOptionalDefault')
);
$optionSet = array(
  new nbOption('config-file',              '',  nbOption::PARAMETER_OPTIONAL, 'Config file option', 'myns-mycommand.yml'),
  new nbOption('optionWithParameter',      'a', nbOption::PARAMETER_OPTIONAL, 'MyPlugin option optionWithParameter', 'barParameterDefault'),
  new nbOption('otherOptionWithParameter', 'b', nbOption::PARAMETER_OPTIONAL, 'MyPlugin option otherOptionWithParameter', 'bar2ParameterDefault'),
  new nbOption('optionDisabledCfg',        'n', nbOption::PARAMETER_NONE, 'MyPlugin option optionDisabledCfg'),
  new nbOption('optionCfg',                'c', nbOption::PARAMETER_NONE, 'MyPlugin option optionCfg')
);
$parser = new nbCommandLineParser($argumentSet, $optionSet);
$parser->setDefaultConfigurationDirs(array(
  nbConfig::get('nb_data_dir') . '/config',
));

$commandLine = ' --config-file';
$parser->parse($commandLine, 'myNS', 'myCommand');
$t->is($parser->isValid(), true, '->parse() success with config file');
$t->is($parser->hasArgumentValue('argumentRequired'), true, '->hasArgumentValue(argumentRequired) returns "true"');
$t->is($parser->getArgumentValue('argumentRequired'), 'fooRequiredCfg', '->getArgumentValue(argumentRequired) returns "fooRequiredCfg"');
$t->is($parser->hasArgumentValue('argumentOptional'), true, '->hasArgumentValue(argOptional) returns "true"');
$t->is($parser->getArgumentValue('argumentOptional'), 'fooOptionalCfg', '->getArgumentValue(argumentOptional) returns "fooOptionalCfg"');
$t->is($parser->hasOptionValue('optionWithParameter'), true, '->hasOptionValue(optionWithParameter) returns "true"');
$t->is($parser->getOptionValue('optionWithParameter'), 'barParameterCfg', '->getOptionValue(optionWithParameter) returns "barParameterCfg"');
$t->is($parser->hasOptionValue('optionDisabledCfg'), false, '->hasOptionValue(optionDisabledCfg) returns "false"');
$t->is($parser->hasOptionValue('optionCfg'), true, '->hasOptionValue(optionCfg) returns "true"');
$t->is($parser->getOptionValue('optionCfg'), true, '->getOptionValue(optionCfg) returns "true"');

$t->comment('nbCommandLineParserTest - Test that argument or options passed by command line override argument and options from config file passed by option --config-file');

$parser->parse(' --config-file --optionWithParameter=foo bar ', 'myNS', 'myCommand');
$t->is($parser->isValid(), true, '->parse() success with config file');
$t->is($parser->hasArgumentValue('argumentRequired'), true, '->hasArgumentValue(argumentRequired) returns "true"');
$t->is($parser->getArgumentValue('argumentRequired'), 'bar', '->getArgumentValue(argumentRequired) returns "bar"');
$t->is($parser->hasOptionValue('optionWithParameter'), true, '->hasOptionValue(optionWithParameter) returns "true"');
$t->is($parser->getOptionValue('optionWithParameter'), 'foo', '->getOptionValue(optionWithParameter) returns "foo"');
$t->is($parser->hasArgumentValue('argumentOptional'), true, '->hasArgumentValue(argOptional) returns "true"');
$t->is($parser->getArgumentValue('argumentOptional'), 'fooOptionalCfg', '->getArgumentValue(argumentOptional) returns "fooOptionalCfg"');
$t->is($parser->hasOptionValue('optionDisabledCfg'), false, '->hasOptionValue(optionDisabledCfg) returns "false"');
$t->is($parser->hasOptionValue('optionCfg'), true, '->hasOptionValue(optionCfg) returns "true"');
$t->is($parser->getOptionValue('optionCfg'), true, '->getOptionValue(optionCfg) returns "true"');


$parser->parse(' --config-file --optionWithParameter=foo --otherOptionWithParameter bar', 'myNS', 'myCommand');
$t->is($parser->hasArgumentValue('argumentRequired'), true, '->hasArgumentValue(argumentRequired) returns "true"');
$t->is($parser->getArgumentValue('argumentRequired'), 'bar', '->getArgumentValue(argumentRequired) returns "bar"');
$t->is($parser->hasOptionValue('optionWithParameter'), true, '->hasOptionValue(optionWithParameter) returns "true"');
$t->is($parser->getOptionValue('optionWithParameter'), 'foo', '->getOptionValue(optionWithParameter) returns "foo"');
$t->is($parser->hasArgumentValue('argumentOptional'), true, '->hasArgumentValue(argOptional) returns "true"');
$t->is($parser->getArgumentValue('argumentOptional'), 'fooOptionalCfg', '->getArgumentValue(argumentOptional) returns "fooOptionalCfg"');
$t->is($parser->hasOptionValue('optionDisabledCfg'), false, '->hasOptionValue(optionDisabledCfg) returns "false"');
$t->is($parser->hasOptionValue('optionCfg'), true, '->hasOptionValue(optionCfg) returns "true"');
$t->is($parser->getOptionValue('optionCfg'), true, '->getOptionValue(optionCfg) returns "true"');
$t->is($parser->hasOptionValue('otherOptionWithParameter'), true, '->hasOptionValue(otherOptionWithParameter) returns "true"');
$t->is($parser->getOptionValue('otherOptionWithParameter'), 'bar2ParameterDefault', '->getOptionValue(otherOptionWithParameter) returns "bar2ParameterDefault"');

$t->comment('nbCommandLineParserTest - Test set/getDefaultConfigurationDirs');
$parser = new nbCommandLineParser();
$dataConfigDir = nbConfig::get('nb_data_dir') . '/config';
$parser->setDefaultConfigurationDirs($dataConfigDir);
$t->is($parser->getDefaultConfigurationDirs(), array($dataConfigDir), 'Default configuration dir is ' . $dataConfigDir);

$t->comment('nbCommandLineParserTest - Test checkDefaultConfigurationDirs
  (only filename is provided (eg.: config.ok.yml))');

$t->is($parser->checkDefaultConfigurationDirs('config.ok.yml'), $dataConfigDir . '/config.ok.yml', 'File was found in ' . $dataConfigDir);
$t->is($parser->checkDefaultConfigurationDirs('config.notexists.yml'), null, 'File doesn\'t exists');

$t->comment('nbCommandLineParserTest - Test checkDefaultConfigurationDirs
  (an absolute or relative filename is provided (eg.: ../../../data/config/config.ok.yml))');

$t->is(
  $parser->checkDefaultConfigurationDirs($dataConfigDir . '/config.ok.yml'),
  $dataConfigDir . '/config.ok.yml',
  'The file exists: ' . $dataConfigDir . '/config.ok.yml');

$t->is($parser->checkDefaultConfigurationDirs($dataConfigDir . '/config.notexists.yml'), null, 'File doesn\'t exists');
$t->is($parser->checkDefaultConfigurationDirs($dataConfigDir), null, 'Only file (not dir) is valid');