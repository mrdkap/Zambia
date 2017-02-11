<?php
require_once('StaffCommonCode.php');
global $link;
$title="Photo Lounge Info Update";
$description="<P>Please update what information is necessary to update.</P>\n";
$additionalinfo="<P>For further picture checks, take a look at either the\n";
$additionalinfo.="<A HREF=\"PhotoLoungeContactSheet.php\">Photo Lounge Contact Sheet</A>\n";
$additionalinfo.="or the\n";
$additionalinfo.="<A HREF=\"genreport.php?reportname=picturesubmiswvote\">Picture Submissions With Votes</A>\n";
$additionalinfo.="report.</P>\n";

// Check to see if page can be displayed
if (!may_I("SuperPhotoRev")) {
  $message_error ="Alas, you do not have the proper permissions to view this page.\n";
  $message_error.="If you think this is in error, please, get in touch with an administrator.";
  RenderError($title,$message_error);
  exit();
}

// Set conid
$conid=$_SESSION['conid'];

// Get the photoid as it is passed in.
$photoid="";
if (!empty($_GET['photoid']) AND is_numeric($_GET['photoid'])) {
  $photoid=$_GET['photoid'];
} elseif (!empty($_POST['photoid']) AND is_numeric($_POST['photoid'])) {
  $photoid=$_POST['photoid'];
}

// Error out if photoid is not passed in.
if ($photoid=="") {
  $message_error ="Unfortunately, you need a particular photo to be updating to use this page.\n";
  $message_error.="<A HREF=\"".$_SESSION['return_to_page']."\">Return</A> to wherever you were.\n";
  RenderError($title,$message_error);
  exit();
}

// Update the database information
if ($_POST['update']=="please") {
  $genconsent_p="No";
  if ($_POST['genconsent']=="Yes") {$genconsent_p="Yes";}
  $dvdconsent_p="No";
  if ($_POST['dvdconsent']=="Yes") {$dvdconsent_p="Yes";}
  $pairedvalue_array=array("phototitle='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($_POST["phototitle"])))."'",
			   "photoartist='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($_POST["photoartist"])))."'",
			   "photomodel='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($_POST["photomodel"])))."'",
			   "photoloc='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($_POST["photoloc"])))."'",
			   "genconsent='".$genconsent_p."'",
			   "dvdconsent='".$dvdconsent_p."'");
  $message.=update_table_element($link, $title, "PhotoLoungePix", $pairedvalue_array, "photoid", $photoid);
}

// Pull all the bits of information for this picture
$query=<<<EOD
SELECT
    photoid,
    photofile,
    phototitle,
    photoartist,
    photomodel,
    photoloc,
    genconsent,
    dvdconsent
  FROM
      PhotoLoungePix
  WHERE
    conid=$conid AND
    photoid=$photoid
EOD;

// Retrieve query
list($rows,$elementheader_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* From here, it is just the page information.
   The begin page starts it, followed by the thumbnail picture.
   The hidden inputs are the photo id, so you aren't dropped
   off the deep end, and the value that says, yes, the update
   button was selected.  The rest of the form is just the values. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo "<P><img src=\"../Local/$conid/Photo_Lounge_Submissions/.thmb/".$element_array[1]['photofile']."\"></P>\n";
echo "<FORM name=\"updatephotoloungeinfo\" method=POST action=\"StaffEditPhotoLoungeInfo.php\">\n";
echo "<INPUT type=submit name=submit class=SubmitButton value=Update>\n";
echo "<INPUT type=hidden name=photoid value=".$element_array[1]['photoid'].">\n";
echo "<INPUT type=hidden name=update value=please>\n";
echo "<DL>\n";
echo "  <DD>Title: <INPUT type=text name=\"phototitle\" size=50 value=\"".$element_array[1]['phototitle']."\"></DD>\n";
echo "  <DD>Artist: <INPUT type=text name=\"photoartist\" size=50 value=\"".$element_array[1]['photoartist']."\"></DD>\n";
echo "  <DD>Model: <INPUT type=text name=\"photomodel\" size=50 value=\"".$element_array[1]['photomodel']."\"></DD>\n";
echo "  <DD>Location: <INPUT type=text name=\"photoloc\" size=50 value=\"".$element_array[1]['photoloc']."\"></DD>\n";
echo "  <DD>General Consent: <INPUT type=text name=\"genconsent\" size=50 value=\"".$element_array[1]['genconsent']."\"></DD>\n";
echo "  <DD>DVD Consent: <INPUT type=text name=\"dvdconsent\" size=50 value=\"".$element_array[1]['dvdconsent']."\"></DD>\n";
echo "</DL>\n";
echo "<INPUT type=submit name=submit class=SubmitButton value=Update>\n";
echo "</FORM>";

correct_footer();
?>
