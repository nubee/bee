<?php

require_once dirname(__FILE__) . '/../../lib/core/util/nbGlob.php';
require_once dirname(__FILE__) . '/../../lib/core/system/nbFileFinder.php';
require_once dirname(__FILE__) . '/../../vendor/lime/lime.php';

$finder = nbFileFinder::create('file');
$finder->followLink()->add('*Test.php');

$dir = dirname(__FILE__);

$h = new lime_harness();

// filter and register unit tests
$h->register($finder->in($dir));

$ret = $h->run() ? 0 : 1;

if ($argc > 1) {
  $stringData = $h->to_xml();
  $myFile = "$argv[1]";
  $fh = fopen($myFile, 'w') or die("can't open file");
  fwrite($fh, $stringData);
  fclose($fh);
}

exit($ret);
