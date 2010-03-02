<?php

require_once dirname(__FILE__) . '/lib/core/autoload/nbAutoload.php';

$autoload = nbAutoload::getInstance();
$autoload->register();

$autoload->addDirectory('lib/', '*.php', true);

$output = new nbConsoleOutput();
$output->setFormatter(new nbAnsiColorFormatter());
$logger = nbLogger::getInstance();
$logger->setOutput($output);

try {
  $application = new nbBeeApplication();
  $command = new nbTestUnitCommand();
  $application->setCommands(new nbCommandSet(array($command)));
  $application->run();
}
catch(Exception $e) {
  if($application)
    $application->renderException($e);
}
