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

$existing="<P>If your name is listed below, select your name and\n";
$existing.="log in, since you are already\n";
$existing.="set up to submit photos for this event.</P>\n";

$notyet="<P>If your name is not listed above, and is in the below table, please indicate\n";
$notyet.="that you want to submit photos for us by clicking on your name (and your name only).\n";
$notyet.="You should then be able to <A HREF=\"login.php?newconid=$conid\">log in</A>,\n";
$notyet.="and start submitting photos to your heart's content.</P>\n";

$notyet="<P>If you are a returning photographer, please find your name in the list below, select\n";
$notyet.="it and log in. If you need your password reset, please contact us at\n";
$notyet.="<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A>.</P>\n";

$notatall="<P>If your name is not listed at all, please\n";
$notatall.="<A HREF=\"PhotoLoungeProposed.php\">propose</A> yourself as someone who wants to\n";
$notatall.="submit photographs for the Photo Lounge.  Someone will get back to you soon.</P>\n";

$notatall="<P>If you are new to our event, please <A HREF=\"PhotoLoungeProposed.php\">create\n";
$notatall.="an account</A> to submit photos.  Please note you will be asked to review our\n";
$notatall.="Release form that includes the Photo Lounge and our optional DVD for this years\n";
$notatall.="event.</P>\n";

/* Some loop that will take their click-through or form, and set their
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
echo $notatall;
echo $existing;
echo renderhtmlreport(1,$current_rows,$current_header_array,$current_array);
echo $notyet;
echo renderhtmlreport(1,$previous_rows,$previous_header_array,$previous_array);
correct_footer();
?>