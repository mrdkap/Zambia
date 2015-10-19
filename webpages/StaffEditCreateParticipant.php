<?php
require_once ('StaffCommonCode.php');
require ('StaffEditCreateParticipant_FNC.php');
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

if (isset($_GET['action'])) {
  $action=$_GET['action'];
} elseif (isset($_POST['action'])) {
  $action=$_POST['action'];
} else {
   $title="Edit or Add Participant";
   $message_error="Required parameter 'action' not found.  Can't continue.<BR>\n";
   RenderError($title,$message_error);
   exit();
 }
if (!($action=="edit"||$action=="create"||$action=="migrate")) {
  $title="Edit or Add Participant";
  $message_error="Parameter 'action' contains invalid value.  Can't continue.<BR>\n";
  RenderError($title,$message_error);
  exit();
 }

if ($action=="create") { //initialize participant array
  $title="Add Participant";
  $description ="<P>Please use this only if you have already checked with ";
  $description.="<A HREF=\"StaffEditCreateParticipant.php?action=migrate\">Migrate Participant</A> ";
  $description.="and they are not there.</P>\n";

  // If the information has already been added, and we are
  // on the return loop, add the Participant to the database.
  if ((isset ($_POST['update'])) and ($_POST['update']=="Yes")) {
    list($message,$message_error)=create_participant ($_POST);
  }

  // Get a set of bioinfo, not for the info, but for the arrays.
  $bioinfo=getBioData($_SESSION['badgeid']);

  /* We are only updating the raw bios here, so only a 3-depth
   search happens on biolang, biotypename, and biodest. */
  $biostate='raw'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
  for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
    for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
      for ($l=0; $l<count($bioinfo['biodest_array']); $l++) {

	// Setup for keyname, to collapse all four variables into one passed name.
	$biotype=$bioinfo['biotype_array'][$i];
	$biolang=$bioinfo['biolang_array'][$j];
	// $biostate=$bioinfo['biostate_array'][$k];
	$biodest=$bioinfo['biodest_array'][$l];
	$keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";

	// Clear the values.
	$participant_arr[$keyname]="";
      }
    }
  }

  // Clear the values.
  $participant_arr['password']=md5("changeme");
  $participant_arr['badgeid']="auto-assigned";
  $participant_arr['bestway']=""; //null means hasn't logged in yet.
  $participant_arr['interested']=""; //null means hasn't logged in yet.
  $participant_arr['permroleid']=""; //null means hasn't logged in yet.
  $participant_arr['altcontact']="";
  $participant_arr['prognotes']="";
  $participant_arr['pubsname']="";
  $participant_arr['firstname']="";
  $participant_arr['lastname']="";
  $participant_arr['badgename']="";
  $participant_arr['phone']="";
  $participant_arr['email']="";
  $participant_arr['postaddress1']="";
  $participant_arr['postaddress2']="";
  $participant_arr['postcity']="";
  $participant_arr['poststate']="";
  $participant_arr['postzip']="";

  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  RenderEditCreateParticipant($title,$action,$participant_arr,$message,$message_error);
  correct_footer();
 } else {
  // get participant array from database
  $title="Edit Participant";
  $description="";
  if ($action=="migrate") {
    $title="Migrate Participant";
    $description="<P>Locate someone who already exists, and migrate them to ".$_SESSION['conname']." so they can be appropriately utilized.</P>\n";
  }
  // Collapse the three choices into one
  if ($_POST["partidl"]!=0) {$_POST["partid"]=$_POST["partidl"];}
  if ($_POST["partidf"]!=0) {$_POST["partid"]=$_POST["partidf"];}
  if ($_POST["partidp"]!=0) {$_POST["partid"]=$_POST["partidp"];}
  if ($_POST["partide"]!=0) {$_POST["partid"]=$_POST["partide"];}

  if (isset($_POST["partid"])) {
    $selpartid=$_POST["partid"];
  } elseif (isset($_GET["partid"])) {
    $selpartid=$_GET["partid"];
  } else {
    $selpartid=0;
  }

  topofpagereport($title,$description,$additionalinfo,$message,$message_error);

  //Choose the individual from the database
  if ($action=="migrate") {
    select_participant($selpartid, 'ALL', "StaffEditCreateParticipant.php?action=migrate");
  } else {
    select_participant($selpartid, "'Yes'", "StaffEditCreateParticipant.php?action=edit");
  }

  //Stop page here if and individual has not yet been selected
  if ($selpartid==0) {
    correct_footer();
    exit();
  }

  //If we are on the loop with an update, update the database
  // with the current version of the information
  if ((isset ($_POST['update'])) and ($_POST['update'] == "Yes")) {
    edit_participant ($_POST);
  }

  //Get Participant information for updating
  $participant_arr['badgeid']=$selpartid;
  $partid=mysql_real_escape_string($selpartid,$link);
  $query= <<<EOD
SELECT
    badgeid,
    firstname,
    lastname,
    badgename,
    phone,
    email,
    postaddress1,
    postaddress2,
    postcity,
    poststate,
    postzip,
    regtype,
    bestway,
    pubsname,
    altcontact,
    prognotes,
    permroleid_list,
    group_concat(conroleid) as 'conroleid_list'
  FROM
      CongoDump
    JOIN Participants USING (badgeid)
    LEFT JOIN (
      SELECT
          badgeid,
          group_concat(permroleid) AS permroleid_list
        FROM
            UserHasPermissionRole
        WHERE
            badgeid="$selpartid" AND
            conid=$conid) AS X USING (badgeid)
    JOIN UserHasConRole USING (badgeid)
  WHERE
    conid=$conid AND
    badgeid='$selpartid'
EOD;
  if (($result=mysql_query($query,$link))===false) {
    $message_error="Error retrieving data from database<BR>\n";
    $message_error.=$query;
    RenderError($title,$message_error);
    exit();
  }
  if (mysql_num_rows($result)!=1) {
    $message_error="Database query did not return expected number of rows (1).<BR>\n";
    $message_error.=$query;
    RenderError($title,$message_error);
    exit();
  }
  $participant_arr=mysql_fetch_array($result,MYSQL_ASSOC);

  // Get interested as in participating in current con
  $query="SELECT interestedtypeid FROM Interested WHERE badgeid=$selpartid AND conid=$conid";
  if (($result=mysql_query($query,$link))===false) {
    $message_error="Error retrieving data from database<BR>\n";
    $message_error.=$query;
    RenderError($title,$message_error);
    exit();
  }
  list($participant_arr['interested'])=mysql_fetch_array($result,MYSQL_NUM);

  // Get a set of bioinfo, and map it to the appropriate $participant_arr.
  $bioinfo=getBioData($selpartid);

  /* We are only updating the raw bios here, so only a 3-depth
     search happens on biolang, biotypename, and biodest. */
  $biostate='raw'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
  for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
    for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
      for ($l=0; $l<count($bioinfo['biodest_array']); $l++) {

	// Setup for keyname, to collapse all three variables into one passed name.
	$biotype=$bioinfo['biotype_array'][$i];
	$biolang=$bioinfo['biolang_array'][$j];
	// $biostate=$bioinfo['biostate_array'][$k];
	$biodest=$bioinfo['biodest_array'][$l];
	$keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";

	// Set the values.
	$participant_arr[$keyname]=$bioinfo[$keyname];
      }
    }
  }
  RenderEditCreateParticipant($title,$action,$participant_arr,$message,$message_error);
  echo "<DIV class=\"sectionheader\">\n";
  $printname=htmlspecialchars($participant_arr['pubsname']);
  echo "<A HREF=AdminParticipants.php?partid=$selpartid>Edit password for $printname</A> ::\n";
echo "<A HREF=StaffSched.php?format=desc&conid=$conid&feedback=Y&badgeid=$selpartid>Show feedback for $printname</A> :: \n";
  if (may_I(SuperLiaison)) {
    echo "<A HREF=StaffEditCompensation.php?partid=$selpartid>Set Compensation for $printname</A> ::\n";
  }
  if (may_I(Participant)) {
    echo "<A HREF=ClassIntroPrint.php?individual=$selpartid>Print Intros for $printname</A> ::\n";
    echo "<A HREF=WelcomeLettersPrint.php?individual=$selpartid>Print Welcome Letter for $printname</A> ::\n";
  }
  echo "<A HREF=SchedulePrint.php?individual=$selpartid>Print Schedule for $printname</A>\n";
  echo "</DIV>\n";
  // Show previous notes added, for references, and end page
  show_participant_notes ($selpartid);
}
?>
