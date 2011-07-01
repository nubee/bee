<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$configParser->parseFile(dirname(__FILE__) . '/../config/config.yml');
$serviceContainer->pluginLoader->loadPlugins(array('nbSymfonyPlugin'));

$cloneFrom = nbConfig::get('symfony_project-clone_from');
$cloneTo = nbConfig::get('symfony_project-clone_to');
$name = nbConfig::get('symfony_project-clone_name');

$t = new lime_test(5);

$cmd = new nbSymfonyCloneProjectCommand();

echo $commandLine = $cloneFrom . ' ' . $cloneTo . ' ' . $name . "\n";

$finder = nbFileFinder::create('any');
$appFiles = $finder->add('*.*')->remove('.')->remove('..')->relative()->in($cloneFrom);

$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Command SymfonyCloneProject called succefully');
$t->ok(file_exists($cloneTo . '/' . $name), 'project is cloned');
//TODO rivedere directory cache e il test in generale 20???
$clonedAppFiles = $finder->add('*.*')->remove('.')->remove('..')->relative()->in($cloneTo . '/' . $name);
$t->is(count(array_diff($appFiles, $clonedAppFiles)), 20, 'All files are cloned');

$fsr = new File_SearchReplace($name, '', $cloneTo . '/' . $name . '/trunk/symfony/config/properties.ini', '', false);
$fsr->doSearch();
$t->is($fsr->getNumOccurences(), 1, 'properties.ini modified');

$fsr = new File_SearchReplace($name, '', $cloneTo . '/' . $name . '/trunk/symfony/config/databases.yml', '', false);
$fsr->doSearch();
$t->is($fsr->getNumOccurences(), 2, 'databases.yml modified');

cleanDir($cloneTo . '/' . $name);

function cleanDir($dir) {
  $finder = nbFileFinder::create('any');
  $files = $finder->add('*.*')->remove('.')->remove('..')->in($dir);
  $files = array_reverse($files);
  foreach ($files as $file)
    if (is_dir($file))
      nbFileSystem::rmdir($file, true);
    else
      nbFileSystem::delete($file);
  nbFileSystem::rmdir($dir);
}