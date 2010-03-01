<?php

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(43);

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

// ->setOptions() ->getOptions()
$t->comment('nbCommandLineParserTest - Test set and get options');
$parser = new nbCommandLineParser();
$options = new nbOptionSet();
$parser->setOptions($options);
$t->is($parser->getOptions(), $options, '->setOptions() sets the manager option set');

// ->process()
$t->comment('nbCommandLineParserTest - Test process');
$arguments = new nbArgumentSet(array(
  new nbArgument('foo1', nbArgument::REQUIRED),
  new nbArgument('foo2', nbArgument::OPTIONAL | nbArgument::IS_ARRAY),
));
$options = new nbOptionSet(array(
  new nbOption('foo1', '', nbOption::PARAMETER_NONE),
  new nbOption('foo2', 'f', nbOption::PARAMETER_NONE),
  new nbOption('foo3', '', nbOption::PARAMETER_OPTIONAL, '', 'default3'),
  new nbOption('foo4', '', nbOption::PARAMETER_OPTIONAL, '', 'default4'),
  new nbOption('foo5', '', nbOption::PARAMETER_OPTIONAL, '', 'default5'),
  new nbOption('foo6', 'r', nbOption::PARAMETER_REQUIRED, '', 'default5'),
  new nbOption('foo7', 't', nbOption::PARAMETER_REQUIRED, '', 'default7'),
  new nbOption('foo8', '', nbOption::PARAMETER_REQUIRED | nbOption::IS_ARRAY),
  new nbOption('foo9', 's', nbOption::PARAMETER_OPTIONAL, '', 'default9'),
  new nbOption('foo10', 'u', nbOption::PARAMETER_OPTIONAL, '', 'default10'),
  new nbOption('foo11', 'v', nbOption::PARAMETER_OPTIONAL, '', 'default11'),
));
$parser = new nbCommandLineParser($arguments, $options);
$parser->process('--foo1 -f --foo3 --foo4="foo4" --foo5=foo5 -r"foo6 foo6" -t foo7 --foo8="foo" --foo8=bar -s -u foo10 -vfoo11 foo1 foo2 foo3 foo4');
$arguments = array(
  'foo1' => 'foo1',
  'foo2' => array('foo2', 'foo3', 'foo4')
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
);
$t->ok($parser->isValid(), '->process() processes CLI options');
$t->is($parser->getOptionValues(), $options, '->process() processes CLI options');
$t->is($parser->getArgumentValues(), $arguments, '->process() processes CLI options');

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

// ->isValid() ->getErrors()
$t->comment('nbCommandLineParserTest - Test validity and errors');
$arguments = new nbArgumentSet();
$parser = new nbCommandLineParser($arguments);
$parser->process('foo');
$t->ok(!$parser->isValid(), '->isValid() returns false if the arguments are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$arguments = new nbArgumentSet(array(new nbArgument('foo', nbArgument::REQUIRED)));
$parser = new nbCommandLineParser($arguments);
$parser->process('');
$t->ok(!$parser->isValid(), '->isValid() returns false if the arguments are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$options = new nbOptionSet(array(new nbOption('foo', '', nbOption::PARAMETER_REQUIRED)));
$parser = new nbCommandLineParser(null, $options);
$parser->process('--foo');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$options = new nbOptionSet(array(new nbOption('foo', 'f', nbOption::PARAMETER_REQUIRED)));
$parser = new nbCommandLineParser(null, $options);
$parser->process('-f');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$options = new nbOptionSet(array(new nbOption('foo', '', nbOption::PARAMETER_NONE)));
$parser = new nbCommandLineParser(null, $options);
$parser->process('--foo="bar"');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$parser = new nbCommandLineParser();
$parser->process('--bar');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$parser = new nbCommandLineParser();
$parser->process('-b');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

$parser = new nbCommandLineParser();
$parser->process('--bar="foo"');
$t->ok(!$parser->isValid(), '->isValid() returns false if the options are not valid');
$t->is(count($parser->getErrors()), 1, '->getErrors() returns an array of errors');

// ->getArgumentValue()
$t->comment('nbCommandLineParserTest - Test -- as last option');
$parser = new nbCommandLineParser();
$parser->process('-- bar');
$t->is($parser->isValid(), true, '->process() with "--" stops parsing the command line');
