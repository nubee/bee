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
  $commandSet = new nbCommandSet();
  $commandSet->addCommand(new nbTestUnitCommand());
  $commandSet->addCommand(new nbHelpCommand($application));
  $application->setCommands($commandSet);
  $application->run();
}
catch(Exception $e) {
  if($application)
    $application->renderException($e);
}
