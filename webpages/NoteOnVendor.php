<?php
require_once('StaffCommonCode.php');
global $link, $message, $message_error;

// Set the vendorid
$vendorid="";

// See if it is set by the pulldowns
if (may_I('SuperVendor')) {
  // Collaps the two choices into one
  if ($_POST["partidp"]!=0) {$_POST["partid"]=$_POST["partidp"];}
  if ($_POST["partide"]!=0) {$_POST["partid"]=$_POST["partide"];}

  if (isset($_POST["partid"])) {
    $vendorid=$_POST["partid"];
  } elseif (isset($_GET["partid"])) {
    $vendorid=$_GET["partid"];
  }
}

// Supercede the pulldowns if it is explicitly passed.
if ((!empty($_GET['vendorid'])) and (is_numeric($_GET['vendorid']))) {
  $vendorid=$_GET['vendorid'];
} elseif ((!empty($_POST['vendorid'])) and (is_numeric($_POST['vendorid']))) {
  $vendorid=$_POST['vendorid'];
}

if (!empty($vendorid)) {
  $congoinfo=getCongoData($vendorid);
  $vendorname=$congoinfo['badgename'];
  if (empty($congoinfo['badgeid'])) {
    $message_error.=" since there is no vendor associated with that badgeid.";
    RenderError($title,$message_error);
    exit();
  }

  // Submit the note, if there was one, when this was called
  if (!empty($_POST["note"])) {
    $element_array = array('badgeid', 'rbadgeid', 'note', 'conid');
    $value_array=array($vendorid,
		       $_SESSION['badgeid'],
		       $_POST["note"],
		       $_SESSION['conid']);
    $message.=submit_table_element($link, $title, "NotesOnVendors", $element_array, $value_array);
  }

  // If there is a vendorid, then get all the notes, including the newest
  $query = <<<EOD
SELECT
    timestamp as 'When',
    pubsname as 'Who',
    note as 'What Was Done',
    conid as "Con"
  FROM
      NotesOnVendors N
    JOIN Participants P ON N.rbadgeid=P.badgeid
  WHERE
    N.badgeid=$vendorid
  ORDER BY
    timestamp DESC
EOD;

  list($rows,$header_array,$notes_array)=queryreport($query,$link,$title,$descripton,0);
} else {
  $vendorname="No Vendor Selected";
}

// LOCALISMS
$title="Notes On Vendor";
$description="<P>Add a persistent note about a particular vendor below.</P>\n";
$additionalinfo="";

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

select_participant($vendorid, 'VENDORCURRENT', "NoteOnVendor.php");

// Stop page here if and individual has not yet been selected
if (empty($vendorid)) {
  correct_footer();
  exit();
}

// Add note through form below
?>

<HR>
<BR>
<FORM name="vendornoteform" method=POST action="NoteOnVendor.php">
<INPUT type="hidden" name="vendorid" value="<?php echo $vendorid; ?>">
<DIV class="titledtextarea">
  <LABEL for="note">Note:</LABEL>
  <TEXTAREA name="note" rows=6 cols=72></TEXTAREA>
</DIV>
<BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>

<?php
// Show previous notes added, for references, and end page
echo renderhtmlreport(1,$rows,$header_array,$notes_array);
correct_footer();
?>
