<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(4);

$output = new nbStreamOutput();
$logger = nbLogger::getInstance();
$logger->setOutput($output);


$t->comment('nbBeeApplicationTest - Test constructor');
$application = new nbBeeApplication($serviceContainer);
$application->run('-V');

$t->is($application->getName(), 'bee', '->getName() is "bee"');
$t->is($application->getVersion(), '0.1.0', '->getVersion() is "0.1.0"');
$t->is($output->getStream(), '0.1.0', '->run() outputs application version');

$application->run('-vV');
$t->is($output->getStream(), 'bee version 0.1.0', '->run() outputs long application version');


