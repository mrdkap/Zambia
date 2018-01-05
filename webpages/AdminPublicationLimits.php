<?php
require_once('StaffCommonCode.php');
global $link, $message, $message_error;

// LOCALIZATIONS
$title="Create/Update Publication Limits";
$description="<P>Create and/or update this event's Publicaton Limits.</P>\n";
$additionalinfo.="<P>Any \"Submit\" button submits the whole page.</P>\n";
$additionalinfo.="<P>This page is limited to a few select people who can change it, ";
$additionalinfo.="basically the Con Chair and the Super Publication folks.</P>\n";
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

// Get the publimname array and the publimtype array
$publimname_array=get_enum_values('PublicationLimits','publimname');
$publimtype_array=get_enum_values('PublicationLimits','publimtype');

// Get the BioDests
$queryBioDests="SELECT biodestname FROM BioDests order by display_order";
// Get the query
list($biodestrows,$biodestheader_array,$biodest_array)=queryreport($queryBioDests,$link,$title,$description,0);
for ($i=1; $i<=$biodestrows; $i++) {
  $publimdest_array[]=$biodest_array[$i]['biodestname'];
}

if (((may_I("Maint")) or (may_I("ConChair")) or (may_I("SuperVendor"))) and ($_POST['submit']=="Submit")) {
  foreach ($publimname_array as $publimname) {
    foreach ($publimdest_array as $publimdest) {
      foreach ($publimtype_array as $publimtype) {

	// was set, limit not set, so delete
	if ((!empty($_POST["waslimit"][$publimtype][$publimdest][$publimname])) and
	    (is_numeric($_POST["waslimit"][$publimtype][$publimdest][$publimname])) and
	    (empty($_POST["limit"][$publimtype][$publimdest][$publimname]))) {
	  $match_string="conid=\"$conid\" and publimtype=\"$publimtype\" and ";
          $match_string.="publimdest=\"$publimdest\" and publimname=\"$publimname\"";
	  $message.=delete_table_element($link,$title,"PublicationLimits",$match_string);

	// waslimit and limit are different so update
	} elseif ((!empty($_POST["waslimit"][$publimtype][$publimdest][$publimname])) and
		  (is_numeric($_POST["waslimit"][$publimtype][$publimdest][$publimname])) and
		  (!empty($_POST["limit"][$publimtype][$publimdest][$publimname])) and
		  (is_numeric($_POST["limit"][$publimtype][$publimdest][$publimname])) and
		  ($_POST["waslimit"][$publimtype][$publimdest][$publimname] !=
		   $_POST["limit"][$publimtype][$publimdest][$publimname])) {
	  $set_array=array('publimval="' . $_POST["limit"][$publimtype][$publimdest][$publimname] . '"');
	  $match_string="conid=\"$conid\" and publimtype=\"$publimtype\" and ";
          $match_string.="publimdest=\"$publimdest\" and publimname=\"$publimname\"";
	  $message.=update_table_element_extended_match($link,$title,"PublicationLimits",$set_array,$match_string);

	// limit set, was not set, so add
	} elseif ((!empty($_POST["limit"][$publimtype][$publimdest][$publimname])) and
	    (is_numeric($_POST["limit"][$publimtype][$publimdest][$publimname])) and
	    (empty($_POST["waslimit"][$publimtype][$publimdest][$publimname]))) {
	  $element_array=array('conid', 'publimtype', 'publimdest', 'publimname', 'publimval');
	  $value_array = array($conid, $publimtype, $publimdest, $publimname,
			       $_POST["limit"][$publimtype][$publimdest][$publimname]);
	  $message.=submit_table_element($link,$title,"PublicationLimits",$element_array,$value_array);
	}
      }
    }
  }
}

// Get the PublicationLimits, after the above to update any changes.
$limit_array=getLimitArray();

$formstring="<FORM name=\"publim\" action=\"AdminPublicationLimits.php\" method=POST>\n";
$formstring.="  <BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Submit\">Submit</BUTTON>\n";
foreach ($publimname_array as $publimname) {
  $formstring.="  <TABLE border=2>\n    <TR><TH>$publimname</TH></TR>\n    <TR>\n";
  foreach ($publimdest_array as $publimdest) {
    $formstring.="      <TD>\n        <TABLE border=1>\n";
    $formstring.="          <TR><TH colspan=2>$publimdest</TH></TR>\n          <TR>\n";
    foreach ($publimtype_array as $publimtype) {
      $formstring.="            <TD><LABEL for=\"limit[$publimtype][$publimdest][$publimname]\">";
      $formstring.="$publimtype = </LABEL>\n";
      $formstring.="              <input name=\"limit[$publimtype][$publimdest][$publimname]\" ";
      $formstring.="id=\"limit[$publimtype][$publimdest][$publimname]\" type=\"text\" size=4 ";
      $formstring.="value=\"" . $limit_array[$publimtype][$publimdest][$publimname] . "\">\n";
      $formstring.="              <input name=\"waslimit[$publimtype][$publimdest][$publimname]\" ";
      $formstring.="id=\"waslimit[$publimtype][$publimdest][$publimname]\" type=\"hidden\"";
      $formstring.="value=\"" . $limit_array[$publimtype][$publimdest][$publimname] . "\"></TD>\n";
    }
    $formstring.="          </TR>\n        </TABLE>\n      </TD>\n    </TR>\n";
  }
  $formstring.="  </TABLE>\n";
  $formstring.="  <BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Submit\">Submit</BUTTON>\n";
}
$formstring.="</FORM>\n";



topofpagereport($title,$description,$additionalinfo,$message,$message_error);

if ((may_I("Maint")) or (may_I("ConChair")) or (may_I("SuperPublications"))) {
  echo $formstring;
} else {
  echo "<P>We're sorry you do not have permission to view this page at this time.</P>\n";
}


correct_footer();
?>


