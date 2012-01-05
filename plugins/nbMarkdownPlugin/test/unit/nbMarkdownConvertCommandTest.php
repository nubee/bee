<?php

require_once dirname(__FILE__) . '/../../../../test/bootstrap/unit.php';
$serviceContainer->pluginLoader->loadPlugins(array('nbMarkdownPlugin'));

$fs = nbFileSystem::getInstance();
$sourceDir = dirname(__FILE__) . '/../data';
$destinationDir = dirname(__FILE__) . '/../sandbox';

$fs->mkdir($destinationDir);

$t = new lime_test(4);
$t->comment('Markdown Convert');

$t->comment('  1. - Convert simple markdown file');
$cmd = new nbMarkdownConvertCommand();

$sourceFile = $sourceDir . '/simple.md';
$destinationFile = $destinationDir . '/simple.html';

$commandLine = sprintf('%s %s', $sourceFile, $destinationFile);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Markdown simple file has been converted');
$t->ok(file_exists($destinationFile), 'Destination file exists');


$t->comment('  2. - Convert markdown extra file');
$cmd = new nbMarkdownConvertCommand();

$sourceFile = $sourceDir . '/extra.md';
$destinationFile = $destinationDir . '/extra.html';

$commandLine = sprintf('%s %s', $sourceFile, $destinationFile);
$t->ok($cmd->run(new nbCommandLineParser(), $commandLine), 'Markdown extra file has been converted');
$t->ok(file_exists($destinationFile), 'Destination file exists');

// Tear down
$fs->rmdir($destinationDir, true);
