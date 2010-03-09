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

$search[]  = "[opyright]+";
$replace[] = "oOoOo";

$search[]  = "\* @([a-zO]+) ([0-9.]+)";
$replace[] = "* \\2 \\1";

$search[]  = "\*";
$replace[] = "o";

$snr = new File_SearchReplace( $search, $replace, $files);
$snr -> setSearchFunction('ereg');
$snr -> doSearch() ;

foreach($files as $f) {
   readfile($f);
};

echo "\n------[Occurences]: " . $snr->getNumOccurences();
echo "\n------[Last Error]: " , ($snr->getLastError() !== '') ? var_dump($snr->getLastError()) : "N/A";


?>
--EXPECT--
<?oOoOo
// +-----------------------------------------------------------------------+
// | CoOoOo (c) 2002-2003, RoOoOocoOoOoaoOoOod HeoOoOoes                                |
// | All oOoOos oOoOoeseoOoOoved.                                                  |
// |                                                                       |/oo
// +-----------------------------------------------------------------------+
// | CoOoOo (c) 2002-2003, RoOoOocoOoOoaoOoOod HeoOoOoes                                |
 ...
 o
 o SeaoOoOocoOoOo and ReoOoOolace UoOoOoloOoOo
 o
 o 1.0 veoOoOosoOoOon
 o @oOoOoackaoOoOoe FoOoOole
 o/
------[Occurences]: 33
------[Last Error]: N/A