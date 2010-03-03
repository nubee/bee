<?php

require_once dirname(__FILE__) . '/../../../bootstrap/unit.php';

$t = new lime_test(7);

$shell = new nbShell();

$t->comment('nbShellTest - Test execute');
$ret = $shell->execute('dir');
$t->ok($ret);

$t->comment('nbShellTest - Test execute retriving output');
$out = array();
$ret = $shell->execute('dir', $out);
$t->ok($ret);
$t->ok(count($out) > 0);

$t->comment('nbShellTest - Test execute unknown command');
$ret = $shell->execute('unknown_command');
$t->ok($ret === false);

$t->comment('nbShellTest - Test error executing command');
$ret = $shell->execute('dir /e');
$t->ok($ret === false);

$t->comment('nbShellTest - Test redirect stderr to stdout');
$out = array();
$ret = $shell->execute('dir /e', $out);
$t->ok($ret === false);
$t->ok(count($out) > 0);


//$output = array();
//$ret = $shell->execute('svn co', $output);
//print_r($output);
//$stderr = '';
//$stderr = fopen("php://stdout","r");
//while (true)
//{
//  $str = fread($stderr, 256);
//  echo "STR: $str";
//  if ($str != false)
//    $stderr .= $str;
//  else
//    break;
//}
//echo "STDERR: " . fread($stderr, 256) . "\n";
//fclose($stderr);
