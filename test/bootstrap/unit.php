<?php

require_once dirname(__FILE__) . '/../../lib/core/autoload/nbAutoload.php';

$autoload = nbAutoload::getInstance();
$autoload->register();

$autoload->addDirectory('vendor/', '*.php', true);
$autoload->addDirectory('lib/', '*.php', true);
$autoload->addDirectory('test/lib/', '*.php', true);

// Configures bee variables
$configParser = new nbYamlConfigParser();
nbConfig::set('nb_bee_dir', dirname(__FILE__) . '/../..');
nbConfig::set('nb_config_dir', nbConfig::get('nb_bee_dir') . '/config');
nbConfig::set('nb_test_config_dir', dirname(__FILE__) . '/../config/');

$configParser->parseFile(nbConfig::get('nb_config_dir') . '/config.yml', '', true);
$configParser->parseFile(nbConfig::get('nb_test_config_dir') . '/config.yml', '', true);


$serviceContainer = new sfServiceContainerBuilder();
$serviceContainer->register('pluginLoader', 'nbPluginLoader')->
  addArgument(nbConfig::get('nb_plugins_dir'))->
  addArgument(new sfServiceReference('commandLoader'))->
  setShared(true);

$serviceContainer->register('commandLoader', 'nbCommandLoaderWithReset')->
  setShared(true);

$output = new nbConsoleOutput();
$logger = nbLogger::getInstance();
$logger->setOutput($output);
