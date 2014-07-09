<?php
//This page is intended to be hit from a cron job only.
//Need to add some code to prevent it from being accessed any other way, but leave it exposed for now for testing.
require_once('email_functions.php');
require_once('StaffCommonCode.php');

// For using php mail directly, otherwise will try to use mutt
$usephpmail="True";

// For test verbose=1, otherwise for silent, verbose='';
$verbose='1';

if ($verbose) {staff_header("Auto Send Queued Mail");}

$query="SELECT emailqueueid, emailto, emailfrom, emailcc, emailsubject, body from EmailQueue ";
$query.="WHERE status=1 ORDER BY emailtimestamp";
if ($verbose) {echo "<P>EmailQuery Query: $query</P>\n";}
if (!$result=mysql_query($query,$link)) {
    $message.="Zambia: AutoSendQueuedMail: ".$query." Error querying database.\n";
    error_log($message);
    //RenderError($title,$message);
    exit();
    }
$rows=mysql_num_rows($result);
if ($rows==0) exit();
$numGood=0;
$numBad=0;
for ($i=1; $i<=$rows; $i++) {
  $email_array[$i]=mysql_fetch_assoc($result);
 }

// Limit it to 10 sent at a time.
if ($rows > 10) {$rows=10;}

for ($i=1; $i<=$rows; $i++) {
  $emailqueueid=$email_array[$i]['emailqueueid'];
  $subject=$email_array[$i]['emailsubject'];
  $from=$email_array[$i]['emailfrom'];
  $cc=$email_array[$i]['emailcc'];
  $to=$email_array[$i]['emailto'];
  if ((isset($cc)) and ($cc !="")) {$toandcc=$to.",".$cc;}else{$toandcc=$to;}
  $body=$email_array[$i]['body'];
  $body=str_replace('"','\"',$body);
  $body=str_replace('`','\'',$body);
  $body=wordwrap($body,70,"\r\n");
  $headers="From: $from <$from>\r\nBcc: $cc\r\n$Reply-To: $from <$from>\r\n";
  $flags="-f$from -r$from";
  if ($verbose) {
    echo "<P>Email From: $from</P>\n<P>To: $to</P>\n<P>CC: $cc</P>\n";
    echo "<P>Subject: $subject</P>\n<P>Body: $body</P>\n<P>Headers: $headers</P>\n";
  }
  // For php mail else mutt $mailstring, and shell_exec
  if ($usephpmail=="True") {
    $ok=mail($toandcc,$subject,$body,$headers,$flags);
  } else {
    $mailstring="echo -e \"".$body."\" | /usr/bin/mutt -s '".$subject."' -f /dev/null";
    $mailstring.=" -e 'set copy=no' -e 'set from = \"".$from."\"' -c '".$cc."' \"".$to."\"";
    $mailstring.=" > /dev/null ; echo $?";
    if ($verbose) {echo "<P><PRE>$mailstring</PRE></P>\n";}
    $ok=shell_exec("$mailstring");
  }
  if (($ok =="") OR $ok) {
    //succeeded
    $query="UPDATE EmailQueue SET status=2 WHERE emailqueueid=$emailqueueid";
    if (!$result=mysql_query($query,$link)) {
      $message.="Zambia: AutoSendQueuedMail: ".$query." Error querying database.\n";
      error_log($message);
      //RenderError($title,$message);
      exit();
    }
    $goodList.=sprintf("%d,",$email_array[$i]['emailqueueid']);
    $numGood++;
  } else {
    //failed
    $query="UPDATE EmailQueue SET status=3 WHERE emailqueueid=$emailqueueid";
    if (!$result=mysql_query($query,$link)) {
      $message.="Zambia: AutoSendQueuedMail: ".$query." Error querying database.\n";
      error_log($message);
      //RenderError($title,$message);
      exit();
    }
    $badList.=sprintf("%d,",$email_array[$i]['emailqueueid']);
    $numBad++;
  }
  sleep(25);
}

$goodList=substr($goodList,0,-1); //remove final trailing comma
$badList=substr($badList,0,-1); //remove final trailing comma
if ($verbose) {echo "Num good: $numGood. Num bad: $numBad.<BR>\n";}
if ($verbose) {echo "Good list: $goodList <BR>\n";}
if ($verbose) {echo "Bad list: $badList <BR>\n";}
exit();
?>
