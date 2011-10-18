<?php

require_once dirname(__FILE__) . '/../../lib/core/autoload/nbAutoload.php';

$basedir = dirname(__FILE__);

$autoload = nbAutoload::getInstance();
$autoload->register();

$autoload->addDirectory('vendor/', '*.php', true);
$autoload->addDirectory('lib/', '*.php', true);
$autoload->addDirectory('test/lib/', '*.php', true);

$configParser = new nbYamlConfigParser();
$configParser->parseFile(dirname(__FILE__) . '/../../config/config.yml');
$configParser->parseFile(dirname(__FILE__) . '/../config/config.test.yml');

$serviceContainer = new sfServiceContainerBuilder();
$serviceContainer->register('pluginLoader', 'nbPluginLoader')->
  addArgument(nbConfig::get('nb_plugin_dir'))->
  addArgument(new sfServiceReference('commandLoader')
  )->
  setShared(true);

$serviceContainer->register('commandLoader', 'nbCommandLoaderWithReset')->
  setShared(true);

$output = new nbConsoleOutput();
//$output->setFormatter(new nbAnsiColorFormatter());
$logger = nbLogger::getInstance();
$logger->setOutput($output);


$dir = dirname(__FILE__) . '/../sandbox/';
$dirEntries = glob(rtrim($dir, '/') . '/*');

function recursiveDelete($str)
{
  if(is_file($str))
    return @unlink($str);
  else if(is_dir($str)) {
    $scan = glob(rtrim($str, '/') . '/*');

    foreach($scan as $index => $path)
      recursiveDelete($path);

    return @rmdir($str);
  }
}

foreach($dirEntries as $dirEntry)
  recursiveDelete($dirEntry);
