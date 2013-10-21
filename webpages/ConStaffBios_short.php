<?php
require_once('StaffCommonCode.php');

$from="programming@nelaonline.org";
$to="PercyHaven@gmail.com";
$cc="NELA.Program@gmail.com";
$subject="Test message";

$message="Greetings,\r\n\r\nThis is a test 9 in a series.\r\nThis is only a test 9 in a series.\r\nOf the php email system.\r\n\r\nThank you,\r\nPercival";
$message = wordwrap($message, 70, "\r\n"); 

$headers="From: $from\r\nBcc: $cc\r\nReply-To: $from\r\n";
$flags="-f$from -r$from";
topofpagereport($title,$description,$additionalinfo);
if ($message_error!="") { 
  echo "<P class=\"errmsg\">$message_error</P>\n";
}
if ($message!="") {
  echo "<P class=\"regmsg\">$message</P>\n";
}

echo "<P>Sending ... ";
$ok=mail($to, $subject, $message, $headers,$flags);

if (($ok =="") OR $ok) {
  //succeeded
  echo "Sent! ($ok)</P>\n";
} else {
  //failed
  echo "Not sent, sorry. ($ok)</P>\n";
  }

correct_footer();
?>
