<?php
require_once("../Local/db_name.php");
$fallback=FALLBACK_KEY;
$conid=$_SESSION['conid'];
$relogin=$_SESSION['conurl'];      // Set the return value
// Default role is Posting, so at least something familiar shows.
if (empty($_SESSION['conid'])) {$_SESSION['conid']=$conid;}
if (empty($_SESSION['conid'])) {$_SESSION['conid']=$fallback;}
if (empty($_SESSION['conid'])) {$_SESSION['conid']=42;}
if (empty($_SESSION['role'])) {$_SESSION['role']="Posting";}
if (empty($_SESSION['conurl'])) {$_SESSION['conurl']=$relogin;}
$conid=$_SESSION['conid'];
$relogin=$_SESSION['conurl'];      // Set the return value
require_once('PostingCommonCode.php');
// Default role is Posting, so at least something familiar shows.
if (empty($_SESSION['conid'])) {$_SESSION['conid']=$conid;}
if (empty($_SESSION['conid'])) {$_SESSION['conid']=$fallback;}
if (empty($_SESSION['conid'])) {$_SESSION['conid']=42;}
if (empty($_SESSION['role'])) {$_SESSION['role']="Posting";}
if (empty($_SESSION['conurl'])) {$_SESSION['conurl']=$relogin;}
$conid=$_SESSION['conid'];
$relogin=$_SESSION['conurl'];      // Set the return value
unlock_participant('');            // unlock any records locked by this user
$_SESSION=array();                 // Unset session data
unset($_COOKIE[session_name()]);   // Clear cookie
session_destroy();                 // Destroy session data

// LOCALIZATIONS
$title="Logout Confirmation";
$description="<P align=\"center\">You have logged out from Zambia</P>\n";
$additionalinfo="<P align=\"center\"><A HREF=\"http://$relogin\">Log in</A> again.</P>";

// Default role is Posting, so at least something familiar shows.
if (empty($_SESSION['conid'])) {$_SESSION['conid']=$conid;}
if (empty($_SESSION['conid'])) {$_SESSION['conid']=$fallback;}
if (empty($_SESSION['role'])) {$_SESSION['role']="Posting";}
if (empty($_SESSION['conurl'])) {$_SESSION['conurl']=$relogin;}

$conid=$_SESSION['conid'];
topofpagereport($title,$description,$additionalinfo);
correct_footer();
?>
