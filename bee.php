<?php

{
//BOOTSTRAP
  require_once dirname(__FILE__) . '/lib/core/autoload/nbAutoload.php';

  $autoload = nbAutoload::getInstance();
  $autoload->register();
  $autoload->addDirectory(dirname(__FILE__).'/lib/', '*.php', true);
  $autoload->addDirectory(dirname(__FILE__).'/vendor/', '*.php', true);

  $yaml = new nbYamlConfigurationParser();
  $yaml->parseFile(dirname(__FILE__).'/config/config.yml');
  if(file_exists(nbConfiguration::get('project_configuration_file')))
    $yaml->parseFile(nbConfiguration::get('project_configuration_file'));

  $autoload->addDirectory(nbConfiguration::get('bee_command_dir'), 'Command.php', true);
}

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
