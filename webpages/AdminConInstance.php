<?php
require_once ('StaffCommonCode.php');
global $link;
$title="Update Current Con Instance";
$description="<P>Update the information for this con instance.</P>\n";
$additionalinfo="<P>Please be careful, these values change very basic elements.</P>";
$conid=$_SESSION['conid'];

// Only Maint and Con Chair can go forward
if ((may_I("Maint")) or (may_I("ConChair"))) {
  $additionalinfo.="";
} else {
  $message_error="You do not currently have permission to create or set up a new con instance.<BR>\n";
  RenderError($title,$message_error);
  exit();
}

// Set up the coninfo fields
$queryconinfodesc="desc ConInfo";
list($coninfodescrows,$coninfodescheader_array,$coninfodesc_array)=queryreport($queryconinfodesc,$link,$title,$description,0);


// Form returned
if ($_POST['update'] == "Yes") {

  // Just in case, empty the newconid variable, element array and value array for the ConInfo
  $newconid="";
  $element_array=array();
  $value_array=array();

  /*
  Update the ConInfo information, set up some variables, and exit
  if there aren't defined values.
  */

  // For each of the fields in the ConInfo table
  for ($i=1; $i<=$coninfodescrows; $i++) {

    // Short-cut so I don't have to keep typing this out.
    $field=$coninfodesc_array[$i]['Field'];

    // Skip the conid field, since we are changing this one.
    if ($field == "conid") { continue; }

    // Bounce out, if there is an empty value
    if (empty($_POST[$field])) {
      $message_error.="All fields need values, and $field doesn't have one.\n";
      $message_error.="Please, use your back button, and try again.";
      RenderError($title,$message_error);
      exit();
    }

    // Set the next value in the value array
    $pairedvalue_array[] = "$field='" . mysql_real_escape_string(stripslashes($_POST[$field])) . "'";
  }
  $message.=update_table_element($link, $title, "ConInfo", $pairedvalue_array, "conid", $_SESSION['conid']);

}

// Size array for the various boxes, because it looks better that way 5 minimum
// Apparently the minimum size is 5, so don't bother for anything smaller
$defaultsize=5;
$longsize=55;
$size['conname']=12;
$size['connamelong']=$longsize;
$size['constartdate']=19;
$size['conurl']=$longsize;
$size['conlogo']=$longsize;

// Nice labels for the fields
$label['connamelong']="Name (long)";
$label['connumdays']="Number of Days";
$label['constartdate']="Start Date";
$label['condefaultduration']="Default Duration";
$label['condurationminutes']="Duration in Just Minutes";
$label['congridspacer']="Grid Spacer";
$label['conallowkids']="Allow Kids to Attend";
$label['contotalsess']="Total Number Of Sessions";
$label['condailysess']="Number of Sessions Allowed for a Day";
$label['conavailabilityrows']="Number of Rows for Indicating Availability";

for ($i=1; $i<=$coninfodescrows; $i++) {

  // Short-cut so I don't have to keep typing this out.
  $field=$coninfodesc_array[$i]['Field'];

  // Skip the conid field, since we are changing this one.
  if ($field == "conid") { continue; }

  $formstring.="            <DT><LABEL for=\"$field\">";
  if (!empty($label[$field])) {
    $formstring.=$label[$field];
  } else {
    $formstring.=ucfirst(str_replace("con", "", $field));
  }
  $formstring.=": </LABEL>\n";
  $formstring.="            <INPUT type=\"text\" size=";
  if (!empty($size[$field])) {
    $formstring.=$size[$field];
  } else {
    $formstring.="5";
  }
  $formstring.=" name=\"$field\" id=\"$field\"\n";
  $formstring.="            value=\"";
  $formstring.=stripslashes(htmlspecialchars($_SESSION[$field]));
  $formstring.="\"></DT>\n";
  $formstring.="            <DD>";
  $formstring.=get_verbiage("ConInstance_" . $field);
  $formstring.="</DD><br />\n\n";
}

// Produce the form and get the appropriate information.
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
?>
    <DIV class="formbox">
      <FORM name="conform" class="bb"  method=POST action="AdminConInstance.php">
        <INPUT type="hidden" name="update" value="Yes">
        <CENTER><BUTTON class="ib" type=submit value="save">Save</BUTTON></CENTER>
        <DIV class="denseform">
          <DL>
<?php
  echo $formstring;
?>
          </DL>
        </DIV>
        <CENTER><BUTTON class="ib" type=submit value="save">Save</BUTTON></CENTER>
      </FORM>
    </DIV>

<?php
// Close the page
correct_footer();
?>
