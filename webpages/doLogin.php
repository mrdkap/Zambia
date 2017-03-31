<?php
$logging_in=true;

// Set the conid
if ((!empty($_POST['newconid'])) and (is_numeric($_POST['newconid']))) {
  $_SESSION['conid']=$_POST['newconid'];
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
  $message_error="Unable to connect to database.<BR>No further execution possible.";
  RenderError($title,$message_error);
  exit();
};
// echo "Connected to database.\n";

// Email Address test
if ((!isset($badgeid)) and (isset($emailaddr))) {
  $result=mysql_query("SELECT badgeid FROM CongoDump WHERE email='".$emailaddr."'",$link);
  if (!$result) {
    $message="Incorrect BadgeID, email address or password - please be aware that BadgeID, email address and password are case sensitive and try again.";
    require ('login.php');
    exit();
  }

  // Badgeid set, only if unique
  if (mysql_num_rows($result)==1) {
    $dbobject=mysql_fetch_object($result);
    $badgeid=$dbobject->badgeid;
  }
}

//Badgid test
$result=mysql_query("SELECT password FROM Participants WHERE badgeid='".$badgeid."'",$link);
if (!$result) {
  $message="Incorrect BadgeID, email address or password - please be aware that BadgeID, email address and password are case sensitive and try again.";
  require ('login.php');
  exit();
}

// Password check
$dbobject=mysql_fetch_object($result);
$dbpassword=$dbobject->password;
//echo $badgeid."<BR>".$dbpassword."<BR>".$password."<BR>".md5($password);
//exit(0);
if (md5($password)!=$dbpassword) {
  $message="Incorrect BadgeID, email address or password - please be aware that BadgeID, email address and password are case sensitive and try again.";
  require ('login.php');
  exit(0);
}

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
    interestedtypename="Yes" AND
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
    interestedtypename="Suggested" AND
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

// Switch on which page is shown
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
  } elseif (isset($prevconpresent)) {
    /* This should have some sort of reentran piece, allowing a form
       to be set, and relogin to happen.
    */
    $message.="You previously presented for us at $prevconpresent.\n";
    $message.="<A HREF=\"\">Propose</A> yourself for this event, then log in again.\n";
    require('logout.php');
  } elseif (isset($prevconphotosub)) {
    /* This should have some sort of reentran piece, allowing a form
       to be set, and relogin to happen.  Probably much along the
       lines of:
       "<FORM name=\"propose\" method=POST action=\"ProposalPage.php\">\n",
       "<INPUT type=\"hidden\" name=\"who\" value=\"",badgeid,"\">\n",
       "<INPUT type=\"submit\" value=\"",badgename,"\">\n",
       "</FORM>\n"

       and something in the functionality of:
       $message.=photo_lounge_propose($title, $description, $badgeid, $message, $message_error);

       on said ProposalPage.php
    */
    $message.="You were invited to submit photos for us at $prevconphotosub.\n";
    $message.="<A HREF=\"\">Propose</A> to submit photos for this event, then log in again.\n";
    require('logout.php');
  } else {
    set_permission_set(0);
    $message_error.="There is a problem with your permission configuration:\n";
    $message_error.="It doesn't have permission to access any welcome page for ";
    $message_error.=$_SESSION['conname'].".\nPlease pick a <A HREF=\"http://".$_SESSION['conurl']."\">";
    $message_error.="different year</A> or contact a member of the ".$_SESSION['conname']." staff.";
    require('logout.php');
  }
  exit();
}

// Fail to get db information somewhere ...
$message_error="<BR>Error retrieving data from DB.  No further execution possible.";
RenderError($title,$message_error);
exit();
?>
