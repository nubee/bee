<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(3);

$output = new nbStreamOutput();
nbLogger::getInstance()->setOutput($output);

$application = new DummyApplication();
$command = new nbHelpCommand();
$command->setApplication($application);
$application->setCommands(new nbCommandSet(array(new DummyCommand('dummy1'), $command)));

$t->comment('nbHelpCommandTest - Test get name');
$t->is($command->getName(), 'help', '->getName() is "help"');

//$t->comment('nbHelpCommandTest - Test print command help');
//$command->run(new nbCommandLineParser(), array('help'));
//$t->ok($application->executedFormatHelpString, '->run() called nbApplication::formatHelpString()');
//$application->executedFormatHelpString = false;

$t->comment('nbHelpCommandTest - Test unknown command');
try {
  $command->run(new nbCommandLineParser(), array('cmd'));
  $t->fail('->run() throws an Exception if command is not found');
} catch (Exception $e) {
  $t->pass('->run() throws an Exception if command is not found');
}
$t->ok(!$application->executedFormatHelpString, '->run() didn\'t call nbApplication::formatHelpString()');

//$t->comment('nbHelpCommandTest - Test existing command');
//$command->run(new nbCommandLineParser(), array('dummy1'));
//$t->ok($application->executedFormatHelpString, '->run() called nbApplication::formatHelpString()');
