<?php
require_once('PostingCommonCode.php');
global $link;
if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}

// Test for conid being passed in
if ($conid == "") {
  $conid=$_SESSION['conid'];
}

// Localisms
$_SESSION['return_to_page']='PhotoLoungeProposed.php';
$title="Propose to Submit Photos";
$verbiage=get_verbiage("PhotoLoungeProposed_0");
if ($verbiage != "") {
  ob_start();
  eval ('?>' . $verbiage);
  $description=ob_get_clean();
} else {

  $description="<P><B>Please\n";
  $description.="<A HREF=\"PhotoLoungeReturning.php\">check here first</A></B>\n";
  $description.="to see if you can activate yourself simply by clicking on your name.</P>\n";
  $description.="<P>Otherwise, please provide your name, email and bio below, and click\n";
  $description.="\"save\". One of our staff members will get back to you in 2-3 days with\n";
  $description.="your log-in ID and a temporary password at the email address\n";
  $description.="provided, once your account has been set up.</P>\n";
  $description.="<P>If you are\n";
  $description.="<A HREF=\"PhotoLoungeReturning.php\">already in our system</A>\n";
  $description.="we already have your information, and it is you can just click and\n";
  $description.="be able to start uploading photos immediately.</P>\n";
}

$additionalinfo="<P>Note: items in red must be completed before you can save.</P>\n";
$additionalinfo.="<P>Please make sure your name and email address are valid as well\n";
$additionalinfo.="as whatever you want for your bio.</P>\n";

// Check to see if the phase is open
$phasequery= <<<EOD
SELECT
    phasestate
  FROM
      Phase
    JOIN PhaseTypes USING (phasetypeid)
  WHERE
    conid=$conid AND
    phasetypename in ('Photo Submission')
EOD;

if (!$result=mysql_query($phasequery,$link)) {
  $message_error=$phasequery."<BR>Error querying database. Unable to continue.<BR>";
  RenderError($title,$message_error);
  exit();
}

list($is_Photo_Submission)=mysql_fetch_array($result, MYSQL_NUM);

if ($is_Photo_Submission != "0") {
  $message_error="<P>We're sorry, Photo Submmisions are not open at this time.</P>\n";
  RenderError($title,$message_error);
  exit();
}

// If the information has already been added, and we are
// on the return loop, add the Participant to the database.
if ((isset ($_POST['update'])) and ($_POST['update']=="Yes")) {
  $recordfile = fopen("../Local/$conid/Photo_Lounge_Proposed.txt","a") or RenderError($title,"Unable to open record file.");
  $recordstring = "Name: " . htmlspecialchars_decode($_POST["pubsname"]) . "\n";
  $recordstring.= "Email: " . htmlspecialchars_decode($_POST["email"]) . "\n";
  $recordstring.= "Bio: " . htmlspecialchars_decode($_POST["raw_bio"]) . "\n";
  fwrite ($recordfile, $recordstring);
  fclose($recordfile);
  $message.="Accout for " . htmlspecialchars_decode($_POST["pubsname"]) . " has been submitted to our process.\n";
  $message.="Look for an email in the next few days with your password.\n";
 }

// Begin the display
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
?>

<script language="javascript" type="text/javascript">
var phase1required=new Array("pubsname", "email", "raw_bio");
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
  <FORM name="photosubform" class="bb"  method=POST action="PhotoLoungeProposed.php">
    <INPUT type="submit" ID="sButtonTop" value="Save">&nbsp;
    <INPUT type="hidden" name="update" value="Yes">
    <TABLE>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="pubsname" ID="pubsname">Your Name:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="pubsname" onKeyPress="return checkSubmitButton();"></TD></TR>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="email" ID="email">Your Email Address:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="email" size="50" onKeyPress="return checkSubmitButton();"></TD></TR>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="raw_bio" ID="raw_bio">Your Bio:</LABEL><BR>
          <TEXTAREA cols="70" rows="5" name="raw_bio" onKeyPress="return checkSubmitButton();"></TEXTAREA></TD></TR>
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
