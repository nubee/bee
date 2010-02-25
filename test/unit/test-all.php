<?php

require_once dirname(__FILE__) . '/../../lib/util/FileFinder.class.php';
require_once dirname(__FILE__) . '/../../lib/vendor/lime.php';

$finder = FileFinder::type('file');
$finder->follow_link()->name('*Test.php');

$dir = dirname(__FILE__);
/*
foreach ($finder->in($dir) as $file)
  require $file;
*/
$h = new lime_harness();

// filter and register unit tests
$h->register($finder->in($dir));

$ret = $h->run() ? 0 : 1;

//if ($options['xml'])
if ($argc > 1)
{
  $stringData = $h->to_xml();
  $myFile = "$argv[1]";
  $fh = fopen($myFile, 'w') or die("can't open file");
  fwrite($fh, $stringData);
  fclose($fh);
}

// TODO: add option to return ret or always 0
//exit($ret);
