<?php

require_once dirname(__FILE__) . '/lib/core/autoload/nbAutoload.php';

$autoload = nbAutoload::getInstance();
$autoload->register();
$autoload->addDirectory(dirname(__FILE__) . '/lib/', '*.php', true);
$autoload->addDirectory(dirname(__FILE__) . '/vendor/', '*.php', true);

$beeDir = nbFileSystem::sanitizeDir(dirname(__FILE__));

nbConfig::set('nb_bee_dir', $beeDir);

if('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
  nbConfig::set('nb_user_dir', getenv('APPDATA') . '/.bee');
}
else {
  nbConfig::set('nb_user_dir', getenv('HOME') . '/.bee');
}
nbConfig::set('nb_user_config', nbConfig::get('nb_user_dir') . '/config.yml');

$yaml = new nbYamlConfigParser();
$yaml->parseFile(nbConfig::get('nb_bee_dir') . '/config/config.yml', '', true);

if(file_exists(nbConfig::get('nb_user_config')))
  $yaml->parseFile(nbConfig::get('nb_user_config'), '', true);

if(file_exists('.bee/config.yml'))
  $yaml->parseFile('.bee/config.yml', '', true);

if(file_exists('./.bee/' . nbConfig::get('nb_project_config')))
  $projectConfigurationFile = './.bee/' . nbConfig::get('nb_project_config');
else if(file_exists('./' . nbConfig::get('nb_project_config')))
  $projectConfigurationFile = './' . nbConfig::get('nb_project_config');
else if(file_exists(nbConfig::get('nb_bee_dir') . '/' . nbConfig::get('nb_project_config')))
  $projectConfigurationFile = nbConfig::get('nb_bee_dir') . '/' . nbConfig::get('nb_project_config');

$yaml->parseFile($projectConfigurationFile, '', true);

/* * ********************* */
sfServiceContainerAutoloader::register();

$serviceContainer = new sfServiceContainerBuilder();

$serviceContainer->
  register('pluginLoader', 'nbPluginLoader')->
  addArgument(nbConfig::get('nb_plugins_dir'))->
  addArgument(new sfServiceReference('commandLoader'))->
  setShared(true)
;

$serviceContainer->
  register('commandLoader', 'nbCommandLoader')->
  setShared(true)
;

$output = new nbConsoleOutput();
$output->setFormatter(nbConfig::get('nb_output_color', 'false') == 'true' ? new nbAnsiColorFormatter() : new nbFormatter());
$logger = nbLogger::getInstance();
$logger->setOutput($output);

/* * ********************* */
if(nbConfig::has('project_bee_plugins_dir'))
  $serviceContainer->pluginLoader->addDir(nbConfig::get('project_bee_plugins_dir'));

// Loads default plugins from path/to/bee/config/config.yml
if(!$defaultPlugins = nbConfig::get('nb_default_plugins'))
  $defaultPlugins = array();
else
  $serviceContainer->pluginLoader->loadPlugins($defaultPlugins);

//loads project plugins from project/path/bee.yml
if(nbConfig::has('project_bee_enabled_plugins')) {
  $plugins = nbConfig::get('project_bee_enabled_plugins');

  (null === $plugins) ?
      $serviceContainer->pluginLoader->loadAllPlugins() :
      $serviceContainer->pluginLoader->loadPlugins($plugins);
}

$autoload->addDirectory(nbConfig::get('nb_command_dir'), 'Command.php', true);

$serviceContainer->commandLoader->loadCommands();
$serviceContainer->commandLoader->loadCommandAliases();

try {
  $application = new nbBeeApplication($serviceContainer);
  $application->run();
}
catch(Exception $e) {
  if($application)
    $application->renderException($e);
  $statusCode = $e->getCode();
  exit(is_numeric($statusCode) && $statusCode ? $statusCode : 1);
}
