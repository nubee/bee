--TEST--
File_SearchReplace multiple files with search/replace pairs in array
--SKIPIF--
<?php 
include(dirname(__FILE__).'/setup.php');
print $status; 
?>
--FILE--
<?php 
require_once(dirname(__FILE__).'/setup.php');

$search[]  = "Copyright (c) 2002-2003";
$replace[] = "Copyright (c) 2002-2005";

$search[]  = "* @version 1.0";
$replace[] = "* @version 1.1";

$search[]  = "00";
$replace[] = "oo";

$snr = new File_SearchReplace( $search, $replace, $files);
$snr -> doSearch() ;

foreach($files as $f) {
   readfile($f);
};

echo "\n------[Occurences]: " . $snr->getNumOccurences();
echo "\n------[Last Error]: " , ($snr->getLastError() !== '') ? var_dump($snr->getLastError()) : "N/A";


?>
--EXPECT--
<?php
// +-----------------------------------------------------------------------+
// | Copyright (c) 2oo2-2oo5, Richard Heyes                                |
// | All rights reserved.                                                  |
// |                                                                       |/**
// +-----------------------------------------------------------------------+
// | Copyright (c) 2oo2-2oo5, Richard Heyes                                |
 ...
 *
 * Search and Replace Utility
 *
 * @version 1.1
 * @package File
 */
------[Occurences]: 7
------[Last Error]: N/A