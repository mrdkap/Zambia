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
global $link, $message;

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

$message_error=$message;
if (isset($message)) {
  $webstring.= "<P class=\"errmsg\">".$message_error."</P>\n";
}

$webstring.= <<<EOD
<form name="loginform" method="POST" action="doLogin.php">
  <table class="login" align=center>
    <tr>
      <td>Badge ID:</td>
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

if (file_exists("../Local/Verbiage/login_0")) {
  $webstring.=file_get_contents("../Local/Verbiage/login_0");
 } else {
  $webstring.="<P id=\"brainstorm-login-hint\"> <b>Brainstorm</b> users: if you want to submit ideas for panels, please enter \"brainstorm\" for your Badge ID and use the last name of the author of the Foundation series as your password (in all lowercase). </P>\n";
 }

if ($included!="YES") {
  topofpagereport($title,$description,$additionalinfo);
  echo $webstring;
  correct_footer();
}
?>
