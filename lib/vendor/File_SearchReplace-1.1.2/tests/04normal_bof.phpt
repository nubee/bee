--TEST--
File_SearchReplace bug or feature with search string and replace array
--SKIPIF--
<?php 
include(dirname(__FILE__).'/setup.php');
print $status; 
?>
--FILE--
<?php 
require_once(dirname(__FILE__).'/setup.php');

$search = "Copyright (c) 2002-2003,";
$replace[] = "Copyprotected.";
$replace[] = "Copystuff 2005";
$replace[] = "Eat upon perusal";

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
// | Copyprotected. Richard Heyes                                |
// | All rights reserved.                                                  |
// |                                                                       |/**
// +-----------------------------------------------------------------------+
// | Copyprotected. Richard Heyes                                |
 ...
 *
 * Search and Replace Utility
 *
 * @version 1.0
 * @package File
 */
------[Occurences]: 2
------[Last Error]: N/A