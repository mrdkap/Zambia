<?php
require_once ('Local/db_name.php'); // This should be generalized so everything can be one directory
$ConKey=CON_KEY; // should be passed in, rather than hard set
$webstring="";
$HeaderTemplateFile="Local/HeaderTemplate.html";
$FooterTemplateFile="Local/FooterTemplate.html";

// Database link
$link = mysql_connect(DBHOSTNAME,DBUSERID,DBPASSWORD);
mysql_select_db(DBDB,$link);

// Establish the con info
$query= <<<EOF
SELECT
    conname,
    constartdate,
    connumdays,
    conid
  FROM
      ConInfo
  ORDER BY
    constartdate DESC
EOF;

// Retrieve query fail if database can't be found, and if there isn't just one result
if (($result=mysql_query($query,$link))===false) {
  $message ="<P>Error retrieving data from database.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}
if (0==($conrows=mysql_num_rows($result))) {
  $message.="<P>No results found.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}

for ($i=1; $i<=$conrows; $i++) {
  $tmpconinfo_array=mysql_fetch_assoc($result);
  $ConCount_array[$i]=$tmpconinfo_array['conid'];
  $ConInfo_array[$tmpconinfo_array['conid']]['conname']=$tmpconinfo_array['conname'];
  $ConInfo_array[$tmpconinfo_array['conid']]['constartdate']=$tmpconinfo_array['constartdate'];
  $ConInfo_array[$tmpconinfo_array['conid']]['connumdays']=$tmpconinfo_array['connumdays'];
}

// Establish the "now" for comparison purposes
$nowis=time();

// Establish the states, for the look of the page
$query= <<<EOF
SELECT
    phasestate,
    phasetypename,
    conid
  FROM
      Phase
    JOIN PhaseTypes USING (phasetypeid)
EOF;

// Retrieve query, fail if database can't be found, or there aren't any results
if (($result=mysql_query($query,$link))===false) {
  $message ="<P>Error retrieving data from database.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}
if (0==($phaserows=mysql_num_rows($result))) {
  $message.="<P>No results found.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}

// Set up the phase_array such that the typename is the key and the state is the value
for ($i=1; $i<=$phaserows; $i++) {
  $element_array=mysql_fetch_assoc($result);
  $phase_array[$element_array['conid']][$element_array['phasetypename']]=$element_array['phasestate'];
}

$title="Upcoming Events:";

// header/footer for each section
$divfooter ="    </UL>\n";
$divfooter.="  </DIV>\n";
$genheader ="  <DIV style=\"float: left; width: 50%; font-size: 1.167em; \">\n";
$genheader.="    <H3>General Information</H3>\n";
$genheader.="    <UL>\n";
$progheader ="  <DIV style=\"float: right; width: 50%; font-size: 1.167em; \">\n";
$progheader.="    <H3>Programming Information</H3>\n";
$progheader.="    <UL>\n";
$volheader ="  <DIV style=\"float: left; width: 50%; font-size: 1.167em; \">\n";
$volheader.="    <H3>Volunteer Information</H3>\n";
$volheader.="    <UL>\n";
$vendheader ="  <DIV style=\"float: right; width: 50%; font-size: 1.167em; \">\n";
$vendheader.="    <H3>Vending Information</H3>\n";
$vendheader.="    <UL>\n";

// Setup for the nav set on the left
$navstring ="        <P>Upcoming:</P>\n";
$navstring.="        <UL>\n";

// Set the counter for the "Previous" break
$onetime=0;

// Setup to put the soonest at the top
$webfinalstring="";

// Walk each of the convention instances
for ($i=1; $i<=$conrows; $i++) {
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
  $conname=htmlspecialchars_decode($ConInfo_array[$conid]['conname']);

  // Set the "Previous" barrier.  Once.
  if ($nowis > ($constart + $offset)) {
    if ($onetime < 1) {
      $webstring.="<DIV style=\"background-color: #ffdb70; display: table; width: 100%;\">\n";
      $webstring.="  <H1>Previous Events:</H1>";
      $navstring.="        </UL>\n";
      $navstring.="        <P>Previous:</P>\n";
      $navstring.="        <UL>\n";
      $webstring.="</DIV>\n";
      $onetime++;
    }
  }

  // Name and date the con.
  $webstring.="<DIV>\n";
  $webstring.="  <DIV style=\"float: left; width: 100%; background-color: #F0F0F0; \">\n";
  $webstring.="    <H2><A NAME=\"$conid\"></A>$conname &mdash; $condate</H2>\n";
  $navstring.="          <LI><A HREF=#$conid>$conname &mdash; $condate</A></LI>\n";
  $webstring.="  </DIV>\n";

  // General information block
  $genbody="";
  if ($phase_array[$conid]['General Info Available'] == '0' ) {
    $genbody.="      <LI><A HREF=\"webpages/GenInfo.php?conid=$conid\">General Event Information</A></LI>\n";
  }
  $genbody.="      <LI><A HREF=\"webpages/ConStaffBios.php?conid=$conid\">Con Staff</A></LI>\n";
  if ($phase_array[$conid]['Venue Available'] == '0' ) {
    $genbody.="      <LI><A HREF=\"webpages/Venue.php?conid=$conid\">Venue Information</A></LI>\n";
  }
  if ($phase_array[$conid]['Comments Displayed'] == '0' ) {
    $genbody.="      <LI><A HREF=\"webpages/CuratedComments.php?conid=$conid\">Comments about the event</A></LI>\n";
  }
  if (file_exists("Local/$conid/Program_Book.pdf")) {
    $genbody.="      <LI><A HREF=\"Local/$conid/Program_Book.pdf\">Program Book</A></LI>\n";
  }
  if (file_exists("Local/$conid/Con_Chair_Welcome")) {
    $genbody.="      <LI><A HREF=\"webpages/GenInfo.php?conid=$conid#conchairletter\">Welcome from the Con Chair</A></LI>\n";
  }
  if (file_exists("Local/$conid/Org_Welcome")) {
    $genbody.="      <LI><A HREF=\"webpages/GenInfo.php?conid=$conid#orgletter\">Welcome from the Organization</A></LI>\n";
  }
  if (file_exists("Local/$conid/Rules")) {
    $genbody.="      <LI><A HREF=\"webpages/GenInfo.php?conid=$conid#rules\">Rules</A></LI>\n";
  }
  if ($genbody!="") {$webstring.=$genheader . $genbody . $divfooter;}

  // Programming information block
  $progbody="";
  if ($phase_array[$conid]['Prog Available'] == '0' ) {
    $progbody.="      <LI><A HREF=\"webpages/Postgrid.php?conid=$conid\">Schedule Grid</A></LI>\n";
    $progbody.="      <LI><A HREF=\"webpages/PubsSched.php?format=desc&conid=$conid\">Class Descriptions</A>\n";
    $progbody.="        <A HREF=\"webpages/PubsSched.php?format=desc&conid=$conid&short=Y\">(short)</A></LI>\n";
    $progbody.="      <LI><A HREF=\"webpages/PubsSched.php?format=sched&conid=$conid\">Schedule</A>\n";
    $progbody.="        <A HREF=\"webpages/PubsSched.php?format=sched&conid=$conid&short=Y\">(short)</A></LI>\n";
    $progbody.="      <LI><A HREF=\"webpages/PubsSched.php?format=tracks&conid=$conid\">Tracks</A>\n";
    $progbody.="        <A HREF=\"webpages/PubsSched.php?format=tracks&conid=$conid&short=Y\">(short)</A></LI>\n";
    $progbody.="      <LI><A HREF=\"webpages/PubsSched.php?format=trtime&conid=$conid\">Tracks by Time</A>\n";
    $progbody.="        <A HREF=\"webpages/PubsSched.php?format=trtime&conid=$conid&short=Y\">(short)</A></LI>\n";
    $progbody.="      <LI><A HREF=\"webpages/PubsSched.php?format=rooms&conid=$conid\">Rooms</A>\n";
    $progbody.="        <A HREF=\"webpages/PubsSched.php?format=rooms&conid=$conid&short=Y\">(short)</A></LI>\n";
    $progbody.="      <LI><A HREF=\"webpages/PubsBios.php?conid=$conid\">Presenter Bios</A>\n";
    $progbody.="        <A HREF=\"webpages/PubsBios.php?conid=$conid&short=Y\">(short)</A></LI>\n";
  }
  if ($phase_array[$conid]['WebApp'] == '0' ) {
    $progbody.="      <LI><A HREF=\"webpages/KonOpas.php?conid=$conid\">Web App</A></LI>\n";
  }
  if ($phase_array[$conid]['Brainstorm'] == '0' ) {
    $progbody.="      <LI>\n";
    $progbody.="      <FORM name=\"brainstormform\" method=\"POST\" action=\"webpages/doLogin.php\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"target\" value=\"brainstorm\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"newconid\" value=\"$conid\">\n";
    $progbody.="        <INPUT type=\"submit\" name=\"submit\" value=\"Class/Presenter Submission\">\n";
    $progbody.="      </FORM>\n";
    $progbody.="      <FORM name=\"brainstormviewform\" method=\"POST\" action=\"webpages/doLogin.php\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"target\" value=\"brainstorm\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"newconid\" value=\"$conid\">\n";
    $progbody.="        <INPUT type=\"submit\" name=\"submit\" value=\"View Suggested Classes\">\n";
    $progbody.="      </FORM>\n";
    $progbody.="      </LI>\n";
  }
  if ($phase_array[$conid]['Photo Submission'] == '0') {
    $progbody.="      <LI><A HREF=\"PhotoLoungeProposed.php\">Propose to Submit to the Photo Lounge</A></LI>\n";
  }
  if ($phase_array[$conid]['Feedback Available'] == '0') {
    $progbody.="      <LI><A HREF=\"webpages/Feedback.php?conid=$conid\">Feedback</A></LI>\n";
  }
  if ($nowis < $constart) {
    if ($phase_array[$conid]['Photo Submission'] == '0') {
      $progbody.="      <LI><A HREF=\"webpages/login.php?newconid=$conid\">Presenter/Photo Submissions/Volunteer Login</A></LI>\n";
    } else {
      $progbody.="      <LI><A HREF=\"webpages/login.php?newconid=$conid\">Presenter/Volunteer Login</A></LI>\n";
    }
  } else {
    $progbody.="      <LI><A HREF=\"webpages/login.php?newconid=$conid\">Presenter Login</A></LI>\n";
  }
  if ($progbody!="") {$webstring.=$progheader . $progbody . $divfooter;}

  // Volunteer information block
  $volbody="";
  if ($phase_array[$conid]['Vol Available'] == '0' ) {
    $volbody.="      <LI><A HREF=\"webpages/Postgrid.php?volunteer=y&conid=$conid\">Volunteer Grid</A></LI>\n";
    $volbody.="      <LI><A HREF=\"webpages/VolsSched.php?format=desc&conid=$conid\">Job Descriptions</A>\n";
    $volbody.="        <A HREF=\"webpages/VolsSched.php?format=desc&conid=$conid&short=Y\">(short)</A></LI>\n";
    $volbody.="      <LI><A HREF=\"webpages/VolsSched.php?format=sched&conid=$conid\">Schedule</A>\n";
    $volbody.="        <A HREF=\"webpages/VolsSched.php?format=sched&conid=$conid&short=Y\">(short)</A></LI>\n";
    $volbody.="      <LI><A HREF=\"webpages/VolsSched.php?format=rooms&conid=$conid\">Posts</A>\n";
    $volbody.="        <A HREF=\"webpages/VolsSched.php?format=rooms&conid=$conid&short=Y\">(short)</A></LI>\n";
  }
  if ($volbody!="") {$webstring.=$volheader . $volbody . $divfooter;}

  // Vending information block
  $vendbody="";
  if ($phase_array[$conid]['Vendors Available'] == '0' ) {
    $vendbody.="      <LI><A HREF=\"webpages/Vendors.php?conid=$conid\">Vendor List</A></LI>\n";
  }
  if ($phase_array[$conid]['Vendor'] == '0' ) {
    $vendbody.="      <LI>\n";
    $vendbody.="      <FORM name=\"vendorform\" method=\"POST\" action=\"webpages/doLogin.php\">\n";
    $vendbody.="        <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
    $vendbody.="        <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
    $vendbody.="        <INPUT type=\"hidden\" name=\"target\" value=\"vendor\">\n";
    $vendbody.="        <INPUT type=\"hidden\" name=\"newconid\" value=\"$conid\">\n";
    $vendbody.="        <INPUT type=\"submit\" name=\"submit\" value=\"New Vendor Application\">\n";
    $vendbody.="      </FORM>\n</LI>\n";
  }
  if ($vendbody!="") {$webstring.=$vendheader . $vendbody . $divfooter;}
  $webstring.="</DIV>\n";

  // Switch the ordering so soonest first of the Upcoming Events section
  if ($onetime < 1) {
      $webfinalstring=$webstring . $webfinalstring;
      $webstring="";
  } else {
    $webfinalstring.=$webstring;
    $webstring="";
  }
}
$navstring.="        </UL>\n";

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
  readfile($HeaderTemplateFile);
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
      article { display: table; float: right; width: 74%; }
      footer { background-color: #F0F0F0; padding: 1em; border-top: 1px; display: table; float: right; width: 74%; }
      header { background-color: #ffdb70; display: table; width: 100%; }
      nav { display: table; float: left; width: 25%; }
      nav li { margin: .3em 0 .3em 0; list-style-type: none; }
      body { display: table; font-family: sans-serif; text-align: left; width: 99% }
    </STYLE>
  </HEAD>
  <BODY>
      <NAV>
        <H2>Events</H2>
<?php echo $navstring; ?>
      </NAV>
<?php } ?>
      <ARTICLE>
        <DIV style="background-color: #ffdb70; display: table; width: 100%;">
<?php echo "          <H1>$title</H1>\n" ?>
        </DIV>
<?php echo $webfinalstring; ?>
      </ARTICLE>
<?php if (file_exists($FooterTemplateFile)) {
  readfile($FooterTemplateFile);
 } else {
?>
      <FOOTER>
        <hr>
        <P>If you have questions or wish to communicate an idea, please contact
           <A HREF=<?php echo "\"mailto:$ProgramEmail\">$ProgramEmail"; ?></A></P>
      </FOOTER>
    </DIV>
<?php }
include ('webpages/google_analytics.php');
?>
  </BODY>
</HTML>
