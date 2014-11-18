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

$title="Zambia -- Check out the links below to learn about the great programming and vending we will have at upcoming events!";

$onetime=0;
for ($i=1; $i<=$conrows; $i++) {
  $conid=$ConCount_array[$i];
  if ($ConInfo_array[$conid]['constartdate']=="0000-00-00 00:00:00") continue;
  $constart=strtotime($ConInfo_array[$conid]['constartdate']);
  $connumdays=$ConInfo_array[$conid]['connumdays'];
  if ($connumdays > 1) {
    $offset=86400 * ($connumdays - 1);
    $condate=date("l, F j ",$constart);
    $condate.=" - ";
    $condate.=date("l, F j Y",($constart + $offset));
  } else {
    $condate=date("l, F j Y",$constart);
  }
  $conname=htmlspecialchars_decode($ConInfo_array[$conid]['conname']);

  if ($nowis < $constart) { 
    $webstring.="<H3>$conname on $condate:</H3>\n";
  } else {
    if ($onetime < 1) {
      $webstring.="<HR>\n<H3>Check out the below links to learn about the great programming we had at previous events:</H3>\n<HR>\n";
      $onetime++;
    }
    $webstring.="<H3>$conname that ran $condate:</H3>\n";
  }
  $webstring.="<UL>\n";
  if ($phase_array[$conid]['General Info Available'] == '0' ) {
    $webstring.="  <LI><A HREF=\"webpages/GenInfo.php?conid=$conid\">General Event Information</A></LI>\n";
  }
  $webstring.="  <LI><A HREF=\"webpages/ConStaffBios.php?conid=$conid\">Con Staff</A></LI>\n";
  if ($phase_array[$conid]['Prog Available'] == '0' ) {
    $webstring.="  <LI><A HREF=\"webpages/Postgrid.php?conid=$conid\">Schedule Grid</A></LI>\n";
    $webstring.="  <LI><A HREF=\"webpages/Descriptions.php?conid=$conid\">Class Descriptions</A></LI>\n";
    $webstring.="  <LI><A HREF=\"webpages/Schedule.php?conid=$conid\">Schedule</A></LI>\n";
    $webstring.="  <LI><A HREF=\"webpages/Tracks.php?conid=$conid\">Tracks</A></LI>\n";
    $webstring.="  <LI><A HREF=\"webpages/Bios.php?conid=$conid\">Presenter Bios</A></LI>\n";
  }
  if ($phase_array[$conid]['Vendors Available'] == '0' ) {
    $webstring.="  <LI><A HREF=\"webpages/Vendors.php?conid=$conid\">Vendor List</A></LI>\n";
  }
  if ($phase_array[$conid]['Venue Available'] == '0' ) {
    $webstring.="  <LI><A HREF=\"webpages/Venue.php?conid=$conid\">Venue Information</A></LI>\n";
  }
  if ($phase_array[$conid]['Vol Available'] == '0' ) {
    $webstring.="  <LI><A HREF=\"webpages/Postgrid.php?volunteer=y&conid=$conid\">Volunteer Grid</A></LI>\n";
    $webstring.="  <LI><A HREF=\"webpages/Descriptions.php?volunteer=y&conid=$conid\">Volunteer Job Descriptions</A></LI>\n";
  }
  if ($phase_array[$conid]['Comments Displayed'] == '0' ) {
    $webstring.="  <LI><A HREF=\"webpages/CuratedComments.php?conid=$conid\">Comments about the event</A></LI>\n";
  }
  if ($nowis < $constart) { 
    $webstring.="  <LI><A HREF=\"webpages/login.php?newconid=$conid\">Presenter/Volunteer Login</A></LI>\n";
    if ($phase_array[$conid]['Brainstorm'] == '0' ) {
      $webstring.="  <LI>\n";
      $webstring.="  <FORM name=\"brainstormform\" method=\"POST\" action=\"webpages/doLogin.php\">\n";
      $webstring.="    <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
      $webstring.="    <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
      $webstring.="    <INPUT type=\"hidden\" name=\"target\" value=\"brainstorm\">\n";
      $webstring.="    <INPUT type=\"submit\" name=\"submit\" value=\"Class/Presenter Submission\">\n";
      $webstring.="  </FORM>\n";
      $webstring.="  <FORM name=\"brainstormviewform\" method=\"POST\" action=\"webpages/doLogin.php\">\n";
      $webstring.="    <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
      $webstring.="    <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
      $webstring.="    <INPUT type=\"hidden\" name=\"target\" value=\"brainstorm\">\n";
      $webstring.="    <INPUT type=\"submit\" name=\"submit\" value=\"View Suggested Classes\">\n";
      $webstring.="  </FORM>\n";
    }
    if ($phase_array[$conid]['Vendor'] == '0' ) {
      $webstring.="  <LI>\n";
      $webstring.="  <FORM name=\"vendorform\" method=\"POST\" action=\"webpages/doLogin.php\">\n";
      $webstring.="    <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
      $webstring.="    <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
      $webstring.="    <INPUT type=\"hidden\" name=\"target\" value=\"vendor\">\n";
      $webstring.="    <INPUT type=\"submit\" name=\"submit\" value=\"New Vendor Application\">\n";
      $webstring.="  </FORM>\n";
    }
  } else { 
    if ($phase_array[$conid]['Feedback Available'] == '0') {
      $webstring.="  <LI><A HREF=\"webpages/Feedback.php?conid=$conid\">Feedback</A></LI>\n";
    }
    // Percy, we need to make a hard link to Zambia or run this through Pyro,  right now, the link breaks
    // if ($onetime < 1) {
    $webstring.="  <LI><A HREF=\"webpages/login.php?newconid=$conid\">Presenter Login</A></LI>\n";
    // }
  }
  $webstring.="</UL>\n";
}


// Percy, Added $include IF eval
// -Mike
if (!isset($included) && $included != 'YES')
  {
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml">
  <HEAD>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">
<?php
echo "    <TITLE>$title</TITLE>\n";
if (file_exists($HeaderTemplateFile)) {
  readfile($HeaderTemplateFile);
 } else {
?>
  <link rel="stylesheet" href="Common.css" type="text/css">
  </HEAD>
  <BODY>
<?php } echo "<H1>$title</H1>\n<hr>\n" . $webstring; 
if (file_exists($FooterTemplateFile)) {
  readfile($FooterTemplateFile);
 } else {
  echo "<hr>\n<P>If you have questions or wish to communicate an idea, please contact ";
  echo "<A HREF=\"mailto:$ProgramEmail\">$ProgramEmail</A>.\n</P>";
 }
include ('webpages/google_analytics.php');
?>
  </BODY>
</HTML>

<?php
}  // close IF $included 
?>
