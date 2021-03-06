<?php 
/* Adjust conid */
$newconid=$_POST['newconid'];
if ($newconid=="") {
  $newconid=$_GET['newconid'];
}
if ((!empty($newconid)) and (is_numeric($newconid))) {
  $_SESSION['conid']=$newconid;
}

require_once('PartCommonCode.php');
global $link, $message, $message_error;

if ((!empty($newconid)) and (is_numeric($newconid))) {
  $_SESSION['conid']=$newconid;
}

// LOCALIZATIONS
$title="Login";
$description="";
$additionalinfo="";

// Default role is Participant, so at least something familiar shows.
if (empty($_SESSION['role'])) {$_SESSION['role']="Participant";}

// Blank return string to start with
$webstring="";

/* For passed in login ids */
$badgeid=$_GET['login'];

$webstring.= <<<EOD
<form name="loginform" method="POST" action="doLogin.php">
  <table class="login" align=center>
    <tr>
      <td>Badge ID/Email:</td>
      <td><input type="text" name="badgeid" maxlength="40"
EOD;

if ($badgeid!="") {$webstring.=" value=\"$badgeid\"";}
$webstring.=" ></td>";

$webstring.= <<<EOD
    </tr>
    <tr>
      <td>Password:</td>
      <td><input type="password" name="passwd" maxlength="50"></td>
    </tr>
    <tr>
      <td colspan="2" align="center"> <input type="submit" name="submit" value="Login"> </td>
    </tr>
  </table>
</form>
EOD;

$verbiage=get_verbiage("login_0");
if ($verbiage != "") {
  ob_start();
  eval ('?>' . $verbiage);
  $webstring.=ob_get_clean();
} else {
  $webstring.="<P id=\"brainstorm-login-hint\"> <b>Brainstorm</b> users: if you want to submit ideas for panels, please enter \"brainstorm\" for your Badge ID and use the last name of the author of the Foundation series as your password (in all lowercase). </P>\n";
}

if ($included!="YES") {
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo $webstring;
  correct_footer();
}
?>
