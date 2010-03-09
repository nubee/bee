--TEST--
File_SearchReplace Preg mode s/r - strings
--SKIPIF--
<?php 
include(dirname(__FILE__).'/setup.php');
print $status; 
?>
--FILE--
<?php 
require_once(dirname(__FILE__).'/setup.php');

$search  = '!<TD[^>]*>.*?</TD>!is';
$replace = '<cell>';

$snr = new File_SearchReplace( $search, $replace, $onefilename);
$snr -> setSearchFunction('preg');
$snr -> doSearch() ;

readfile($onefilename);
echo "\n------[Occurences]: " . $snr->getNumOccurences();
echo "\n------[Last Error]: " , ($snr->getLastError() !== '') ? var_dump($snr->getLastError()) : "N/A";

?>
--EXPECT--
</td>
        </tr>
</table>
<!-- end OSTG navbar -->

<!-- prdownloads supplemental -->
<TABLE width="100%" bgcolor="#FFFFFF" cellpadding="4" cellspacing="0" align="center" border="0">
<TR valign="middle">
 <cell>

</TR>
</TABLE>
<font size=1><br></font>
<!-- prdownloads supplemental --><TABLE align="center" width="90%" bgcolor="#FFFFFF" cellpadding="0" cellspacing="0" border="0">
<tr valign="middle">
  <cell>

  <cell>
</tr>
 <TR><cell><cell><cell><cell></TR><TR BGCOLOR=#EEEEEE><cell><cell><cell><cell></TR><TR BGCOLOR=#DDDDDD><cell><cell><cell><cell></TR><TR BGCOLOR=#EEEEEE><cell><cell><cell><cell></TR><TR BGCOLOR=#DDDDDD><cell><cell><cell><cell></TR><TR BGCOLOR=#EEEEEE><cell><cell><cell><cell></TR><TR BGCOLOR=#DDDDDD><cell><cell><cell><cell></TR><TR BGCOLOR=#EEEEEE><cell><cell><cell><cell></TR><TR BGCOLOR=#DDDDDD><cell><cell><cell><cell></TR><TR BGCOLOR=#EEEEEE><cell><cell><cell><cell></TR><TR BGCOLOR=#DDDDDD><cell><cell><cell><cell></TR><TR BGCOLOR=#EEEEEE><cell><cell><cell><cell></TR><TR BGCOLOR=#DDDDDD><cell><cell><cell><cell></TR><TR BGCOLOR=#EEEEEE><cell><cell></TR><TR BGCOLOR=#EEEEEE><cell></TR><TR BGCOLOR=#DDDDDD><cell></TR><TR BGCOLOR=#EEEEEE><cell></TR><TR BGCOLOR=#DDDDDD><cell></TR><TR BGCOLOR=#EEEEEE><cell></TR><TR BGCOLOR=#DDDDDD><cell></TR><TR BGCOLOR=#EEEEEE><cell></TR><TR BGCOLOR=#DDDDDD><cell></TR><TR BGCOLOR=#EEEEEE><cell></TR><TR BGCOLOR=#DDDDDD><cell></TR><TR BGCOLOR=#EEEEEE><cell></TR><TR BGCOLOR=#DDDDDD><cell></TR><TR BGCOLOR=#CCCCFF><cell></TR><TR BGCOLOR=#DDDDDD><cell></TR>
</table></TD></TR></TABLE></FORM><P class="footer">
<b>Jan 27, 2005 12:40</b><br>
</P></TD>
<cell>
</TR></TABLE>
<br>&nbsp;
<!-- start OSDN Footer -->

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr bgcolor="#FFFFFF">
<cell>
------[Occurences]: 73
------[Last Error]: N/A