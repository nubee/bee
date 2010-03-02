<?php

require_once(dirname(__FILE__).'/../../../bootstrap/unit.php');

$t = new lime_test(48);

// __construct()
$t->comment('nbCommandLineParserTest - Test constructor');
$arguments = new nbArgumentSet();
$options = new nbOptionSet();

$parser = new nbCommandLineParser();
$t->isa_ok($parser->getArguments(), 'nbArgumentSet', '__construct() creates a new nbArgumentsSet if none given');
$t->isa_ok($parser->getOptions(), 'nbOptionSet', '__construct() creates a new nbOptionSet if none given');

$parser = new nbCommandLineParser($arguments);
$t->is($parser->getArguments(), $arguments, '__construct() takes a nbArgumentSet as its first argument');
$t->isa_ok($parser->getArguments(), 'nbArgumentSet', '__construct() takes a nbArgumentSet as its first argument');

$parser = new nbCommandLineParser($arguments, $options);
$t->isa_ok($parser->getOptions(), 'nbOptionSet', '__construct() takes a nbOptionSet as its second argument');
$t->is($parser->getOptions(), $options, '__construct() can take a nbOptionSet as its second argument');

// ->setArguments() ->getArguments()
$t->comment('nbCommandLineParserTest - Test set and get arguments');
$parser = new nbCommandLineParser();
$arguments = new nbArgumentSet();
$parser->setArguments($arguments);
$t->is($parser->getArguments(), $arguments, '->setArguments() sets the manager argument set');

// ->addArguments()
$t->comment('nbCommandLineParserTest - Test add arguments');
$arguments = new nbArgumentSet(array(new nbArgument('foo1', nbArgument::REQUIRED)));
$parser = new nbCommandLineParser($arguments);
$parser->addArguments(new nbArgumentSet(array(new nbArgument('foo2', nbArgument::REQUIRED))));
$t->is($parser->getArguments()->count(), 2, '->addArguments() does not clear the argument set');

// ->setOptions() ->getOptions()
$t->comment('nbCommandLineParserTest - Test set and get options');
$parser = new nbCommandLineParser();
$options = new nbOptionSet();
$parser->setOptions($options);
$t->is($parser->getOptions(), $options, '->setOptions() sets the manager option set');

// ->addOptions()
$t->comment('nbCommandLineParserTest - Test add options');
$options = new nbOptionSet(array(new nbOption('foo1')));
$parser = new nbCommandLineParser(null, $options);
$parser->addOptions(new nbOptionSet(array(new nbOption('foo2'))));
$t->is($parser->getOptions()->count(), 2, '->addOptions() does not clear the option set');

// ->parse()
$t->comment('nbCommandLineParserTest - Test parse');
$argumentSet = new nbArgumentSet(array(
  new nbArgument('foo1', nbArgument::REQUIRED),
  new nbArgument('foo2', nbArgument::OPTIONAL | nbArgument::IS_ARRAY),
));
$optionSet = new nbOptionSet(array(
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
));
$parser = new nbCommandLineParser($argumentSet, $optionSet);
$parser->parse('--foo1 -f --foo3 --foo4="foo4" --foo5=foo5 -r"foo6 foo6" -t foo7 --foo8="foo" --foo8=bar -s -u foo10 -vfoo11 -wx foo13 foo1 foo2 foo3 foo4 "foo5 foo5"');
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
$t->ok($parser->isValid(), '->parse() parsees CLI options');
$t->is($parser->getOptionValues(), $options, '->parse() parsees CLI options');
$t->is($parser->getArgumentValues(), $arguments, '->parse() parsees CLI options');

// ->parse
$parser = new nbCommandLineParser($argumentSet, $optionSet);
$parser->parse('foo1');
$arguments = array(
  'foo1' => 'foo1',
  'foo2' => array()
);
$options = array(
  'foo3' => 'default3',
  'foo4' => 'default4',
  'foo5' => 'default5',
  'foo9' => 'default9',
  'foo10' => 'default10',
  'foo11' => 'default11',
);
$t->ok($parser->isValid(), '->parse() parsees CLI options');
$t->is($parser->getOptionValues(), $options, '->parse() parsees CLI options');
$t->is($parser->getArgumentValues(), $arguments, '->parse() parsees CLI options');

// ->getOptionValue()
$t->comment('nbCommandLineParserTest - Test get option value');
foreach ($options as $name => $value)
  $t->is($parser->getOptionValue($name), $value, '->getOptionValue() returns the value for the given option name');

try {
  $parser->getOptionValue('undefined');
  $t->fail('->getOptionValue() throws a nbCommandException if the option name does not exist');
}
catch (RangeException $e) {
  $t->pass('->getOptionValue() throws a nbCommandException if the option name does not exist');
}

// ->getArgumentValue()
$t->comment('nbCommandLineParserTest - Test get argument value');
foreach ($arguments as $name => $value)
  $t->is($parser->getArgumentValue($name), $value, '->getArgumentValue() returns the value for the given argument name');

try {
  $parser->getArgumentValue('undefined');
  $t->fail('->getArgumentValue() throws a nbCommandException if the argument name does not exist');
}
catch (RangeException $e) {
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
$arguments = new nbArgumentSet();
$parser = new nbCommandLineParser($arguments);
$parser->parse('foo');
$t->ok(!$parser->isValid(), '->isValid() returns false if the arguments are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$arguments = new nbArgumentSet(array(new nbArgument('foo', nbArgument::REQUIRED)));
$parser = new nbCommandLineParser($arguments);
$parser->parse('');
$t->ok(!$parser->isValid(), '->isValid() returns false if the arguments are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$options = new nbOptionSet(array(new nbOption('foo', '', nbOption::PARAMETER_REQUIRED)));
$parser = new nbCommandLineParser(null, $options);
$parser->parse('--foo');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$options = new nbOptionSet(array(new nbOption('foo', 'f', nbOption::PARAMETER_REQUIRED)));
$parser = new nbCommandLineParser(null, $options);
$parser->parse('-f');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$options = new nbOptionSet(array(new nbOption('foo', '', nbOption::PARAMETER_NONE)));
$parser = new nbCommandLineParser(null, $options);
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
$argumentSet = new nbArgumentSet(array(
  new nbArgument('foo1', nbArgument::REQUIRED),
  new nbArgument('foo2', nbArgument::OPTIONAL | nbArgument::IS_ARRAY),
));
$optionSet = new nbOptionSet(array(
  new nbOption('foo1', '', nbOption::PARAMETER_NONE),
  new nbOption('foo2', 'f', nbOption::PARAMETER_NONE)
));
$parser = new nbCommandLineParser($argumentSet, $optionSet);
$parser->parse(array('foo1Value'));
$t->is($parser->isValid(), true, '->parse() with command line set as array');
