--TEST--
File_SearchReplace single file argument as string
--SKIPIF--
<?php 
include(dirname(__FILE__).'/setup.php');
print $status; 
?>
--FILE--
<?php 
require_once(dirname(__FILE__).'/setup.php');

$search  = ' BGCOLOR=#EEEEEE>';
$replace = ' style="background-color:black">';

$snr = new File_SearchReplace( $search, $replace, $onefilename);
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
 <TD align="center" colspan=3>

<iframe SRC="http://ads.osdn.com/?op=iframe&position=1&site_id=2&section=prdownloads" width="728" height="90" frameborder="no" border="0" MARGINWIDTH="0" MARGINHEIGHT="0" SCROLLING="no"></iframe> 

 </TD>

</TR>
</TABLE>
<font size=1><br></font>
<!-- prdownloads supplemental --><TABLE align="center" width="90%" bgcolor="#FFFFFF" cellpadding="0" cellspacing="0" border="0">
<tr valign="middle">
  <td align="center"><a href="http://sourceforge.net/"><img alt="SourceForge.Net" src="http://images.sourceforge.net/prdownloads/sourceforge_whitebg.gif" width="136" height="79" border="0"></a>&nbsp;</td>

  <td align="center"><img alt="Download Server" src="http://images.sourceforge.net/prdownloads/sf-download_server.png" HEIGHT="72" WIDTH="172" border="0">&nbsp;</td>
</tr>
 <TR><TD align="center" colspan=2><TABLE width=100% border=1><TR bgcolor=#CCCCCC><TH><!--   failedmirror:  failedmirrorshort:  -->You are requesting file: /sevenzip/7z415b.exe<br>Please select a mirror</TH></TR></TABLE><TABLE width=100% cellpadding=1 cellspacing=1 border=0><TR><TH>Host<br></TH><TH>Location<br></TH><TH>Continent<br></TH><TH>Download<br></TH></TR>
<TR BGCOLOR=#DDDDDD><TD align=center><A HREF=http://www.ibiblio.org><IMG ALT="unc logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/ibiblio_100x34.gif WIDTH=100 HEIGHT=41></A></TD><TD align=center>Chapel Hill, NC</TD><TD align=center>North America</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=unc><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR style="background-color:black"><TD align=center><A HREF=http://www.internap.com><IMG ALT="internap logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/internap_137_54_2.jpg WIDTH=100 HEIGHT=41></A></TD><TD align=center>Atlanta, GA</TD><TD align=center>North America</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=internap><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR BGCOLOR=#DDDDDD><TD align=center><A HREF=http://www.mirrorservice.org/help/introduction.html><IMG ALT="kent logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/ukms-button-100x34.png WIDTH=100 HEIGHT=41></A></TD><TD align=center>Kent, UK</TD><TD align=center>Europe</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=kent><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR style="background-color:black"><TD align=center><A HREF=http://www.aleron.com><IMG ALT="aleron logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/aleron_100x34.jpg WIDTH=100 HEIGHT=41></A></TD><TD align=center>Reston, VA</TD><TD align=center>North America</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=aleron><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR BGCOLOR=#DDDDDD><TD align=center><A HREF=http://www.optusnet.com.au><IMG ALT="optusnet logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/optusnet_100x34.gif WIDTH=100 HEIGHT=41></A></TD><TD align=center>Sydney, Australia</TD><TD align=center>Australia</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=optusnet><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR style="background-color:black"><TD align=center><A HREF=http://www.jaist.ac.jp><IMG ALT="jaist logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/jaist_100_34.png WIDTH=100 HEIGHT=41></A></TD><TD align=center>Ishikawa, Japan</TD><TD align=center>Asia</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=jaist><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR BGCOLOR=#DDDDDD><TD align=center><A HREF=http://www.puzzle.ch><IMG ALT="puzzle logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/puzzleitc_100x34.gif WIDTH=100 HEIGHT=41></A></TD><TD align=center>Bern, Switzerland</TD><TD align=center>Europe</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=puzzle><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR style="background-color:black"><TD align=center><A HREF=http://www.heanet.ie><IMG ALT="heanet logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/heanet_100x34.gif WIDTH=100 HEIGHT=41></A></TD><TD align=center>Dublin, Ireland</TD><TD align=center>Europe</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=heanet><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR BGCOLOR=#DDDDDD><TD align=center><A HREF=http://www.mesh-solutions.com/sf/><IMG ALT="mesh logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/mesh_100_34_3.gif WIDTH=100 HEIGHT=41></A></TD><TD align=center>Duesseldorf, Germany</TD><TD align=center>Europe</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=mesh><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR style="background-color:black"><TD align=center><A HREF=http://www.ovh.com><IMG ALT="ovh logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/ovh_100x34.jpg WIDTH=100 HEIGHT=41></A></TD><TD align=center>Roubaix, France</TD><TD align=center>Europe</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=ovh><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR BGCOLOR=#DDDDDD><TD align=center><A HREF=http://www.voxel.net><IMG ALT="voxel logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/voxel_100x34.gif WIDTH=100 HEIGHT=41></A></TD><TD align=center>New York, New York</TD><TD align=center>North America</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=voxel><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR style="background-color:black"><TD align=center><A HREF=http://www.umn.edu><IMG ALT="umn logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/umn_100x34.gif WIDTH=100 HEIGHT=41></A></TD><TD align=center>Minneapolis, MN</TD><TD align=center>North America</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=umn><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR BGCOLOR=#DDDDDD><TD align=center><A HREF=http://www.belnet.be><IMG ALT="belnet logo" BORDER=0 SRC=http://images.sourceforge.net/prdownloads/belnet_100x34.gif WIDTH=100 HEIGHT=41></A></TD><TD align=center>Brussels, Belgium</TD><TD align=center>Europe</TD><TD align=center><A HREF=/sevenzip/7z415b.exe?use_mirror=belnet><IMG ALT="download"BORDER=0 SRC=/icons/binary.gif></A>1111 kb</TD></TR><TR style="background-color:black"><TD colspan=2>
</TD><TD colspan=2><FORM METHOD=GET ACTION=/sevenzip/7z415b.exe><TABLE cellpadding=1 cellspacing=1 border=1 width=100% bgcolor=#FFFFFF bordercolor=#FFFFFF bordercolorlight=#FFFFFF bordercolordark=#FFFFFF>
<TR><TH>Select Preferred Mirror</TH></TR><TR BGCOLOR=#DDDDDD><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=unc OnClick="form.submit()">unc (US)
</TD></TR><TR style="background-color:black"><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=internap OnClick="form.submit()">internap (US)
</TD></TR><TR BGCOLOR=#DDDDDD><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=kent OnClick="form.submit()">kent (UK)
</TD></TR><TR style="background-color:black"><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=aleron OnClick="form.submit()">aleron (US)
</TD></TR><TR BGCOLOR=#DDDDDD><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=optusnet OnClick="form.submit()">optusnet (AU)
</TD></TR><TR style="background-color:black"><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=jaist OnClick="form.submit()">jaist (JP)
</TD></TR><TR BGCOLOR=#DDDDDD><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=puzzle OnClick="form.submit()">puzzle (CH)
</TD></TR><TR style="background-color:black"><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=heanet OnClick="form.submit()">heanet (IE)
</TD></TR><TR BGCOLOR=#DDDDDD><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=mesh OnClick="form.submit()">mesh (DE)
</TD></TR><TR style="background-color:black"><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=ovh OnClick="form.submit()">ovh (FR)
</TD></TR><TR BGCOLOR=#DDDDDD><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=voxel OnClick="form.submit()">voxel (US)
</TD></TR><TR style="background-color:black"><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=umn OnClick="form.submit()">umn (US)
</TD></TR><TR BGCOLOR=#DDDDDD><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT  TYPE=RADIO NAME=use_default VALUE=belnet OnClick="form.submit()">belnet (BE)
</TD></TR><TR BGCOLOR=#CCCCFF><TD align=left>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT checked TYPE=RADIO NAME=use_default VALUE=none OnClick="form.submit()"><b>none</b> 
</TD></TR><TR BGCOLOR=#DDDDDD><TD align=center><INPUT TYPE=submit VALUE='set default'></TD></TR>
</table></TD></TR></TABLE></FORM><P class="footer">
<b>Jan 27, 2005 12:40</b><br>
</P></TD>
<TD valign=top width=126 bgcolor=#AAAAAA>
 <!-- AD POSITION 2 --> <iframe src="http://ads.osdn.com/?op=iframe&position=2&site_id=2&section=prdownloads&allpositions=1,2" height="600" width="125" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no"></iframe>  <!-- end ad position2 -->
</TD>
</TR></TABLE>
<br>&nbsp;
<!-- start OSDN Footer -->

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr bgcolor="#FFFFFF">
<td><img src="http://images.sourceforge.net/prdownloads/blank.gif" width="1" height="1" alt=""></td>
------[Occurences]: 13
------[Last Error]: N/A