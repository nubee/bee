<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

$cloneFrom = nbConfig::get('symfony_project-clone_from');
$cloneTo = nbConfig::get('symfony_project-clone_to');
$name = nbConfig::get('symfony_project-clone_name');

$t = new lime_test(5);
$t->comment('Symfony Project Clone');

$cmd = new nbSymfonyCloneProjectCommand();

$commandLine = sprintf('%s %s %s', $cloneFrom, $cloneTo, $name);

$finder = nbFileFinder::create('any');
$appFiles = $finder->add('*.*')->remove('.')->remove('..')->relative()->in($cloneFrom);

$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Symfony project cloned successfully');
$t->ok(file_exists($cloneTo . '/' . $name), 'project is cloned');

//TODO rivedere directory cache e il test in generale 20???
$t->comment('Total files are 46 with cache and log directories');
$clonedAppFiles = $finder->add('*.*')->remove('.')->remove('..')->relative()->in($cloneTo . '/' . $name);
$t->is(count($clonedAppFiles), 46, 'All files (cache and log are excluded) are cloned');

$fsr = new File_SearchReplace($name, '', $cloneTo . '/' . $name . '/symfony/config/properties.ini', '', false);
$fsr->doSearch();
$t->is($fsr->getNumOccurences(), 1, 'File properties.ini modified');

$fsr = new File_SearchReplace($name, '', $cloneTo . '/' . $name . '/symfony/config/databases.yml', '', false);
$fsr->doSearch();
$t->is($fsr->getNumOccurences(), 2, 'File databases.yml modified');

$fileSystem->rmdir($cloneTo . '/' . $name, true);
