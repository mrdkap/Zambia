<?php
// Default role is Posting, so at least something familiar shows.
require_once('PostingCommonCode.php');

// Get the next event as the fallback otherwise pull the fallback from the db_name file
$query=<<<EOF
SELECT
    conid,
    conurl
  FROM
      ConInfo
   WHERE
    (DATE_ADD(constartdate, INTERVAL connumdays day) >= CURRENT_DATE())
   ORDER BY
    constartdate
   LIMIT 1
EOF;

// Get the key and URL
$result=mysql_query($querypast,$link);
if ($result) {
  $dbobject=mysql_fetch_object($result);
  $key=$dbobject->conid;
  $url=$dbobject->conurl;
} else {
  $key=FALLBACK_KEY;
  $url=FALLBACK_URL;
}

// Make a guess and tne conid and conurl
if (empty($_SESSION['conid'])) {$_SESSION['conid']=$conid;}
if (empty($_SESSION['conid'])) {$_SESSION['conid']=$key;}
if (empty($_SESSION['role'])) {$_SESSION['role']="Posting";}
if (empty($_SESSION['conurl'])) {$_SESSION['conurl']=$conurl;}
if (empty($_SESSION['conurl'])) {$_SESSION['conurl']=$url;}
$conid=$_SESSION['conid'];
$conurl=$_SESSION['conurl'];
unlock_participant('');            // unlock any records locked by this user
$_SESSION=array();                 // Unset session data
unset($_COOKIE[session_name()]);   // Clear cookie
session_destroy();                 // Destroy session data

// Attempt to make the login more useful than just the "Return"
if (!empty($conid)) {
  $relogin.="$conurl/webpages/login.php?newconid=$conid";
}

// LOCALIZATIONS
$title="Logout Confirmation";
$description="<P align=\"center\">You have logged out from Zambia</P>\n";
$additionalinfo="<P align=\"center\"><A HREF=\"http://$relogin\">Log in</A> again.</P>\n";

// Default role is Posting, so at least something familiar shows.
if (empty($_SESSION['conid'])) {$_SESSION['conid']=$conid;}
if (empty($_SESSION['conid'])) {$_SESSION['conid']=$key;}
if (empty($_SESSION['role'])) {$_SESSION['role']="Posting";}
if (empty($_SESSION['conurl'])) {$_SESSION['conurl']=$conurl;}
if (empty($_SESSION['conurl'])) {$_SESSION['conurl']=$url;}

$conid=$_SESSION['conid'];
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
correct_footer();
?>
