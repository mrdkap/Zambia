<?php
require_once('StaffCommonCode.php');
global $link;
$title="Photo Lounge Selected Pictures";
$description="<P>Please select/view which pictures are to be included this year.</P>\n";
$additionalinfo="<P>For further picture checks, take a look at either the\n";
$additionalinfo.="<A HREF=\"PhotoLoungeContactSheet.php\">Photo Lounge Contact Sheet</A>\n";
$additionalinfo.="or the\n";
$additionalinfo.="<A HREF=\"genreport.php?reportname=picturesubmiswvote\">Picture Submissions With Votes</A>\n";
$additionalinfo.="report.  Or\n";
$additionalinfo.="<A HREF=\"PhotoLoungeVote.php\">add votes</A> to pictures needing votes.</P>\n";

// Check to see if page can be displayed
if (!may_I("SuperPhotoRev")) {
  $message_error ="Alas, you do not have the proper permissions to view this page.\n";
  $message_error.="If you think this is in error, please, get in touch with an administrator.";
  RenderError($title,$message_error);
  exit();
}

// Set conid
$conid=$_SESSION['conid'];

// Complicated form for inclusion state toggle
$toggleform ="'<FORM name=\"toggleinclusion\" method=\"POST\" action=\"PhotoLoungeCollectVotes.php\">\n";
$toggleform.="  <INPUT type=\"hidden\" name=\"dotoggle\" value=\"please\">\n";
$toggleform.="  <INPUT type=\"hidden\" name=\"newincludestate\" value=\"',if(includestatus=\"p\",\"a\",\"p\"),'\">\n";
$toggleform.="  <INPUT type=\"hidden\" name=\"togglephotoid\" value=\"',photoid,'\">\n";
$toggleform.="  <INPUT style=\"background-color:',if(includestatus=\"p\",\"gray\",\"green\"),';color:',if(includestatus=\"p\",\"black\",\"white\"),'\" type=\"submit\" name=\"submit\" value=\"',if(includestatus=\"p\",\"Possible\",\"Accepted\"),'\">\n";
$toggleform.="</FORM>\n'";

// Test and toggle include state in database
if (($_POST["dotoggle"]=="please") and
    (!empty($_POST["togglephotoid"])) and
    (is_numeric($_POST["togglephotoid"])) and
    (($_POST["newincludestate"] == "p") or ($_POST["newincludestate"] == "a"))) {
  $set_array=array("includestatus='".$_POST["newincludestate"]."'");
  $message.=update_table_element($link, $title, "PhotoLoungePix", $set_array, "photoid", $_POST['togglephotoid']);
}

/* This query pulls:
   o The a link to the Photo and it's thumbnail for display
   o The info included with the photo, and a link to change it
   o The pubsname of the person who uploaded the photos
   o The sum (out of 6, inverting the votes so 1st choice is
     worth 5 points, second 4 etc.) of all the votes on pictures
   o The complex toggle thing from above.
 */
$query=<<<EOD
SELECT
    concat("<A HREF=../Local/$conid/Photo_Lounge_Submissions/",pictureid,"><img height=150 src=../Local/$conid/Photo_Lounge_Submissions/.thmb/",pictureid,"></A>") AS "Picture",
    concat("<A HREF=StaffEditPhotoLoungeInfo.php?photoid=",photoid,">Title: ", phototitle,"<br>Artist: ",photoartist,"<br>Model: ",photomodel,"<br>Location: ",photoloc,"<br>General Consent: ",genconsent,"<br>DVD Consent: ",dvdconsent,"</A>") AS "Info",
    pubsname,
    SUM(6-picturevote) AS "Vote Sum",
    concat($toggleform) as "Change State"
  FROM
      VotesOnPicture VOP
    JOIN PhotoLoungePix PLP ON (pictureid=photofile)
    JOIN Participants P ON (PLP.badgeid=P.badgeid)
  WHERE
    VOP.conid=$conid AND
    PLP.conid=$conid
  GROUP BY
   pictureid
EOD;

// Retrieve query
list($rows,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

// Begin page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Show the current state of voting and inclusion
echo renderhtmlreport(1,$rows,$header_array,$element_array);

// End page
correct_footer();
