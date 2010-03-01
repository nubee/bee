<?php

require_once dirname(__FILE__) . '/lib/core/autoload/nbAutoload.php';

$autoload = nbAutoload::getInstance();
$autoload->register();

$autoload->addDirectory('lib/', '*.php', true);

$application = new nbBeeApplication();
$command = new nbTestUnitCommand();
$application->setCommands(new nbCommandSet(array($command)));
$application->run();
