<?php

require_once dirname(__FILE__) . '/../../../../../test/bootstrap/unit.php';

nbPluginLoader::getInstance()->loadPlugins(array('nbSubversion'));

$t = new lime_test(16);

$client = new nbSvnClient();

$t->comment('nbSvnClientTest - Test mkdir command generation');
$t->is($client->getMkdirCmdLine('Projects/bee', 'msg'),
  'svn mkdir "Projects/bee" --message "msg"',
  '->getMkdirCmdLine() is "svn mkdir "Projects/bee" --message "msg" "');
$t->is($client->getMkdirCmdLine('Projects/bee', 'msg', 'anuser', 'apass'),
  'svn mkdir "Projects/bee" --message "msg" --username anuser --password apass',
  '->getMkdirCmdLine() is "svn mkdir "Projects/bee" --message "msg" --username anuser --password apass"');

$t->comment('nbSvnClientTest - Test del command generation');
$t->is($client->getDelCmdLine('Projects/bee', 'msg'),
  'svn del "Projects/bee" --message "msg"',
  '->getDelCmdLine() is "svn del "Projects/bee" --message "msg" "');
$t->is($client->getDelCmdLine('Projects/bee', 'msg', 'anuser', 'apass'),
  'svn del "Projects/bee" --message "msg" --username anuser --password apass',
  '->getDelCmdLine() is "svn del "Projects/bee" --message "msg" --username anuser --password apass"');

$t->comment('nbSvnClientTest - Test import command generation');
$t->is($client->getImportCmdLine('Projects/bee', 'svn://svn_root/Projects/bee', 'msg'),
  'svn import "Projects/bee" "svn://svn_root/Projects/bee" --message "msg"',
  '->getImportCmdLine() is "svn import "Projects/bee" "svn://svn_root/Projects/bee" --message "msg" "');
$t->is($client->getImportCmdLine('Projects/bee', 'svn://svn_root/Projects/bee', 'msg', 'anuser', 'apass'),
  'svn import "Projects/bee" "svn://svn_root/Projects/bee" --message "msg" --username anuser --password apass',
  '->getImportCmdLine() is "svn import "Projects/bee" "svn://svn_root/Projects/bee" --message "msg" --username anuser --password apass"');

$t->comment('nbSvnClientTest - Test checkout command generation');
$t->is($client->getCheckoutCmdLine('svn://svn_root/Projects/bee', 'Projects/bee/dir/dir'),
  'svn checkout "svn://svn_root/Projects/bee" "Projects/bee/dir/dir"',
  '->getCheckoutCmdLine() is "svn checkout "svn://svn_root/Projects/bee" "Projects/bee/dir/dir" "');
$t->is($client->getCheckoutCmdLine('svn://svn_root/Projects/bee', 'Projects/bee/dir/dir', true),
  'svn checkout "svn://svn_root/Projects/bee" "Projects/bee/dir/dir" --force',
  '->getCheckoutCmdLine() is "svn checkout "svn://svn_root/Projects/bee" "Projects/bee/dir/dir" --force"');
$t->is($client->getCheckoutCmdLine('svn://svn_root/Projects/bee', 'Projects/bee/dir/dir', false, 'anuser', 'apass'),
  'svn checkout "svn://svn_root/Projects/bee" "Projects/bee/dir/dir" --username anuser --password apass',
  '->getCheckoutCmdLine() is "svn checkout "svn://svn_root/Projects/bee" "Projects/bee/dir/dir" --username anuser --password apass"');
$t->is($client->getCheckoutCmdLine('svn://svn_root/Projects/bee', 'Projects/bee/dir/dir', true, 'anuser', 'apass'),
  'svn checkout "svn://svn_root/Projects/bee" "Projects/bee/dir/dir" --force --username anuser --password apass',
  '->getCheckoutCmdLine() is "svn checkout "svn://svn_root/Projects/bee" "Projects/bee/dir/dir" --force --username anuser --password apass"');

$t->comment('nbSvnClientTest - Test commit command generation');
$t->is($client->getCommitCmdLine('Projects/bee', 'msg'),
  'svn commit "Projects/bee" --message "msg"',
  '->getCommitCmdLine() is "svn commit "Projects/bee" --message "msg" "');
$t->is($client->getCommitCmdLine('Projects/bee', 'msg', 'anuser', 'apass'),
  'svn commit "Projects/bee" --message "msg" --username anuser --password apass',
  '->getCommitCmdLine() is "svn commit "Projects/bee" --message "msg" --username anuser --password apass"');

$t->comment('nbSvnClientTest - Test update command generation');
$t->is($client->getUpdateCmdLine('Projects/bee'),
  'svn update "Projects/bee"',
  '->getUpdateCmdLine() is "svn update "Projects/bee" "');
$t->is($client->getUpdateCmdLine('Projects/bee', 'anuser', 'apass'),
  'svn update "Projects/bee" --username anuser --password apass',
  '->getUpdateCmdLine() is "svn update "Projects/bee" --username anuser --password apass"');

$t->comment('nbSvnClientTest - Test status command generation');
$t->is($client->getStatusCmdLine('Projects/bee'),
  'svn status "Projects/bee"',
  '->getStatusCmdLine() is "svn status "Projects/bee" "');
$t->is($client->getStatusCmdLine('Projects/bee', 'anuser', 'apass'),
  'svn status "Projects/bee" --username anuser --password apass',
  '->getStatusCmdLine() is "svn status "Projects/bee" --username anuser --password apass"');

//$client->propset('svn:ignore', "*.suo\n*.ncb\n*.anuser", 'Projects/bee');
//$t->is($executor->command,
//       'svn propset "svn://svn_root/Projects/bee" --username anusername --password apassword',
//       'Test propset svn:ignore "" with login info');
