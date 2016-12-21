<?php
require_once('PhotoCommonCode.php');
global $link;

// LOCALIZATIONS
$_SESSION['return_to_page']='PhotoLoungeReturning.php';
$conid=$_SESSION['conid']; // make it a variable so it can be substituted
$title="Submit Photos for ".$_SESSION['conname']." Photo Lounge";
$description="<P>Welcome!</P>\n";
$additionalinfo="<P>Thank you for contributing to our Photo Lounge. We offer a weekend\n";
$additionalinfo.="long digital photo show at the Winter Fetish Fair Fleamarket with\n";
$additionalinfo.="over 50 photographers featured.</P>\n";

/* The below should probably end up in "verbiage" at some point.
   existing is the words for someone who already is set up for this year.
   notyet is someone who was set up in the past, but not clicked through for this year.
   notatall is a new person, not in our system at all.  (Which might not be entirely true.)
   existing_clear is if everyone has been set up for this year, so the words change.
*/
$existing="<P>If your name is listed below, select your name and\n";
$existing.="log in, since you are already set up to submit photos\n";
$existing.="for this event. If you need your password reset please contact us at\n";
$existing.="<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A>\n";
$existing.="and we will get back to you, post haste, to help.</P>\n";

$notyet="<P>If you are a returning photographer, please find your name in the list below, select\n";
$notyet.="it and then log in. If you need your password reset, please contact us at\n";
$notyet.="<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A>.</P>\n";

$notatall="<P>If you are new to our event, please <A HREF=\"PhotoLoungeProposed.php\">complete\n";
$notatall.="this form</A> so we can create an account for you.  We will be in touch in 2-3 days\n";
$notatall.="with your log-in details so that you can submit your photos.</P>\n";

$existing_clear="<P>If you are a returning photographer, please <A HREF=\"login.php?newconid=$conid\">log\n";
$existing_clear.="in</A> with your id number and password.  If you need your password reset, have\n";
$existing_clear.="forgotten your id number, or both, please contact us at\n";
$existing_clear.="<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A>\n";
$existing_clear.="and we will get back to you, post haste, to help.</P>\n";

/* Set those that clicked through up for this event.  Set their
   status to "suggested" and migrate them as a "PhotoSub" to the
   current con-instance. */

if ((!empty($_POST['who'])) and (is_numeric($_POST['who']))) {

  // If the who is not empty and an actual badgeid ...
  $proposed=$_POST['who'];

  // ... then update them as appropriate.
  $message.=photo_lounge_propose($title, $description, $proposed, $message, $message_error);

}

/* Get the list of all the previous presenters in our system except
   for those listed as "Not Accepted", and those already on our roles
   as presenting. */
$query=<<<EOD
SELECT
    DISTINCT concat("<FORM name=\"propose\" method=POST action=\"PhotoLoungeReturning.php\">\n",
      "<INPUT type=\"hidden\" name=\"who\" value=\"",badgeid,"\">\n",
      "<INPUT type=\"submit\" value=\"",badgename,"\">\n",
      "</FORM>\n") as "Previous Photo Submitters"
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

// Information and tables (if they exist) for this year.
echo $notatall;
if ($previous_header_array[0] != "This report retrieved no results matching the criteria.") {
  echo $existing;
  echo renderhtmlreport(1,$current_rows,$current_header_array,$current_array);
  echo $notyet;
  echo renderhtmlreport(1,$previous_rows,$previous_header_array,$previous_array);
} else {
  echo $existing_clear;
}
correct_footer();
?>