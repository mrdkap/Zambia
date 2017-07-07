<?php
$logging_in=true;

// Set the conid
if ((!empty($_POST['newconid'])) and (is_numeric($_POST['newconid']))) {
  $_SESSION['conid']=$_POST['newconid'];
  $conid=$_POST['newconid'];
}

require_once ('CommonCode.php');

$title="Submit Password";
if (isset($_POST['badgeid'])) {
  if (is_numeric($_POST['badgeid'])) {
    $badgeid = $_POST['badgeid'];
  } elseif ((is_email($_POST['badgeid'])) and
	    ($_POST['badgeid'] != "none@available.com") and
	    ($_POST['badgeid'] != "none@nelaonline.org")) {
    $emailaddr = strtolower($_POST['badgeid']);
  }
}
$password = stripslashes($_POST['passwd']);

// echo "Trying to connect to database.\n";
if (prepare_db()===false) {
  $message_error.="Unable to connect to database.<BR>No further execution possible.";
  RenderError($title,$message_error);
  exit();
};
// echo "Connected to database.\n";

// Email Address test
if ((!isset($badgeid)) and (isset($emailaddr))) {
  $result=mysql_query("SELECT badgeid FROM CongoDump WHERE email='".$emailaddr."'",$link);
  if (!$result) {
    $message_error.="Incorrect BadgeID, email address or password - please be aware that BadgeID, email address and password are case sensitive and try again.";
    require ('login.php');
    exit();
  }

  // Badgeid set, only if unique
  if (mysql_num_rows($result)==1) {
    $dbobject=mysql_fetch_object($result);
    $badgeid=$dbobject->badgeid;
  } else {
   $message_error.="Incorrect BadgeID, email address or password - please be aware that BadgeID, email address and password are case sensitive and try again.";
    require ('login.php');
    exit();
  }
}

//Badgid test
$result=mysql_query("SELECT password,password2 FROM Participants WHERE badgeid='".$badgeid."'",$link);
if ((!$result) || (mysql_num_rows($result)!=1)) {
  $message_error.="Incorrect BadgeID, email address or password - please be aware that BadgeID, email address and password are case sensitive and try again.";
  require ('login.php');
  exit();
}

// Password check
$dbobject=mysql_fetch_object($result);
$dbpassword=$dbobject->password;
$dbpassword2=$dbobject->password2;
//echo $badgeid."<BR>".$dbpassword."<BR>".$password."<BR>".md5($password);
//exit(0);
// For 5.5
//if (!password_verify($password,$dbpassword2)) {
  if (md5($password)!=$dbpassword) {

    $message_error.="Incorrect BadgeID, email address or password - please be aware that";
    $message_error.=" BadgeID, email address and password are case sensitive and try ";
    $message_error.="again, or reset your password by clicking the button below, and ";
    $message_error.="have the new one emailed to you.\n";
    $message_error.="<CENTER><FORM name=\"passchange\" method=POST action=\"passgen.php\">\n";
    $message_error.="<INPUT type=\"hidden\" name=\"badgeid\" value=\"".$badgeid."\">\n";
    $message_error.="<INPUT type=\"hidden\" name=\"Update\" value=\"Y\">\n";
    $message_error.="<INPUT type=\"submit\" value=\"Password Change\">\n</FORM></CENTER>\n";
    require ('login.php');
    exit(0);
  }
// For 5.5
//}

// Get and set information on individual
$result=mysql_query("SELECT badgename FROM CongoDump WHERE badgeid='".$badgeid."'",$link);
if ($result) {
  $dbobject=mysql_fetch_object($result);
  $badgename=$dbobject->badgename;
  $_SESSION['badgename']=$badgename;
} else {
  $_SESSION['badgename']="";
}
$result=mysql_query("SELECT pubsname FROM Participants WHERE badgeid='".$badgeid."'",$link);
$pubsname="";
if ($result) {
  $dbobject=mysql_fetch_object($result);
  $pubsname=$dbobject->pubsname;
}
if (!($pubsname=="")) {
  $_SESSION['badgename']=$pubsname;
}
$_SESSION['badgeid']=$badgeid;
$_SESSION['password']=$dbpassword;
set_permission_set($badgeid);
//error_log("Zambia: Completed set_permission_set.\n");

// Was Previous Presenter
$querypresent=<<<EOF
SELECT
    GROUP_CONCAT(DISTINCT conname SEPARATOR ", ") AS ConName
  FROM
      UserHasPermissionRole
    JOIN PermissionRoles USING (permroleid)
    JOIN ConInfo USING (conid)
    JOIN Interested USING (badgeid,conid)
    JOIN InterestedTypes USING (interestedtypeid)
  WHERE
    permrolename="Participant" AND
    interestedtypename in ("Yes","Invited") AND
    badgeid not in (SELECT
          badgeid
        FROM
            Interested
          JOIN InterestedTypes USING (interestedtypeid)
        WHERE
          interestedtypename="Not Accepted") AND
    badgeid=$badgeid
  GROUP BY
    badgeid
EOF;
$result=mysql_query($querypresent,$link);
if ($result and (mysql_num_rows($result)==1)) {
  $dbobject=mysql_fetch_object($result);
  $prevconpresent=$dbobject->ConName;
}

// Was Previous Photo Submitter
$queryphoto=<<<EOF
SELECT
    GROUP_CONCAT(DISTINCT conname SEPARATOR ", ") AS ConName
  FROM
      UserHasPermissionRole
    JOIN PermissionRoles USING (permroleid)
    JOIN ConInfo USING (conid)
    JOIN Interested USING (badgeid,conid)
    JOIN InterestedTypes USING (interestedtypeid)
  WHERE
    permrolename="PhotoSub" AND
    interestedtypename in ('Suggested','Yes','Invited') AND
    badgeid not in (SELECT
          badgeid
        FROM
            Interested
          JOIN InterestedTypes USING (interestedtypeid)
        WHERE
          interestedtypename="Not Accepted") AND
    badgeid=$badgeid
  GROUP BY
    badgeid
EOF;
$result=mysql_query($queryphoto,$link);
if ($result and (mysql_num_rows($result)==1)) {
  $dbobject=mysql_fetch_object($result);
  $prevconphotosub=$dbobject->ConName;
}

// Con date has passed
$querypast=<<<EOF
SELECT
    if ((constartdate < CURRENT_DATE()),"Passed","Upcoming") AS IsPassed
  FROM
      ConInfo
  WHERE
    conid=$conid
EOF;
$result=mysql_query($querypast,$link);
if ($result and (mysql_num_rows($result)==1)) {
  $dbobject=mysql_fetch_object($result);
  $past_p=$dbobject->IsPassed;
}

// Brainstorm open in the form of "Suggestions"
$querybrainstorm=<<<EOF
SELECT
    phasestate
  FROM
      Phase
    JOIN PhaseTypes USING (phasetypeid)
  WHERE
    conid=$conid AND
    phasetypename in ('Suggestions')
EOF;
$result=mysql_query($querybrainstorm,$link);
if ($result and (mysql_num_rows($result)==1)) {
  $dbobject=mysql_fetch_object($result);
  $brainstorm_p=$dbobject->phasestate;
}

// Photo Submissions open
$queryphotosub=<<<EOF
SELECT
    phasestate
  FROM
      Phase
    JOIN PhaseTypes USING (phasetypeid)
  WHERE
    conid=$conid AND
    phasetypename in ('Photo Submission')
EOF;
$result=mysql_query($queryphotosub,$link);
if ($result and (mysql_num_rows($result)==1)) {
  $dbobject=mysql_fetch_object($result);
  $photosub_p=$dbobject->phasestate;
}

/* Switch on which page is shown
   First search to see if any landing page is open to said individual,
   be it Staff, Vendor, Participant, PhotoSub, Photo Sub Returning, or
   Brainstorm.
   Then winnow out anyone who doesn't already have permission, from
   adding themselves to a past event.
   Followed by a test to see if they can add themselves to this
   particular event, having been in our database before.  First as a
   presenter, but only if Brainstorm is open, then as a photo
   submitter but only if Photo Submissions is open.
   Finally error out, at the end, exhausting all possibilities.
 */
if (retrieve_participant_from_db($badgeid)==0) {
  if(may_I('Staff')) {
    require ('StaffPage.php');
  } elseif ((may_I('Vendor')) or ((may_I('public_login')) and ($_POST['target']=="vendor"))) {
    require ('renderVendorWelcome.php');
  } elseif (may_I('Participant')) {
    require ('renderWelcome.php');
  } elseif (may_I('PhotoSub')) {
    require ('PhotoLoungeSubmit.php');
  } elseif ((may_I('public_login')) and ($_POST['target']=="photo")) {
    require ('PhotoLoungeReturning.php');
  } elseif (may_I('public_login')) {
    require ('BrainstormWelcome.php');
  } elseif ($past_p=="Passed") {
    set_permission_set(0);
    $message_error.="The event you have chosen, " . $_SESSION['conname'] . "\n";
    $message_error.="has passed, and you don't have access to it.\n";
    $message_error.="Please pick a <A HREF=\"http://".$_SESSION['conurl']."\">";
    $message_error.="different event</A> or contact a member of the ".$_SESSION['conname']." staff.\n";
    require('logout.php');
  } elseif (isset($prevconpresent)) {
    if ($brainstorm_p != "0") {
      set_permission_set(0);
      $message_error.="The event you have chosen, " . $_SESSION['conname'] . "\n";
      $message_error.="is not open to accepting proposed presenters at this time.\n";
      $message_error.="Please pick a <A HREF=\"http://".$_SESSION['conurl']."\">";
      $message_error.="different event</A> or contact a member of the ".$_SESSION['conname']." staff.\n";
      require('logout.php');
    } else {
      $message.="You previously presented for us at $prevconpresent.\n";
      $message.="Propose yourself to present for this event, by clicking\n";
      $message.="the button below, then log in again.\n";
      $message.="<CENTER><FORM name\"propose\" method=POST action=\"ParticipantReturning.php\">\n";
      $message.="<INPUT type=\"hidden\" name=\"newconid\" value=\"$conid\">\n";
      $message.="<INPUT type=\"hidden\" name=\"perm\" value=\"Part\">\n";
      $message.="<INPUT type=\"hidden\" name=\"who\" value=\"$badgeid\">\n";
      $message.="<INPUT type=\"submit\" value=\"Propose Yourself\">\n</FORM>\n</CENTER\n";
      require('login.php');
    }
  } elseif (isset($prevconphotosub)) {
    if ($photosub_p != "0") {
      set_permission_set(0);
      $message_error.="The event you have chosen, " . $_SESSION['conname'] . "\n";
      $message_error.="is not open to accepting photo submissions at this time.\n";
      $message_error.="Please pick a <A HREF=\"http://".$_SESSION['conurl']."\">";
      $message_error.="different event</A> or contact a member of the ".$_SESSION['conname']." staff.\n";
      require('logout.php');
    } else {
      $message.="You were invited to submit photos for us at $prevconphotosub.\n";
      $message.="Propose yourself to submit photos for this event, by clicking\n";
      $message.="the button below, then log in again.\n";
      $message.="<CENTER><FORM name\"propose\" method=POST action=\"ParticipantReturning.php\">\n";
      $message.="<INPUT type=\"hidden\" name=\"newconid\" value=\"$conid\">\n";
      $message.="<INPUT type=\"hidden\" name=\"perm\" value=\"Photo\">\n";
      $message.="<INPUT type=\"hidden\" name=\"who\" value=\"$badgeid\">\n";
      $message.="<INPUT type=\"submit\" value=\"Propose Yourself\">\n</FORM>\n</CENTER>\n";
      require('login.php');
    }
  } else {
    set_permission_set(0);
    $message_error.="There is a problem with your permission configuration:\n";
    $message_error.="It doesn't have permission to access any welcome page for ";
    $message_error.=$_SESSION['conname'].".\nPlease pick a <A HREF=\"http://".$_SESSION['conurl']."\">";
    $message_error.="different event</A> or contact a member of the ".$_SESSION['conname']." staff.\n";
    require('logout.php');
  }
  exit();
}

// Fail to get db information somewhere ...
$message_error.="<BR>Error retrieving data from DB.  No further execution possible.";
RenderError($title,$message_error);
exit();
?>
