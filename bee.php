<?php

require_once dirname(__FILE__) . '/lib/core/autoload/nbAutoload.php';

$autoload = nbAutoload::getInstance();
$autoload->register();
$autoload->addDirectory(dirname(__FILE__) . '/lib/', '*.php', true);
$autoload->addDirectory(dirname(__FILE__) . '/vendor/', '*.php', true);

nbConfig::set('nb_bee_dir',dirname(__FILE__));

if('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
  nbConfig::set('nb_user_dir',getenv('APPDATA').'/.bee');
} else {
  nbConfig::set('nb_user_dir',getenv('HOME').'/.bee/');
}
nbConfig::set('nb_user_config',nbConfig::get('nb_user_dir').'/config.yml');


$yaml = new nbYamlConfigParser();
$yaml->parseFile(nbConfig::get('nb_bee_dir') . '/config/config.yml');

if(file_exists(nbConfig::get('nb_user_config')))
  $yaml->parseFile(nbConfig::get('nb_user_config'));

if(file_exists(nbConfig::get('nb_project_config')))
  $yaml->parseFile(nbConfig::get('nb_project_config'));

if(! $default_plugins = nbConfig::get('nb_default_plugins'))
  $default_plugins = array();
else
  nbPluginLoader::getInstance()->loadPlugins($default_plugins);

if(nbConfig::has('proj_bee_plugins')) {
  $plugins = nbConfig::get('proj_bee_plugins');

  (null === $plugins)?
    nbPluginLoader::getInstance()->loadAllPlugins() :
    nbPluginLoader::getInstance()->loadPlugins($plugins);
}


$autoload->addDirectory(nbConfig::get('nb_command_dir'), 'Command.php', true);
$output = new nbConsoleOutput();
$output->setFormatter(new nbAnsiColorFormatter());
$logger = nbLogger::getInstance();
$logger->setOutput($output);

try {
  $application = new nbBeeApplication();

  $application->run();
}
catch(Exception $e) {
  if($application)
    $application->renderException($e);
  $statusCode = $e->getCode();
  exit(is_numeric($statusCode) && $statusCode ? $statusCode : 1);
}
