<?php
global $participant,$message_error,$message2,$congoinfo;
require_once('StaffCommonCode.php');
$conid=$_SESSION['conid'];
$LanguageList=LANGUAGE_LIST; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($LanguageList=="LANGUAGE_LIST") {unset($LanguageList);}

$title="Staff - Manage Participant Biographies";
$description="<P>Report of status of Participant Biographies.</P>";
$additionalinfo ="<P>This report is limited to participants who are currently listed as attending and interested in particpating.</P>\n";
$staffadditionalinfo ="<P>These are the con staff members who have reports.</P>\n";
$descadditionalinfo="<P>These are the descriptions for the schedule elements for this event.</P>\n";
$message_error.=$message2;

if (!empty($_GET['badgeids'])) {
  $addinfo.="<P>Click on the participant name in the table below to edit their biography or";
  $addinfo.=" <A HREF=StaffManageBios.php>return</A> to the editing matrix.</P>\n";
  $descadditionalinfo.="<P>Click on the schedule element number in the table below to edit it or";
  $descadditionalinfo.=" <A HREF=StaffManageBios.php>return</A> to the editing matrix.</P>\n";
  $badgeid_list=$_GET['badgeids'];
 } else {
  $addinfo.="<P>Select with which category you would like to work with and click on the number.</P>\n";
  $descadditionalinfo.="<P>Select with which category you would like to work with and click on the number.</P>\n";
  $addinfo.="<P>(We currently do not use the \"good\" category of bio.)</P>\n";
  $descadditionalinfo.="<P>(We currently only use the \"good\" category for descriptions.)</P>\n";
  $badgeid_list="";
 }

$additionalinfo.=$addinfo;
$staffadditionalinfo.=$addinfo;
$descadditionalinfo.="<P>Still to come. Easier to edit elswhere still: \n";
$descadditionalinfo.="<A HREF=genreport.php?reportname=conflictsessdesc>Missing Web or ";
$descadditionalinfo.="Program Book Description</A>::<A HREF=StaffSched.php?format=desc>";
$descadditionalinfo.="Session Descriptions</A>::<A HREF=bookSched.php?format=desc>Book ";
$descadditionalinfo.="Ready Descriptions</A></P>\n";

if (isset($_GET['unlock'])) {
  $unlockresult=unlock_participant($_GET['unlock']);
 }

/* Categories
 Rows:
  No raw bio
  No edited bio
  No good bio (* only liaison)
  Raw bio different from edited bio
  Edited bio different from good bio (* only liaison)
  Raw, edited, and good in agreement (* no links)
 Columns:
  each lang/type/dest combination.  Eg:
  en-us web web, en-us web book, en-us book web, en-us book book,
  en-us bio book, en-us bio web, en-us name web, en-us name book,
  en-us uri web, en-us uri book, en-us picture web, en-us picture book,
  en-uk bio web, en-uk bio book, fr bio web, fr bio book ...
*/

// Participants
$query1= <<<EOD
SELECT
    B.badgeid,
    biostatename,
    concat(biolang, " ", biotypename, " ", biodestname) AS col,
    LB.pubsname AS lockedby,
    P.pubsname,
    biotext
  FROM
      Participants P
    JOIN Bios B USING (badgeid)
    JOIN BioTypes USING (biotypeid)
    JOIN BioStates USING (biostateid)
    JOIN BioDests USING (biodestid)
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
    JOIN Interested USING (badgeid,conid)
    JOIN InterestedTypes USING (interestedtypeid)
    LEFT JOIN Participants LB on B.biolockedby = LB.badgeid
  WHERE
    interestedtypename in ('Yes') AND
    conid=$conid AND
    biotypename not in ('web','book') AND
    (permrolename in ('Participant') OR
     permrolename like '%Super%')
EOD;

// Specific set of badgeids.
if ((!empty($_GET['badgeids'])) and ($_GET['qno']==1)) {
  $query1.=" AND B.badgeid in (".$badgeid_list.")";
 }

// Specific languages.
if (isset($LanguageList)) {
  $query1.=" AND biolang in $LanguageList";
 }

// Give some semblance of order to the names
$query1.=" ORDER BY P.pubsname";

if (($result=mysql_query($query1,$link))===false) {
  $message_error.=$query1."<BR>\nError retrieving data from database.\n";
  RenderError($title,$message_error);
  exit();
 }

$numrows=mysql_num_rows($result);

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
  $check_element[$row['badgeid']][$row['col']][$row['biostatename']]=$row['biotext'];
  $count_badgeid[$row['badgeid']]++;
  $pubsname[$row['badgeid']]=$row['pubsname'];
  /*if (((!isset($lockedby[$row['badgeid']])) or ($lockedby[$row['badgeid']] = "")) and
   (isset($row['lockedby']))) { */
  if (isset($row['lockedby'])) {
    $lockedby[$row['badgeid']]=$row['lockedby'];
   }
  $count_col[$row['col']]++;
 }

// Staff
$query2= <<<EOD
SELECT
    B.badgeid,
    biostatename,
    concat(biolang, " ", biotypename, " ", biodestname) AS col,
    LB.pubsname AS lockedby,
    P.pubsname,
    biotext
  FROM
      Participants P
    JOIN Bios B USING (badgeid)
    JOIN BioTypes USING (biotypeid)
    JOIN BioStates USING (biostateid)
    JOIN BioDests USING (biodestid)
    JOIN UserHasConRole USING (badgeid)
    JOIN ConRoles USING (conroleid)
    JOIN HasReports USING (conroleid,conid)
    LEFT JOIN Participants LB on B.biolockedby = LB.badgeid
  WHERE
    conid=$conid AND
    biotypename not in ('web','book')
EOD;

// Specific set of badgeids.
if ((!empty($_GET['badgeids'])) and ($_GET['qno']==2)) {
  $query2.=" AND B.badgeid in (".$badgeid_list.")";
 }

// Specific languages.
if (isset($LanguageList)) {
  $query2.=" AND biolang in $LanguageList";
 }

// Give some semblance of order to the names
$query2.=" ORDER BY P.pubsname";

if (($result=mysql_query($query2,$link))===false) {
  $message_error.=$query2."<BR>\nError retrieving data from database.\n";
  RenderError($title,$message_error);
  exit();
 }

$numstaffrows=mysql_num_rows($result);

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
  $check_staff_element[$row['badgeid']][$row['col']][$row['biostatename']]=$row['biotext'];
  $count_staff_badgeid[$row['badgeid']]++;
  $staff_pubsname[$row['badgeid']]=$row['pubsname'];
  /*if (((!isset($lockedby[$row['badgeid']])) or ($lockedby[$row['badgeid']] = "")) and
   (isset($row['lockedby']))) { */
  if (isset($row['lockedby'])) {
    $staff_lockedby[$row['badgeid']]=$row['lockedby'];
   }
  $count_staff_col[$row['col']]++;
 }

//Descriptions (hacked at the moment)
$query3=<<<EOD
SELECT
    sessionid AS badgeid,
    biostatename,
    concat(descriptionlang, " ", descriptiontypename, " ", biodestname) AS col,
    pubsname AS lockedby,
    title AS pubsname,
    descriptiontext AS biotext
  FROM
      Descriptions
    JOIN Sessions USING (sessionid,conid)
    JOIN DescriptionTypes USING (descriptiontypeid)
    JOIN BioStates USING (biostateid)
    JOIN BioDests USING (biodestid)
    JOIN SessionStatuses USING (statusid)
    JOIN Divisions USING (divisionid)
    LEFT JOIN Participants ON (descriptionlockedby = badgeid)
  WHERE
    conid=46 AND
    statusname in ("Scheduled") AND
    divisionname in ("Programming")
EOD;

// Specific set of badgeids.
if ((!empty($_GET['badgeids'])) and ($_GET['qno']==3)) {
  $query3.=" AND sessionid in (".$badgeid_list.")";
 }

// Specific languages.
if (isset($LanguageList)) {
  $query3.=" AND descriptionlang in $LanguageList";
 }

// Give some semblance of order to the names
$query3.=" ORDER BY title";

if (($result=mysql_query($query3,$link))===false) {
  $message_error.=$query3."<BR>\nError retrieving data from database.\n";
  RenderError($title,$message_error);
  exit();
 }

$numdescrows=mysql_num_rows($result);

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
  $check_desc_element[$row['badgeid']][$row['col']][$row['biostatename']]=$row['biotext'];
  $count_desc_badgeid[$row['badgeid']]++;
  $desc_pubsname[$row['badgeid']]=$row['pubsname'];
  /*if (((!isset($lockedby[$row['badgeid']])) or ($lockedby[$row['badgeid']] = "")) and
   (isset($row['lockedby']))) { */
  if (isset($row['lockedby'])) {
    $desc_lockedby[$row['badgeid']]=$row['lockedby'];
   }
  $count_desc_col[$row['col']]++;
 }
// Set up the necessary switches so we can know what exactly is being operated on.
$printquery1=renderbiosreport($badgeid_list,1,$check_element,$numrows,$count_badgeid,$count_col,$pubsname,$lockedby);
$printquery2=renderbiosreport($badgeid_list,2,$check_staff_element,$numstaffrows,$count_staff_badgeid,$count_staff_col,$staff_pubsname,$staff_lockedby);
$printquery3=renderbiosreport($badgeid_list,3,$check_desc_element,$numdescrows,$count_desc_badgeid,$count_desc_col,$desc_pubsname,$desc_lockedby);

//Start the page
if (!empty($_GET['badgeids'])) {
  if ($_GET['qno']=="1") {
    topofpagereport($title,$description,$additionalinfo,$message,$message_error);
    echo $printquery1;
  } elseif ($_GET['qno']=="2") {
    $additionalinfo=$staffadditionalinfo;
    topofpagereport($title,$description,$additionalinfo,$message,$message_error);
    echo $printquery2;
  } elseif ($_GET['qno']=="3") {
    $additionalinfo=$descadditionalinfo;
    topofpagereport($title,$description,$additionalinfo,$message,$message_error);
    echo $printquery3;
  } else {
    topofpagereport($title,$description,$additionalinfo,$message,$message_error);
    echo "<P>Query Number: " . $_GET['qno'] . " doesn't match any allowed values.</P>\n";
  }
} else {
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo $printquery1;
  echo $staffadditionalinfo;
  echo $printquery2;
  echo $descadditionalinfo;
  echo $printquery3;
}

// End the page correctly
correct_footer();
exit();
?>