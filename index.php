<?php
require_once ('Local/db_name.php'); // This should be generalized so everything can be one directory
$ConKey=CON_KEY; // should be passed in, rather than hard set
$webstring="";
$HeaderTemplateFile="Local/HeaderTemplate.html";
$FooterTemplateFile="Local/FooterTemplate.html";
$ReportDB=REPORTDB; // Temporary, until we move to one database for it all
// Failover if REPORTDB isn't set.
if ($ReportDB=="REPORTDB") {$ReportDB=DBDB;}

// Database link
$link = mysql_connect(DBHOSTNAME,DBUSERID,DBPASSWORD);
mysql_select_db($ReportDB,$link);

// Establish the con info
$query= <<<EOF
SELECT
    conname,
    constartdate
  FROM
      ConInfo
  WHERE
      conid=$ConKey
EOF;

// Retrieve query fail if database can't be found, and if there isn't just one result
if (($result=mysql_query($query,$link))===false) {
  $message ="<P>Error retrieving data from database.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}
if (0==($rows=mysql_num_rows($result))) {
  $message.="<P>No results found.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}
if (1<($rows=mysql_num_rows($result))) {
  $message.="<P>Too many results found.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}
$ConInfo_array=mysql_fetch_assoc($result);

$ConName=$ConInfo_array['conname'];
$ConStartDate=$ConInfo_array['constartdate'];

$constart=strtotime($ConStartDate);
$nowis=time();

// Establish the states, for the look of the page
$query= <<<EOF
SELECT
    phasestate,
    phasetypename
  FROM
      Phase
    JOIN PhaseTypes USING (phasetypeid)
  WHERE
    conid=$ConKey
EOF;

// Retrieve query, fail if database can't be found, or there aren't any results
if (($result=mysql_query($query,$link))===false) {
  $message ="<P>Error retrieving data from database.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}
if (0==($rows=mysql_num_rows($result))) {
  $message.="<P>No results found.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}

// Set up the phase_array such that the typename is the key and the state is the value
for ($i=1; $i<=$rows; $i++) {
  $element_array=mysql_fetch_assoc($result);
  $phase_array[$element_array['phasetypename']]=$element_array['phasestate'];
}

// These should be passed in from the calling page, if they aren't use the local copies
if (!isset($ConStaffBios)) {$ConStaffBios="webpages/ConStaffBios.php";}
if (!isset($Postgrid)) {$Postgrid="webpages/Postgrid.php";}
if (!isset($Descriptions)) {$Descriptions="webpages/Descriptions.php";}
if (!isset($Schedule)) {$Schedule="webpages/Schedule.php";}
if (!isset($Tracks)) {$Tracks="webpages/Tracks.php";}
if (!isset($Bios)) {$Bios="webpages/Bios.php";}
if (!isset($Vendors)) {$Vendors="webpages/Vendors.php";}
if (!isset($Feedback)) {$Feedback="webpages/Feedback.php";}


$title="Zambia -- ". $ConInfo_array['conname'];

if ($nowis < $constart) { 
  $webstring.="<H2>Check out the below links to learn about the great programming and vending we will have at $ConName!</H2>\n";
} else { 
  $webstring.="<H2>Check out the below links to give us/see your feedback and learn about the great programming we had at $ConName!</H2>\n";
}
$webstring.="<UL>\n";
$webstring.="  <LI><A HREF=\"$ConStaffBios\">Con Staff</A></LI>\n";
if ($phase_array['Prog Available'] == '0' ) {
  $webstring.="  <LI><A HREF=\"$Postgrid\">Schedule Grid</A></LI>\n";
  $webstring.="  <LI><A HREF=\"$Descriptions\">Class Descriptions</A></LI>\n";
  $webstring.="  <LI><A HREF=\"$Schedule\">Schedule</A></LI>\n";
  $webstring.="  <LI><A HREF=\"$Tracks\">Tracks</A></LI>\n";
  $webstring.="  <LI><A HREF=\"$Bios\">Presenter Bios</A></LI>\n";
}
if ($phase_array['Vendors Available'] == '0' ) {
  $webstring.="  <LI><A HREF=\"$Vendors\">Vendor List</A></LI>\n";
}
if ($phase_array['Vol Available'] == '0' ) {
  $webstring.="  <LI><A HREF=\"$Postgrid?volunteer=y\">Volunteer Grid</A></LI>\n";
  $webstring.="  <LI><A HREF=\"$Descriptions?volunteer=y\">Volunteer Job Descriptions</A></LI>\n";
}
if ($nowis < $constart) { 
  $webstring.="  <LI><A HREF=\"webpages/\">Presenter/Vendor Login</A></LI>\n";
  if ($phase_array['Brainstorm'] == '0' ) {
    $webstring.="  <LI>\n";
    $webstring.="  <FORM name=\"brainstormform\" method=\"POST\" action=\"webpages/doLogin.php\">\n";
    $webstring.="    <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
    $webstring.="    <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
    $webstring.="    <INPUT type=\"hidden\" name=\"target\" value=\"brainstorm\">\n";
    $webstring.="    <INPUT type=\"submit\" name=\"submit\" value=\"Class/Presenter Submission\">\n";
    $webstring.="  </FORM>\n";
  }
  if ($phase_array['Vendor'] == '0' ) {
    $webstring.="  <LI>\n";
    $webstring.="  <FORM name=\"vendorform\" method=\"POST\" action=\"webpages/doLogin.php\">\n";
    $webstring.="    <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
    $webstring.="    <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
    $webstring.="    <INPUT type=\"hidden\" name=\"target\" value=\"vendor\">\n";
    $webstring.="    <INPUT type=\"submit\" name=\"submit\" value=\"New Vendor Application\">\n";
    $webstring.="  </FORM>\n";
  }
} else { 
  if ($phase_array['Feedback Available'] == '0') {
    $webstring.="  <LI><A HREF=\"$Feedback\">Feedback</A></LI>\n";
  }

	// Percy, we need to make a hard link to Zambia or run this through Pyro,  right now, the link breaks
  $webstring.="  <LI><A HREF=\"webpages/\">Presenter Login</A></LI>\n";
}
$webstring.="</UL>\n";

// Percy, Added $include IF eval
// -Mike

	if (!isset($included) && $included != 'YES')
	{
	// end new IF $included
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<head>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">
<?php
echo "  <title>$title</title>\n";
if (file_exists($HeaderTemplateFile)) {
  readfile($HeaderTemplateFile);
 } else {
?>
  <link rel="stylesheet" href="Common.css" type="text/css">
</head>
<body>
<?php } echo "<H1>$title</H1>\n<hr>\n" . $webstring; 
if (file_exists($FooterTemplateFile)) {
  readfile($FooterTemplateFile);
 } else {
  echo "<hr>\n<P>If you have questions or wish to communicate an idea, please contact ";
  echo "<A HREF=\"mailto:$ProgramEmail\">$ProgramEmail</A>.\n</P>";
 }
include ('webpages/google_analytics.php');
?>
</body>
</html>
<?php
}  // close IF $included 
?>
