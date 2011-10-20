<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';

$fs = nbFileSystem::getInstance();
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbVersionPlugin'));

$versionFile = nbConfig::get('test_version-file');
$fs->copy($versionFile, $versionFile . '.original');

$build = getBuildVersion($versionFile);

$t = new lime_test(2);
$cmd = new nbUpdateBuildVersionCommand();
$commandLine = $versionFile;
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command run succefully');

$t->is(getBuildVersion($versionFile), $build + 1, 'Build version incremented');
$fs->move($versionFile . '.original', $versionFile);

function getBuildVersion($versionFile) {
  if (file_exists($versionFile)) {
    $configParser = new nbYamlConfigParser();
    $configParser->parseFile($versionFile);
    $version = nbConfig::get('version');
    $arrayVersion = array();
    $arrayVersion = preg_split('/\./', $version);
    return $arrayVersion[3];
  }
}