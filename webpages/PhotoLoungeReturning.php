<?php
require_once('PhotoCommonCode.php');
$conid=$_SESSION['conid'];

// LOCALIZATIONS
$_SESSION['return_to_page']='PhotoLoungeReturning.php';
$title="Submit Photos for ".$_SESSION['conname']." Photo Lounge";
$description="<P>Submit Photos for us, please.</P>\n";
$additionalinfo="<P>If your name is listed below, please, simply click your name and\n";
$additionalinfo.="log in using your password on the subsequent page, since you are already\n";
$additionalinfo.="set up to submit photos for this event.</P>\n";

$notyet="<P>If your name is not listed above, and is in the below table, please indicate\n";
$notyet.="that you want to submit photos for us by clicking on your name (and your name only).\n";
$notyet.="You should then be able to <A HREF=\"login.php?newconid=$conid\">log in</A>,\n";
$notyet.="and start submitting photos to your heart's content.</P>\n";

$notatall="<P>If your name is not listed at all, please\n";
$notatall.="<A HREF=\"PhotoLoungeProposed.php\">propose</A> yourself as someone who wants to\n";
$notatall.="submit photographs for the Photo Lounge.  Someone will get back to you soon.</P>\n";

/* Some loop that will take their click-through or form, and set their
   status to "suggested" and migrate them as a presenter to the
   current con-instance. Set interestedtypeid to 4 for "Suggested".
   This should probably become dynamic at some point. */
if ((!empty($_POST['who'])) and (is_numeric($_POST['who']))) {
  $proposed=$_POST['who'];

  /* Check interested table.  If they exist already, leave it well
     enough alone. They might be involved in other areas of the con,
     just not as a presenter yet. */
  $query="SELECT * from Interested WHERE badgeid=\"$proposed\" AND conid=$conid";
  list($rows,$header_array,$interested_array)=queryreport($query,$link,$title,$description,0);

  // If no rows returned, add one.  If more than one row is returned, notify.
  if ($rows==0) {
    $element_array=array('conid','badgeid','interestedtypeid');
    $value_array=array($conid, $proposed, 4);
    $message.=submit_table_element($link,$title,"Interested", $element_array, $value_array);
  } elseif ($rows > 1) {
    $message.="<P>There might be something wrong with the table, for there are\n";
    $message.="multiple entries for you for this year.  Please email\n";
    $message.="<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A>\n";
    $message.="to get things straightened out.  Thank you.</P>\n";
  }

  /* Add to UserHasPermissionRole table. Set permroleid to 41 for
     "PhotoSub".  This should become dynamic at some point. */
  $element_array=array('badgeid','permroleid','conid');
  $value_array=array($proposed, 41, $conid);
  $verbose.=submit_table_element($link,$title,"UserHasPermissionRole", $element_array, $value_array);
  
  $message.="<P>Your login number is: $proposed and your password has not changed.\n";
  $message.="Please <A HREF=\"login.php?login=$proposed&newconid=$conid\">Log In</A> below by\n";
  $message.="clicking on your name.\n<br>\n";
  $message.="If you need help resetting your password, please email\n";
  $message.="<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A>\n";
  $message.="for assistance.</P>\n";
}

/* Get the list of all the previous presenters in our system except
   for those listed as "Not Accepted", and those already on our roles
   as presenting. */
$query=<<<EOD
SELECT
    DISTINCT concat("<FORM name=\"propose\" method=POST action=\"PhotoLoungeReturning.php\">\n",
      "<INPUT type=\"hidden\" name=\"who\" value=\"",badgeid,"\">\n",
      "<INPUT type=\"submit\" value=\"",badgename,"\">\n",
      "</FORM>\n") as "Inactive Photo Submitters"
  FROM
      CongoDump
    JOIN Participants USING (badgeid)
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    permrolename in ('PhotoSub') AND
    badgeid not in (SELECT
          DISTINCT badgeid
        FROM
            Interested
          JOIN InterestedTypes USING (interestedtypeid)
          JOIN Participants USING (badgeid)
          JOIN UserHasPermissionRole USING (badgeid)
          JOIN PermissionRoles USING (permroleid)
        WHERE
          interestedtypename in ('Not Accepted') and
	  permrolename in ('PhotoSub')) AND
    badgeid not in (SELECT
          DISTINCT badgeid
        FROM
            UserHasPermissionRole
          JOIN PermissionRoles USING (permroleid)
        WHERE
          conid=$conid AND
          permrolename in ('PhotoSub'))
  ORDER BY
    badgename
EOD;

// Retrieve query
list($previous_rows,$previous_header_array,$previous_array)=queryreport($query,$link,$title,$description,0);

/* Get the list of all the current photo submitters in our system. */
$query1=<<<EOD
SELECT
    DISTINCT concat("<A HREF=\"login.php?login=",
      badgeid,
      "&newconid=$conid\">",
      badgename,
      "</A>\n") as "Current Photo Submitters"
  FROM
      CongoDump
    JOIN Participants USING (badgeid)
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    permrolename in ('PhotoSub') AND
    badgeid in (SELECT
          DISTINCT badgeid
        FROM
            UserHasPermissionRole
          JOIN PermissionRoles USING (permroleid)
        WHERE
          conid=$conid AND
          permrolename in ('PhotoSub'))
  ORDER BY
    badgename
EOD;

// Retrieve query
list($current_rows,$current_header_array,$current_array)=queryreport($query1,$link,$title,$description,0);

// Display the page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo renderhtmlreport(1,$current_rows,$current_header_array,$current_array);
echo $notyet;
echo $notatall;
echo renderhtmlreport(1,$previous_rows,$previous_header_array,$previous_array);
correct_footer();
?>