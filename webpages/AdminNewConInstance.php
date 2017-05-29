<?php
require_once ('StaffCommonCode.php');
global $link;
$title="New Con Instance";
$description="<P>Create and set up a new con instance based on this one.</P>\n";
$additionalinfo="<P>Some values will have suggested defaults, feel free to change them as appropriate.</P>";
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
if ($_POST['create'] == "Yes") {

  // Just in case, empty the newconid variable, element array and value array for the ConInfo
  $newconid="";
  $element_array=array();
  $value_array=array();

  /*
  Populate the ConInfo information, set up some variables, and exit
  if there aren't defined values.
  */

  // For each of the fields in the ConInfo table
  for ($i=1; $i<=$coninfodescrows; $i++) {

    // Short-cut so I don't have to keep typing this out.
    $field=$coninfodesc_array[$i]['Field'];

    // Set the next element in the element array
    $element_array[] = $field;

    // Bounce out, if there is an empty value
    if (empty($_POST[$field])) {
      $message_error.="All fields need values, and $field doesn't have one.\n";
      $message_error.="Please, use your back button, and try again.";
      RenderError($title,$message_error);
      exit();
    }

    // Set the newconid to the appropriate value, for substitution below.
    if ($field == "conid") {
      $newconid=$_POST[$field];
    }

    // Set the next value in the value array
    $value_array[] = mysql_real_escape_string(stripslashes($_POST[$field]));
  }
  $message.=submit_table_element($link, $title, "ConInfo", $element_array, $value_array);

  /* 
  Populate one element of the Phase table, so it can be opened, and
  when AdminPhases.php is invoked, all the other phases are populated.
  */
  $element_array = array('conid');
  $value_array = array($newconid);
  $message.=submit_table_element($link, $title, "Phase", $element_array, $value_array);

  // This should be for each of the auto-populated tables
  $autopopulate = array('UserHasPermissionRole','UserHasConRole','HasReports','PublicationLimits');
  foreach ($autopopulate as $table) {

    // Gets the values for the current conid
    $query="SELECT * FROM $table WHERE conid=$conid";
    list($rows,$header_array,$table_array)=queryreport($query,$link,$title,$description,0);

    // Walk each row of the table
    for ($i=1 ; $i<=$rows; $i++) {

      // Empties the element array and value array for each instance
      $element_array=array();
      $value_array=array();

      // Populate each new row, by column for the new con instance
      foreach ($header_array as $column) {
	$element_array[] = $column;
	if ($column == "conid") {
	  $value_array[] = $newconid;
	} else {
	  $value_array[] = $table_array[$i][$column];
	}
      }
      $message.=submit_table_element($link, $title, $table, $element_array, $value_array);
    }
  }

  // Posting complete, message this only, and close
  $message="Thank you for creating a new con instance.\n";
  $message.="Please use the logout button above, and then log into your new con to begin.\n";
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  correct_footer();
  exit();
}

// Query for the next available conid, as newconidsuggest.
$queryconid="SELECT conid from ConInfo ORDER BY conid";
list($conidrows,$conidheader_array,$conid_array)=queryreport($queryconid,$link,$title,$description,0);
$newconidsuggest="";
for ($i=1; $i<=$conidrows; $i++) {
  if (($conid_array[$i]['conid'] >= $conid) and ($newconidsuggest == "")) {
    if ($i == $conidrows) {
      $newconidsuggest=$conid_array[$i]['conid'] + 1;
    } elseif ($conid_array[$i + 1]['conid'] != $conid_array[$i]['conid'] + 1) {
      $newconidsuggest=$conid_array[$i]['conid'] + 1;
    }
  }
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

// Specialize values for the field
$defaultvalue['conid']=$newconidsuggest;
$defaultvalue['conname']="";
$defaultvalue['connamelong']="";
$defaultvalue['constartdate']="0000-00-00 00:00:00";

for ($i=1; $i<=$coninfodescrows; $i++) {

  // Short-cut so I don't have to keep typing this out.
  $field=$coninfodesc_array[$i]['Field'];

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
  if (isset($defaultvalue[$field])) {
    $formstring.=$defaultvalue[$field];
  } else {
    $formstring.=$_SESSION[$field];
  }
  $formstring.="\"></DT>\n";
  $formstring.="            <DD>";
  $formstring.=get_verbiage("ConInstance_" . $field);
  $formstring.="</DD><br />\n\n";
}

// Produce the form and get the appropriate information.
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
?>
    <DIV class="formbox">
      <FORM name="newconform" class="bb"  method=POST action="AdminNewConInstance.php">
        <INPUT type="hidden" name="create" value="Yes">
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
