<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(5);

$output = new nbStreamOutput();
nbLogger::getInstance()->setOutput($output);

$application = new nbBeeApplication();
$cmd = new nbHelpCommand($application);
$application->setCommands(new nbCommandSet(array(new DummyCommand('dummy1'), $cmd)));

$t->comment('nbHelpCommandTest - Test get name');
$t->is($cmd->getName(), 'help', '->getName() is "help"');

$t->comment('nbHelpCommandTest - Test print command help');
$cmd->run(new nbCommandLineParser(), array('help'));
$t->isnt($output->getStream(), '');

$t->comment('nbHelpCommandTest - Test unknown command');
try {
  $cmd->run(new nbCommandLineParser(), array('cmd'));
  $t->fail('->run() throws an Exception if command is not found');
} catch (Exception $e) {
  $t->pass('->run() throws an Exception if command is not found');
}
$t->is($output->getStream(), '');

$t->comment('nbHelpCommandTest - Test existing command');
$cmd->run(new nbCommandLineParser(), array('dummy1'));
$t->isnt($output->getStream(), '', '->run() prints help for command "dummy1"');
