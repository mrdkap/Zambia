<?php
require_once('PartCommonCode.php');
global $link, $message, $message_error;
$badgeid=$_SESSION['badgeid'];
$conid=$_SESSION['conid'];

// LOCALIZATIONS
$title="My General Interests";
$description="";
$additionalinfo="";

// ParticipantInterests
$query = <<<EOD
SELECT
    yespanels,
    nopanels,
    yespeople,
    nopeople,
    otherroles
  FROM
      ParticipantInterests
  WHERE
    conid=$conid AND
    badgeid="$badgeid"
EOD;

$result=mysql_query($query,$link);
if (!$result) {
  $message.=$query."<BR>".mysql_error($link)."<BR>Error querying database. Unable to continue.<BR>";
  RenderError($title,$message);
  exit();
}
$rows=mysql_num_rows($result);
if ($rows>1) {
  $message.=$query."<br>Multiple rows returned from database where one expected. Unable to continue.";
  RenderError($title,$message);
  exit();
}
if ($rows==0) {
  $yespanels="";
  $nopanels=""; 
  $yespeople="";
  $nopeople="";
  $otherroles="";
  $newrow=true;
}
else {
  list($yespanels,$nopanels,$yespeople,$nopeople,$otherroles)=mysql_fetch_array($result, MYSQL_NUM);
  $newrow=false;
}

// Possible Roles
$query = <<<EOD
SELECT
    roleid,
    rolename
  FROM
      Roles
  ORDER BY
    display_order
EOD;

// Retrieve query
list($rolerows,$header_array,$role_array)=queryreport($query,$link,$title,$description,0);

// Chosen Roles
$query = <<<EOD
SELECT
    roleid
  FROM 
      ParticipantHasRole
  WHERE
    badgeid="$badgeid" AND
    conid=$conid
EOD;

// Retrieve query
list($parthasrows,$header_array,$parthasrole_array)=queryreport($query,$link,$title,$description,0);

// Reduce the array
for ($i=1; $i<=$parthasrows; $i++) {
  $parthasroleid_array[$parthasrole_array[$i]['roleid']]="yes";
}

if ($_POST['update']=="YES") {

  // Participant Interests
  $newrow=$_POST["newrow"];
  $yespanels=stripslashes($_POST["yespanels"]);
  $nopanels=stripslashes($_POST["nopanels"]);
  $yespeople=stripslashes($_POST["yespeople"]);
  $nopeople=stripslashes($_POST["nopeople"]);
  $otherroles=stripslashes($_POST["otherroles"]);
  if ($newrow) {
    $element_array=array('badgeid','conid','yespanels','nopanels','yespeople','nopeople','otherroles');
    $value_array=array($_SESSION['badgeid'],
		       $_SESSION['conid'],
		       mysql_real_escape_string($yespanels,$link),
		       mysql_real_escape_string($nopanels,$link),
		       mysql_real_escape_string($yespeople,$link),
		       mysql_real_escape_string($nopeople,$link),
		       mysql_real_escape_string($otherroles,$link));
    $message.=submit_table_element($link,$title,"ParticipantInterests",$element_array,$value_array);
  } else {
    $pairedvalue_array=array("yespanels='".mysql_real_escape_string($yespanels,$link)."'",
			      "nopanels='".mysql_real_escape_string($nopanels,$link)."'",
			      "yespeople='".mysql_real_escape_string($yespeople,$link)."'",
			      "nopeople='".mysql_real_escape_string($nopeople,$link)."'",
			      "otherroles='".mysql_real_escape_string($otherroles,$link)."'");
    $match_string="badgeid='".$_SESSION['badgeid']."' AND conid='".$_SESSION['conid']."'";
    $message.=update_table_element_extended_match ($link,$title,"ParticipantInterests",$pairedvalue_array, $match_string);
  }

  // Participant Has Role
  for ($i=1; $i<=$rolerows; $i++) {
    // if willdo and not did then add it else if did and not willdo then remove it
    if ($_POST["willdorole".$role_array[$i]['roleid']]=="on") {
      if ($parthasroleid_array[$role_array[$i]['roleid']]!="yes") {
	$element_array=array('conid','badgeid','roleid');
	$value_array=array($_SESSION['conid'],$_SESSION['badgeid'],$role_array[$i]['roleid']);
	$message.=submit_table_element($link,$title,"ParticipantHasRole",$element_array,$value_array);
	$parthasroleid_array[$role_array[$i]['roleid']]="yes";
      }
    } else {
      if ($parthasroleid_array[$role_array[$i]['roleid']]=="yes") {
	$match_string="badgeid='".$_SESSION['badgeid']."' AND conid='".$_SESSION['conid']."' AND roleid='".$role_array[$i]['roleid']."'";
	$message.=delete_table_element($link,$title,"ParticipantHasRole",$match_string);
	unset($parthasroleid_array[$role_array[$i]['roleid']]);
      }
    }
  }
}

// Begin Display
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Begin the form
echo "<FORM name=\"addform\" method=POST action=\"my_interests.php\">\n";

// Polite warning if permissions don't exist, update if it does.
if (!may_I('my_gen_int_write')) {
  echo "<P>We're sorry, but we are unable to accept your suggestions at this time.</P>\n";
} else {
  echo "<INPUT type=\"hidden\" name=\"update\" value=\"YES\">\n";
}

echo "<DIV>\n";
if (may_I('my_gen_int_write')) {
  echo "<BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submit\" >Save</BUTTON>";
}
echo "</DIV>\n";
// Begin the roles possibility
echo "<INPUT type=\"hidden\" name=\"newrow\" value=\"".($newrow?1:0)."\">\n";
echo "<P>Roles I'm willing to take on:</P>\n";
echo "<DIV class=\"tab\">\n";
for ($i=1; $i<=$rolerows; $i+=2) {
  echo "  <DIV class=\"tab-row\">\n";
  echo "    <DIV class=\"tab-cell\">\n";
  echo "      <INPUT type=checkbox name=\"willdorole".$role_array[$i]['roleid'];
  echo "\" id=\"willdorole".$role_array[$i]['roleid']."\"";
  if (isset($parthasroleid_array[$role_array[$i]['roleid']])) {
    echo " checked";
  }
  if (!may_I('my_gen_int_write')) {
    echo " disabled";
  }
  echo ">\n";
  echo "      <LABEL for=\"willdorole".$role_array[$i]['roleid']."\">".$role_array[$i]["rolename"]."</LABEL>\n";
  echo "    </DIV><!-- end table cell -->\n";
  if (($i+1)>$rolerows) {
    echo "    <DIV class=\"tab-cell\">&nbsp;</DIV><!-- end table cell -->\n";
    echo "  </DIV><!-- end table row -->\n";
  } else {
    echo "    <DIV class=\"tab-cell\">\n";
    echo "      <INPUT type=checkbox name=\"willdorole".$role_array[($i+1)]['roleid'];
    echo "\" id=\"willdorole".$role_array[($i+1)]['roleid']."\"";
    if (isset($parthasroleid_array[$role_array[$i+1]['roleid']])) {
      echo " checked";
    }
    if (!may_I('my_gen_int_write')) {
      echo " disabled";
    }
    echo ">\n";
    echo "      <LABEL for=\"willdorole".$role_array[($i+1)]['roleid']."\">".$role_array[$i+1]["rolename"]."</LABEL>\n";
    echo "    </DIV><!-- end table cell -->\n";
    echo "  </DIV><!-- end table row -->\n";
  }
}
echo "</DIV><!-- end table -->";
echo "<DIV>\n";
echo "  <P>Description for \"Other\":</P>\n";
echo "  <TEXTAREA name=\"otherroles\" rows=5 cols=72";
if (!may_I('my_gen_int_write')) {
  echo " readonly class=\"readonly\"";
}
echo ">".htmlspecialchars($otherroles,ENT_COMPAT)."</TEXTAREA>\n";
echo "</DIV>\n";
echo "<BR><HR>\n";
echo "<DIV>\n";
echo "   <DIV><LABEL for=\"yespanels\"><P>Other workshops, presentations, or activities I'd be interested in offering:</P></LABEL></DIV>\n";
echo "  <DIV><TEXTAREA name=\"yespanels\" rows=5 cols=72";
if (!may_I('my_gen_int_write')) {
  echo " readonly class=\"readonly\"";
}
echo ">".htmlspecialchars($yespanels,ENT_COMPAT)."</TEXTAREA>\n";
echo "  </DIV>\n";
echo "</DIV>\n"; 
echo "<DIV>\n";
echo "  <DIV><LABEL for=\"nopanels\"><P>I may have done so in the past, but I'm no longer interested in the following:</P></LABEL></DIV>\n";
echo "  <DIV><TEXTAREA name=\"nopanels\" rows=5 cols=72";
if (!may_I('my_gen_int_write')) {
  echo " readonly class=\"readonly\"";
}
echo ">".htmlspecialchars($nopanels,ENT_COMPAT)."</TEXTAREA>\n";
echo "  </DIV>\n";
echo "</DIV>\n";
echo "<DIV>\n";
echo "  <DIV><LABEL for=\"yespeople\"><p>People with whom I'd like to be on a panel:</p></LABEL></DIV>\n";
echo "  <DIV><TEXTAREA name=\"yespeople\" rows=5 cols=72";
if (!may_I('my_gen_int_write')) {
  echo " readonly class=\"readonly\"";
}
echo ">".htmlspecialchars($yespeople,ENT_COMPAT)."</TEXTAREA>\n";
echo "  </DIV>\n";
echo "</DIV>\n";
echo "<DIV>\n";
echo "  <DIV><LABEL for=\"nopeople\"><p>People with whom I'd rather not be on a panel:</p></LABEL></DIV>\n";
echo "  <DIV><TEXTAREA name=\"nopeople\" rows=5 cols=72";
if (!may_I('my_gen_int_write')) {
  echo " readonly class=\"readonly\"";
}
echo ">".htmlspecialchars($nopeople,ENT_COMPAT)."</TEXTAREA>\n";
echo "  </DIV>\n";
echo "  <DIV>\n";
if (may_I('my_gen_int_write')) {
  echo "    <BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submit\" >Save</BUTTON>";
}
echo "  </DIV>\n";
echo "</DIV>\n";
echo "</FORM>\n";
correct_footer();
?>
