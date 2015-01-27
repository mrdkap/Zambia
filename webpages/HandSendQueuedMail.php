<?php
/* This page is for small sets of email, so they can be read,
   customized and sent individually by a person.
*/

require_once('email_functions.php');
require_once('StaffCommonCode.php');
global $link;

//LOCALIZATION
$title="Hand Send Queued Mail.php";
$description="<P>Sending queued email via a mailto link.</P>";

if ((isset($_GET['sentid'])) AND (is_numeric($_GET['sentid']))) {
  $pairedvalue_array=array("status=2");
  $message.=update_table_element($link, $title, "EmailQueue", $pairedvalue_array, "emailqueueid", $_GET['sentid']);
}

$query="SELECT emailqueueid, emailto, emailfrom, emailcc, emailsubject, body from EmailQueue ";
$query.="WHERE status in (1) ORDER BY emailtimestamp";

// Retrieve query
list($rows,$header_array,$email_array)=queryreport($query,$link,$title,$description,0);

topofpagereport($title,$description,$additionalinfo);
echo "<P class=\"errmsg\">$message_error</P>\n";
echo "<P class=\"regmsg\">$message</P>\n";

// Build the table of the email to be sent.
echo "<TABLE>\n";
echo "  <TR>\n    <TH>Email</TH>\n      <TH>To</TH>\n    <TH>Subject</TH>\n    <TH>Sent?</TH>\n  </TR>\n";

for ($i=1; $i<=$rows; $i++) {
  $emailqueueid=$email_array[$i]['emailqueueid'];
  $subject=$email_array[$i]['emailsubject'];
  $strip_subject=rawurlencode($subject);
  $from=$email_array[$i]['emailfrom'];
  $cc=$email_array[$i]['emailcc'];
  $to=$email_array[$i]['emailto'];
  if ((isset($cc)) and ($cc !="")) {$toandcc=$to.",".$cc;}else{$toandcc=$to;}
  $body=rawurlencode($email_array[$i]['body']);

  /*
  $body=$email_array[$i]['body'];
  //$body=wordwrap($body,70,"%0A");
  $body=str_replace('"','\'\'',$body);
  $body=str_replace('`','\'',$body);
  $body=str_replace('\r\n','%0A',$body);
  $body=str_replace('
','%0A',$body);
  $body=str_replace(' ','%20',$body);
  $body=str_replace('<','&lt;',$body);
  $body=str_replace('>','&gt;',$body);
  $body=str_replace('&','%26',$body);
  */

  echo "  <TR>\n    <TD><A HREF=\"mailto:$to?subject=$strip_subject&cc=$cc&body=$body\">Send</A></TD>\n";
  echo "    <TD>$to</TD>\n    <TD>$subject</TD>\n";
  echo "    <TD><A HREF=\"HandSendQueuedMail.php?sentid=$emailqueueid\">Done</A></TD>\n  </TR>\n";
}
echo "</TABLE>\n";
correct_footer();
?>
