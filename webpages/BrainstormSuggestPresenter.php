<?php
require_once('BrainstormCommonCode.php');

// Localisms
$_SESSION['return_to_page']='BrainstormSuggestPresenter.php';
$conid=$_SESSION['conid'];

$title="Suggest a Presenter";
$description="<P><B>Please\n";
$description.="<A HREF=\"BrainstormReturningPresenter.php\">check here first</A></B>\n";
$description.="to see if you can propose simply by clicking on an existing name.</P>\n";
$additionalinfo="<P>Note: items in red must be completed before you can save.</P>\n";
$additionalinfo.="<P>Please make sure your name and email address are valid as well\n";
$additionalinfo.="as that of the presenter. If they are not, the chance that we might\n";
$additionalinfo.="invite them decreases exponentially.</P>\n";
$additionalinfo.="<P>If they are\n";
$additionalinfo.="<A HREF=\"BrainstormReturningPresenter.php\">already in our system</A>\n";
$additionalinfo.="we will already have all their contact information, and it is likely\n";
$additionalinfo.="they will be invited back.</P>\n";


// Get the permroleid and name for assigning as Participant
$query= <<<EOD
SELECT
    permroleid,
    permrolename
  FROM
      PermissionRoles
EOD;
if (($result=mysqli_query($link,$query))===false) {
  $message_error="Error retrieving data from database<BR>\n";
  $message_error.=$query;
  RenderError($title,$message_error);
  exit();
}
if (0==($rows=mysqli_num_rows($result))) {
  $message_error="Database query did not return any rows.<BR>\n";
  $message_error.=$query;
  RenderError($title,$message_error);
  exit();
}
for ($i=1; $i<=$rows; $i++) {
  $permrole_arr[$i]=mysqli_fetch_array($result,MYSQLI_ASSOC);
  if ($permrole_arr[$i]['permrolename'] == "Participant") {
    $permstring="permroleid".$permrole_arr[$i]['permroleid'];
    $participant_arr[$permstring]="checked";
  }
}

// Get the interested value that means "Suggested" from the InterestedTypes table.
$query= <<<EOD
SELECT
    interestedtypeid
  FROM
      InterestedTypes
  WHERE
    interestedtypename in ('Suggested')
EOD;
if (!$result=mysqli_query($link,$query)) {
    $message_error=$query."<BR>Error querying database. Unable to continue.<BR>";
    RenderError($title,$message_error);
    exit();
    }

list($interested)=mysqli_fetch_array($result, MYSQLI_NUM);

// If the information has already been added, and we are
// on the return loop, add the Participant to the database.
if ((isset ($_POST['update'])) and ($_POST['update']=="Yes")) {
  $tmp_note="Suggested by: ".$_POST['yourname']." email: ".$_POST['youremail']." Reason: ".$_POST['note'];
  $_POST['note']=$tmp_note;
  $_POST['firstname']=$_POST['pubsname'];
  $_POST['badgename']=$_POST['pubsname'];
  list($message,$message_error)=create_participant ($_POST,$permrole_arr);
 }

// Set the values.
$participant_arr['password']=md5("unassigned");
$participant_arr['bestway']="Email";
$participant_arr['interested']=$interested;
$participant_arr['prognotes']="Suggested via Brainstorm"; 
$participant_arr['regtype']="SuggestedPresenter";

global $youremail, $yourname;

get_name_and_email($yourname, $youremail);

// Begin the display
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
?>

<script language="javascript" type="text/javascript">
var phase1required=new Array("yourname", "youremail", "pubsname", "email", "uri_en-us_raw_bio", "note");
var currentPhase, unhappyColour, happyColor;

function colourCodeElements(phaseName, unhappyC, happyC) {
  var i, o;

  currentPhase = phaseName;
  unhappyColor = unhappyC;
  happyColor = happyC;
  eval('var requiredElements = ' + phaseName + 'required');
  if (requiredElements == null) return;
  for (i = 0; i < requiredElements.length; i++) {
    o = document.getElementById(requiredElements[i]);
    if (o != null) {
      o.style.color = "red";
    }
  }
}

function checkSubmitButton() {
  var i, j, o, relatedO, controls;
  var enable = true;
  
  eval('var requiredElements = ' + currentPhase + 'required');
  if (requiredElements == null) return;
  for (i = 0; i < requiredElements.length; i++) {
    controls = document.getElementsByName(requiredElements[i]);
    if (controls != null) {
      for (j = 0; j < controls.length; j++) {

	o = controls[j];
	relatedO = document.getElementById(requiredElements[i]);
	switch (o.tagName) {
	case "LABEL":
	  break;
	case "SELECT":
	  if (o.options[o.selectedIndex].value == 0) {
	    enable = false;
	    relatedO.style.color = unhappyColor;
	  } else {
	    relatedO.style.color = happyColor;
	  }
	  break;
	case "TEXTAREA":
	  if (o.value == "") {
	    enable = false;
	    relatedO.style.color = unhappyColor;
	  } else {
	    relatedO.style.color = happyColor;
	  }
	  break;
	case "INPUT":
	  if (o.value == "") {
	    enable = false;
	    relatedO.style.color = unhappyColor;
	  } else {
	    relatedO.style.color = happyColor;
	  }
	  break;
	}
      }
    }
  }
  var saveButton = document.getElementById("sButtonTop");
  if (saveButton != null) {
    saveButton.disabled = !enable;
  }	
  var saveButton = document.getElementById("sButtonBottom");
  if (saveButton != null) {
    saveButton.disabled = !enable;
  }	
}
</script>

<DIV class="formbox">
  <FORM name="presenterform" class="bb"  method=POST action="BrainstormSuggestPresenter.php">
    <INPUT type="submit" ID="sButtonTop" value="Save">&nbsp;
    <INPUT type="hidden" name="update" value="Yes">
    <?php foreach ($participant_arr as $key => $value) { echo "<INPUT type=\"hidden\" name=\"$key\" value=\"$value\">\n"; } ?>
    <TABLE>
      <TR>
        <TD class="form1">
          <LABEL for="yourname" ID="yourname">Your name:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="yourname" onKeyPress="return checkSubmitButton();"
          <?php if ($yourname!="") echo "value=\"$yourname\" "; ?> ></TD></TR>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="youremail" ID="youremail">Your email address:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="youremail" size="50" onKeyPress="return checkSubmitButton();"
          <?php if ($youremail!="") echo "value=\"$youremail\" "; ?> ></TD></TR> 
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="pubsname" ID="pubsname">Suggested Presenter name:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="pubsname" onKeyPress="return checkSubmitButton();"></TD></TR>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="email" ID="email">Suggested Presenter email address:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="email" size="50" onKeyPress="return checkSubmitButton();"></TD></TR>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="uri_en-us_raw_bio" ID="uri_en-us_raw_bio">Suggested Presenter website:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="uri_en-us_raw_bio" size="50" onKeyPress="return checkSubmitButton();"></TD></TR>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="note" ID="note">Why you are suggesting they present for us:</LABEL><BR>
          <TEXTAREA cols="70" rows="5" name="note" onKeyPress="return checkSubmitButton();"></TEXTAREA></TD></TR>
    </TABLE>
    <BR>
    <INPUT type="submit" ID="sButtonBottom" value="Save">&nbsp;
  </FORM>
</DIV>
<script language="javascript" type="text/javascript">
  colourCodeElements("phase1", "red", "green");
  checkSubmitButton();
</script>
<?php correct_footer(); ?>
