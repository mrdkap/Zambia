<?php
require_once('PostingCommonCode.php');
$relogin=$_SESSION['conurl'];      // Set the return value
unlock_participant('');            // unlock any records locked by this user
$_SESSION=array();                 // Unset session data
unset($_COOKIE[session_name()]);   // Clear cookie
session_destroy();                 // Destroy session data

$title="Logout Confirmation";
participant_header($title);

echo "<P align=\"center\">You have logged out from Zambia</P>\n";
echo "<P align=\"center\"><A HREF=\"http://$relogin\">Log in</A> again.</P>";

participant_footer();
?>