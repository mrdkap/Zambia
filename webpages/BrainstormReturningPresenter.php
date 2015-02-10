<?php
require_once('BrainstormCommonCode.php');
$conid=$_SESSION['conid'];

// LOCALIZATIONS
$_SESSION['return_to_page']='BrainstormWelcome.php';
$title="Propose to Present at ".$_SESSION['conname'];
$description="<P>Present for us, please.</P>\n";
$additionalinfo="<P>Once you have indicated that you want to present for\n";
$additionalinfo.="us by clicking on your name, you should be able to\n";
$additionalinfo.="<A HREF=\"login.php?newconid=$conid\">log in</A>\n";
$additionalinfo.="as normal, and decide which classes/panels/social";
$additionalinfo.="gatherings you wish to propose.</P>\n";
$additionalinfo.="<P>If your name is not listed here, you probably have\n";
$additionalinfo.="already been proposed to present at this event. Simply\n";
$additionalinfo.="<A HREF=\"login.php?newconid=$conid\">Log In</A>.</P>\n";

/* Some loop that will take their click-through or form, and set their
   status to "suggested" and migrate them as a presenter to the
   current con-instance. Set interestedtypeid to 4 for "Suggested".
   This should probably become dynamic at some point. */
if ((!empty($_POST['who'])) and (is_numeric($_POST['who']))) {
  $proposed=$_POST['who'];

  // Update the additionalinfo to give them their login number.
  $additionalinfo="<P>Once you have indicated that you want to present for\n";
  $additionalinfo.="us by clicking on your name, you should be able to\n";
  $additionalinfo.="<A HREF=\"login.php?login=$proposed&newconid=$conid\">log in</A>\n";
  $additionalinfo.="as normal, and decide which classes/panels/social";
  $additionalinfo.="gatherings you wish to propose.</P>\n";
  $additionalinfo.="<P>If your name is not listed here, you probably have\n";
  $additionalinfo.="already been proposed to present at this event. Simply\n";
  $additionalinfo.="<A HREF=\"login.php?login=$proposed&newconid=$conid\">Log In</A>.</P>\n";

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

  /* Add to UserHasPermissionRole table. Set permroleid to 3 for
     "Participant".  This should become dynamic at some point. */
  $element_array=array('badgeid','permroleid','conid');
  $value_array=array($proposed, 3, $conid);
  $message.=submit_table_element($link,$title,"UserHasPermissionRole", $element_array, $value_array);
  
  $message.="<P>Your login number is: $proposed and your password has not changed.\n";
  $message.="If you need help resetting your password, please email\n";
  $message.="<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A>\n";
  $message.="for assistance.</P>\n";
}

/* Get the list of all the previous presenters in our system except
   for those listed as "Not Accepted", and those already on our roles
   as presenting. */
$query=<<<EOD
SELECT
    DISTINCT concat("<FORM name=\"propose\" method=POST action=\"BrainstormReturningPresenter.php\">\n",
      "<INPUT type=\"hidden\" name=\"who\" value=\"",badgeid,"\">\n",
      "<INPUT type=\"submit\" value=\"",badgename,"\">\n",
      "</FORM>\n") as "Previous Presenters"
  FROM
      CongoDump
    JOIN Participants USING (badgeid)
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    permrolename in ('Participant') AND
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
	  permrolename in ('Participant')) AND
    badgeid not in (SELECT
          DISTINCT badgeid
        FROM
            UserHasPermissionRole
          JOIN PermissionRoles USING (permroleid)
        WHERE
          conid=$conid AND
          permrolename in ('Participant'))
  ORDER BY
    badgename
EOD;

// Retrieve query
list($rows,$header_array,$vendor_array)=queryreport($query,$link,$title,$description,0);

// Display the page
topofpagereport($title,$description,$additionalinfo);
echo $message;
echo renderhtmlreport(1,$rows,$header_array,$vendor_array);
correct_footer();
?>