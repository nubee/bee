<?php

require_once dirname(__FILE__) . '/../../bootstrap/unit.php';

$t = new lime_test(2);

$output = new nbStreamOutput();

$cmd = new nbHelpCommand();
$cmd->setOutput($output);

$t->is($cmd->getName(), 'help', '->getName() is "help"');

//print_r($argv);

$cmd->run(new nbCommandLineParser(), '');
$t->is($output->getStream(), 'help');
