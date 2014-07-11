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

// Set the header information correctly
$description.="<CENTER><P>- <A HREF=\"ConStaffBios.php?conid=$conid\">Con Staff</A>\n";
if ($phase_array['Prog Available'] == '0' ) {
  $description.=" - <A HREF=\"Postgrid.php?conid=$conid\">Schedule Grid</A>\n";
  $description.=" - <A HREF=\"Descriptions.php?conid=$conid\">Class Descriptions</A>\n";
  $description.=" - <A HREF=\"Schedule.php?conid=$conid\">Schedule</A>\n";
  $description.=" - <A HREF=\"Tracks.php?conid=$conid\">Tracks</A>\n";
  $description.=" - <A HREF=\"Bios.php?conid=$conid\">Presenter Bios</A>\n";
}
if ($phase_array['Vendors Available'] == '0' ) {
  $description.=" - <A HREF=\"Vendors.php?conid=$conid\">Vendor List</A>\n";
}
if ($phase_array['Venue Available'] == '0' ) {
  $description.=" - <A HREF=\"Venue.php?conid=$conid\">Venue Information</A>\n";
}
if ($phase_array['Vol Available'] == '0' ) {
  $description.=" - <A HREF=\"Postgrid.php?volunteer=y&conid=$conid\">Volunteer Grid</A>\n";
  $description.=" - <A HREF=\"Descriptions.php?volunteer=y&conid=$conid\">Volunteer Job Descriptions</A>\n";
}
if ($nowis < $constart) { 
  if ($phase_array['Brainstorm'] == '0' ) {
    $description.=" -";
    $description.="  <FORM name=\"brainstormform\" method=\"POST\" action=\"doLogin.php\">\n";
    $description.="    <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
    $description.="    <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
    $description.="    <INPUT type=\"hidden\" name=\"target\" value=\"brainstorm\">\n";
    $description.="    <INPUT type=\"submit\" name=\"submit\" value=\"Class/Presenter Submission\">\n";
    $description.="  </FORM>\n";
  }
  if ($phase_array['Vendor'] == '0' ) {
    $description.=" -";
    $description.="  <FORM name=\"vendorform\" method=\"POST\" action=\"doLogin.php\">\n";
    $description.="    <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
    $description.="    <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
    $description.="    <INPUT type=\"hidden\" name=\"target\" value=\"vendor\">\n";
    $description.="    <INPUT type=\"submit\" name=\"submit\" value=\"New Vendor Application\">\n";
    $description.="  </FORM>\n";
  }
} else { 
  if ($phase_array['Feedback Available'] == '0') {
    $description.=" - <A HREF=\"Feedback.php?conid=$conid\">Feedback</A>\n";
  }
}


$geninfo="";
if (file_exists("../Local/$conid/Gen_Info")) {
  $geninfo.=file_get_contents("../Local/$conid/Gen_Info");
}

$conchairletter="";
if (file_exists("../Local/$conid/Con_Chair_Welcome")) {
  $description.=" - <A HREF=\"#conchairletter\">Welcome from the Con Chair</A>\n";
  $conchairletter.="<A NAME=\"conchairletter\">&nbsp;</A>\n";
  $conchairletter.="<HR>\n<H3>A welcome letter from the Con Chair:</H3>\n<BR>\n";
  $conchairletter.=file_get_contents("../Local/$conid/Con_Chair_Welcome");
}

$orgletter="";
if (file_exists("../Local/$conid/Org_Welcome")) {
  $description.=" - <A HREF=\"#orgletter\">Welcome from the Organization</A>\n";
  $orgletter.="<A NAME=\"orgletter\">&nbsp;</A>\n";
  $orgletter.="<HR>\n<H3>A welcome letter from the Organization:</H3>\n<BR>\n";
  $orgletter.=file_get_contents("../Local/$conid/Org_Welcome");
}

$rules="";
if (file_exists("../Local/$conid/Rules")) {
  $description.=" - <A HREF=\"#rules\">Rules</A>\n";
  $rules.="<A NAME=\"rules\">&nbsp;</A>\n";
  $rules.="<HR>\n<H3>Rules:</H3>\n<BR>\n";
  $rules.=file_get_contents("../Local/$conid/Rules");
}

$description.=" -</P></CENTER>\n";

/* Printing body.  Uses the page-init then creates the vendor bio page. */
topofpagereport($title,$description,$additionalinfo);
echo $geninfo;
echo $conchairletter;
echo $orgletter;
echo $rules;
correct_footer();

