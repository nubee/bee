<?php

require_once dirname(__FILE__) . '/../bootstrap/unit.php';

//setup
$fileSystem->mkdir(nbConfig::get('archive_archive-dir_destination-dir'), true);
$fileSystem->mkdir(nbConfig::get('app_prod_dir'), true);

if(php_uname('s') == 'Linux')
{
  $t = new lime_test(3);
  $t->comment('Website deploy');

  $cmd = new nbWebsiteDeployCommand();

  $parser = new nbCommandLineParser();
  $parser->setDefaultConfigurationDirs(array(dirname(__FILE__) . '/../data/config'));

  $commandLine = '--config-file --doit';
  $t->ok($cmd->run($parser, $commandLine), 'Website deployed successfully');

  $t->ok(is_file(nbConfig::get('app_prod_dir').'/filetoSync1'), 'File deployed successfully');
  $t->ok(is_file(nbConfig::get('app_prod_dir').'/filetoSync2'), 'File deployed successfully');
}
else
{
  $t = new lime_test(1);
  $t->comment('Website deploy');

  $cmd = new nbWebsiteDeployCommand();

  $parser = new nbCommandLineParser();
  $parser->setDefaultConfigurationDirs(array(dirname(__FILE__) . '/../data/config'));

  $commandLine = '--config-file';
  $t->ok($cmd->run($parser, $commandLine), 'Website deployed successfully');
}

//tear down
$fileSystem->rmdir(nbConfig::get('archive_archive-dir_destination-dir'), true);
$fileSystem->rmdir(nbConfig::get('app_prod_dir'), true);
