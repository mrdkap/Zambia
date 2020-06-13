<?php
require_once ('Local/db_name_historical.php'); // This should be generalized so everything can be one directory
$webstring="";
$HeaderTemplateFile="None";
$FooterTemplateFile="None";

$title="Historical Events:";

// Walk each of the convention instances
for ($i=0; $i<count($ConCount_array); $i++) {
  $conid=$ConCount_array[$i];

  // Ignore non-scheduled cons
  if ($ConInfo_array[$conid]['constartdate']=="0000-00-00 00:00:00") continue;

  // Set the start date, and the length of the con.
  $constart=strtotime($ConInfo_array[$conid]['constartdate']);
  $connumdays=$ConInfo_array[$conid]['connumdays'];

  // Set up run dates for the con
  if ($connumdays > 1) {
    $offset=86400 * ($connumdays - 1);
    $constartmonth=date("M",$constart);
    $conendmonth=date("M",($constart + $offset));
    $condate=date("M jS ",$constart);
    $condate.=" - ";
    if ($constartmonth == $conendmonth) {
      $condate.=date("jS Y",($constart + $offset));
    } else {
      $condate.=date("M jS Y",($constart + $offset));
    }
  } else {
    $condate=date("M jS Y",$constart);
    $offset=0;
  }

  // Set the con name
  $conname_tmp=htmlspecialchars_decode($ConInfo_array[$conid]['connamelong']);
  $conname="<A HREF=\"webpages/KonOpasHistorical.php?conid=$conid#info\">$conname_tmp</A>";

  // Add the name and date the con to be presented
  $webstring.="            <LI><B><A NAME=\"$conid\"></A>$conname</B> &mdash; $condate</LI>\n";
}

//Close what might be opened.
$webstring.="          </UL>\n        </DIV>\n";

// Now start the display.
?>

<!DOCTYPE html>
<HTML>
  <HEAD>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
/* Set up the title, and then pull in the site local HeaderTemplateFile
   or use the simply header file below. */
echo "    <TITLE>$title</TITLE>\n";
if (file_exists($HeaderTemplateFile)) {
  require($HeaderTemplateFile);
 } else {
?>
    <meta name="description" content="Fetish Fair Fleamarket information page">
    <meta name="keywords" content="">
    <!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="webpages/Common.css" type="text/css">
    <STYLE type="text/css">
      h1 { font-size: 2em; margin: .5em;}
      h2 { font-size: 1.5em; margin: .5em;}
      h3 { font-size: 1.17em; }
      .conname { float: left; width: 100%; }
      .geninfo { float: left; width: 50%; }
      .prog { float: right; width: 50%; }
      .vol { float: left; width: 50%; }
      .vend { float: right; width: 50%; }
      .row-fluid:before,.row-fluid:after { display: table; line-height: 0; content: ""; }
      .row-fluid:after { clear: both;  }
      .row-fluid [class*="span"] { display: block; float: left; width: 100%; min-height: 30px; margin-left: 2.5%; *margin-left: 2.5%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; }
      .row-fluid .span3 { width: 30%; *width: 30%; }
      article { display: table; float: center; width: 800px; }
      footer { background-color: #F0F0F0; padding: 1em; border-top: 1px; display: table; float: center; width: 800px; }
      header { background-color: #ffdb70; display: table; width: 100%; }
      body { display: table; font-family: sans-serif; text-align: left; width: 99% }
    </STYLE>
  </HEAD>
  <BODY>
<?php } ?>
      <MAIN role='main' class='content-wrapper inner-section'>
      <ARTICLE>
        <DIV style="background-color: #ffdb70; display: table; width: 100%;">
<?php echo "          <H1>$title</H1>\n" ?>
        </DIV>
<?php echo $webstring; ?>
      </ARTICLE>
      </MAIN>
      <DIV style="background-color: #ffdb70; display: table; width: 100%;">

      </DIV>
<?php if (file_exists($FooterTemplateFile)) {
  require($FooterTemplateFile);
 } else {
?>
      <FOOTER>
        <hr>
        <P>If you have questions or wish to communicate an idea, please contact
           <A HREF=<?php echo "\"mailto:historian@nelaonline.org\">historian@nelaonline.org"; ?></A></P>
      </FOOTER>
    </DIV>
<?php }
include ('webpages/google_analytics.php');
?>
  </BODY>
</HTML>
