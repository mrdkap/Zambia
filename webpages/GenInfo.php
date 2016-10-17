<?php
require_once('PostingCommonCode.php');
global $link;
$conid=$_GET['conid'];

// Test for conid being passed in
if ($conid == "") {
  $conid=$_SESSION['conid'];
}

// Set the conname from the conid
$query="SELECT conname,connumdays,congridspacer,constartdate,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$constart=$conname_array[1]['constartdate'];
$logo=$conname_array[1]['conlogo'];

// Set up the phase array
$query="SELECT phasestate,phasetypename FROM Phase JOIN PhaseTypes USING (phasetypeid) WHERE conid=$conid";
list($phaserows,$phaseheader_array,$fullphase_array)=queryreport($query,$link,$title,$description,0);
for ($i=1; $i<=$phaserows; $i++) {
  $phase_array[$fullphase_array[$i]['phasetypename']]=$fullphase_array[$i]['phasestate'];
}

// LOCALIZATIONS
$_SESSION['return_to_page']="GenInfo.php";
$title="General Information for $conname";

// header/footer for each section
$divfooter ="    </UL>\n";
$divfooter.="  </DIV>\n";
$genheader ="  <DIV style=\"float: left; width: 50%\">\n";
$genheader.="    <H3>General Information</H3>\n";
$genheader.="    <UL>\n";
$progheader ="  <DIV style=\"float: right; width: 50%;\">\n";
$progheader.="    <H3>Programming Information</H3>\n";
$progheader.="    <UL>\n";
$volheader ="  <DIV style=\"float: left; width: 50%;\">\n";
$volheader.="    <H3>Volunteer Information</H3>\n";
$volheader.="    <UL>\n";
$vendheader ="  <DIV style=\"float: right; width: 50%;\">\n";
$vendheader.="    <H3>Vending Information</H3>\n";
$vendheader.="    <UL>\n";

// Set the header information correctly
$description.="<DIV style=\" width: 100%; \">\n";
$genbody="";
$genbody.="      <LI><A HREF=\"ConStaffBios.php?conid=$conid\">Con Staff</A></LI>\n";
if ($phase_array['Venue Available'] == '0' ) {
  $genbody.="      <LI><A HREF=\"Venue.php?conid=$conid\">Venue Information</A></LI>\n";
}
if ($phase_array['Comments Displayed'] == '0' ) {
  $genbody.="      <LI><A HREF=\"CuratedComments.php?conid=$conid\">Comments about the event</A></LI>\n";
}
if (file_exists("../Local/$conid/Program_Book.pdf")) {
  $genbody.="      <LI><A HREF=\"Local/$conid/Program_Book.pdf\">Program Book</A></LI>\n";
}

$conchairletter="";
if (file_exists("../Local/$conid/Con_Chair_Welcome")) {
  $genbody.="      <LI><A HREF=\"#conchairletter\">Welcome from the Con Chair</A></LI>\n";
  $conchairletter.="<DIV style=\" display: block; width: 100%; float: left; \">\n";
  $conchairletter.="  <A NAME=\"conchairletter\">&nbsp;</A>\n";
  $conchairletter.="  <HR>\n<H3>A welcome letter from the Con Chair:</H3>\n<BR>\n";
  $conchairletter.=file_get_contents("../Local/$conid/Con_Chair_Welcome");
  $conchairletter.="</DIV>\n";
}

$orgletter="";
if (file_exists("../Local/$conid/Org_Welcome")) {
  $genbody.="      <LI><A HREF=\"#orgletter\">Welcome from the Organization</A></LI>\n";
  $orgletter.="<DIV style=\" display: block; width: 100%; float: left; \">\n";
  $orgletter.="<A NAME=\"orgletter\">&nbsp;</A>\n";
  $orgletter.="<HR>\n<H3>A welcome letter from the Organization:</H3>\n<BR>\n";
  $orgletter.=file_get_contents("../Local/$conid/Org_Welcome");
  $orgletter.="</DIV>\n";
}

$rules="";
if (file_exists("../Local/$conid/Rules")) {
  $genbody.="      <LI><A HREF=\"#rules\">Rules</A></LI>\n";
  $rules.="<DIV style=\" display: block; width: 100%; float: left; \">\n";
  $rules.="<A NAME=\"rules\">&nbsp;</A>\n";
  $rules.="<HR>\n<H3>Rules:</H3>\n<BR>\n";
  $rules.=file_get_contents("../Local/$conid/Rules");
  $rules.="</DIV>\n";
}
if ($genbody!="") {$description.=$genheader . $genbody . $divfooter;}
$progbody="";
if ($phase_array['Prog Available'] == '0' ) {
  $progbody.="      <LI><A HREF=\"Postgrid.php?conid=$conid\">Schedule Grid</A></LI>\n";
  $progbody.="      <LI><A HREF=\"PubsSched.php?format=desc&conid=$conid\">Class Descriptions</A>\n";
  $progbody.="        <A HREF=\"PubsSched.php?format=desc&conid=$conid&short=Y\">(short)</A></LI>\n";
  $progbody.="      <LI><A HREF=\"PubsSched.php?format=sched&conid=$conid\">Schedule</A>\n";
  $progbody.="        <A HREF=\"PubsSched.php?format=sched&conid=$conid&short=Y\">(short)</A></LI>\n";
  $progbody.="      <LI><A HREF=\"PubsSched.php?format=tracks&conid=$conid\">Tracks</A>\n";
  $progbody.="        <A HREF=\"PubsSched.php?format=tracks&conid=$conid&short=Y\">(short)</A></LI>\n";
  $progbody.="      <LI><A HREF=\"PubsSched.php?format=trtime&conid=$conid\">Tracks by Time</A>\n";
  $progbody.="        <A HREF=\"PubsSched.php?format=trtime&conid=$conid&short=Y\">(short)</A></LI>\n";
  $progbody.="      <LI><A HREF=\"PubsSched.php?format=rooms&conid=$conid\">Rooms</A>\n";
  $progbody.="        <A HREF=\"PubsSched.php?format=rooms&conid=$conid&short=Y\">(short)</A></LI>\n";
  $progbody.="      <LI><A HREF=\"PubsBios.php?conid=$conid\">Presenter Bios</A>\n";
  $progbody.="        <A HREF=\"PubsBios.php?conid=$conid&short=Y\">(short)</A></LI>\n";
}
if ($phase_array['Brainstorm'] == '0' ) {
  $progbody.="      <LI><A HREF=\"BrainstormRedirectLogin.php?conid=$conid\">Class/Presenter Submission</A></LI>\n";
  $progbody.="      <LI><A HREF=\"BrainstormRedirectLogin.php?conid=$conid\">View Suggested Classes</A></LI>\n";
  $progbody.="      <LI>\n";
  $progbody.="      <FORM name=\"brainstormform\" method=\"POST\" action=\"doLogin.php\">\n";
  $progbody.="        <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
  $progbody.="        <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
  $progbody.="        <INPUT type=\"hidden\" name=\"target\" value=\"brainstorm\">\n";
  $progbody.="        <INPUT type=\"submit\" name=\"submit\" value=\"Class/Presenter Submission\">\n";
  $progbody.="      </FORM>\n";
  $progbody.="      <FORM name=\"brainstormviewform\" method=\"POST\" action=\"doLogin.php\">\n";
  $progbody.="        <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
  $progbody.="        <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
  $progbody.="        <INPUT type=\"hidden\" name=\"target\" value=\"brainstorm\">\n";
  $progbody.="        <INPUT type=\"submit\" name=\"submit\" value=\"View Suggested Classes\">\n";
  $progbody.="      </FORM>\n";
  $progbody.="      </LI>\n";
}
if ($phase_array['Photo Submission'] == '0') {
  $progbody.="      <LI>\n";
  $progbody.="      <FORM name=\"photosubmitform\" method=\"POST\" action=\"doLogin.php\">\n";
  $progbody.="        <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
  $progbody.="        <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
  $progbody.="        <INPUT type=\"hidden\" name=\"target\" value=\"photo\">\n";
  $progbody.="        <INPUT type=\"submit\" name=\"submit\" value=\"Propose to Submit to the Photo Lounge\">\n";
  $progbody.="      </FORM>\n";
  $progbody.="      </LI>\n";
}
if ($phase_array['Feedback Available'] == '0') {
  $progbody.="      <LI><A HREF=\"Feedback.php?conid=$conid\">Feedback</A></LI>\n";
}
if ($nowis < $constart) { 
  $progbody.="      <LI><A HREF=\"login.php?newconid=$conid\">Presenter/Volunteer Login</A></LI>\n";
} else {
  $progbody.="      <LI><A HREF=\"login.php?newconid=$conid\">Presenter Login</A></LI>\n";
}
if ($progbody!="") {$description.=$progheader . $progbody . $divfooter;}
$volbody="";
if ($phase_array['Vol Available'] == '0' ) {
  $volbody.="      <LI><A HREF=\"Postgrid.php?volunteer=y&conid=$conid\">Volunteer Grid</A></LI>\n";
  $volbody.="      <LI><A HREF=\"VolsSched.php?format=desc&conid=$conid\">Job Descriptions</A>\n";
  $volbody.="        <A HREF=\"VolsSched.php?format=desc&conid=$conid&short=Y\">(short)</A></LI>\n";
  $volbody.="      <LI><A HREF=\"VolsSched.php?format=sched&conid=$conid\">Schedule</A>\n";
  $volbody.="        <A HREF=\"VolsSched.php?format=sched&conid=$conid&short=Y\">(short)</A></LI>\n";
  $volbody.="      <LI><A HREF=\"VolsSched.php?format=rooms&conid=$conid\">Posts</A>\n";
  $volbody.="        <A HREF=\"VolsSched.php?format=rooms&conid=$conid&short=Y\">(short)</A></LI>\n";
}
if ($volbody!="") {$description.=$volheader . $volbody . $divfooter;}
$vendbody="";
if ($phase_array['Vendors Available'] == '0' ) {
  $vendbody.="      <LI><A HREF=\"Vendors.php?conid=$conid\">Vendor List</A></LI>\n";
}
if ($phase_array['Vendor'] == '0' ) {
  $vendbody.="      <LI>\n";
  $vendbody.="      <FORM name=\"vendorform\" method=\"POST\" action=\"doLogin.php\">\n";
  $vendbody.="        <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
  $vendbody.="        <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
  $vendbody.="        <INPUT type=\"hidden\" name=\"target\" value=\"vendor\">\n";
  $vendbody.="        <INPUT type=\"submit\" name=\"submit\" value=\"New Vendor Application\">\n";
  $vendbody.="      </FORM>\n</LI>\n";
}
if ($vendbody!="") {$description.=$vendheader . $vendbody . $divfooter;}
$description.="</DIV>\n";

$geninfo="";
if (file_exists("../Local/$conid/Gen_Info")) {
  $geninfo.="<DIV style=\" display: block; width: 100%; float: left; \">\n";
  $geninfo.=file_get_contents("../Local/$conid/Gen_Info");
  $geninfo.="</DIV>\n";
}

/* Printing body.  Uses the page-init then creates the vendor bio page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo $geninfo;
echo $conchairletter;
echo $orgletter;
echo $rules;
correct_footer();

