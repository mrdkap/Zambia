<?php
//This page is intended to be hit from a cron job only.
//Need to add some code to prevent it from being accessed any other way, but leave it exposed for now for testing.
require_once('email_functions.php');

// For verbose purposes
$title="Auto Send Queued Mail";
$description="<P>Sending email now ...</P>\n";
$additionalinfo="<P>This should only appear when the verbose flag is switched on.\n";
$additionalinfo.="Otherwise, since this is supposed to run, quietly, out of cron,\n";
$additionalinfo.="you should never see this.</P>\n";

// For using php mail directly, otherwise will try to use mutt
$usephpmail="True";

// For test verbose=1, otherwise for silent, verbose='';
$verbose='';
if ($_GET['verbose'] == "Yes") { $verbose = true; }

// Limit the number of emails sent at a time
$batchnumber=1;

// Space (in seconds) between emails sent
$sleeptime=25;

// If we are looking for the error messages, verbose will put it to the page, otherwise, just send it.
if ($verbose) {
  require_once('StaffCommonCode.php');
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
} else {
  require_once('data_functions.php');
  require_once('db_functions.php');
  prepare_db();
  set_session_defaults();
}

// Query for all those queued (status=1) and as many as the limit is.
$query="SELECT emailqueueid, emailto, emailfrom, emailcc, emailsubject, body from EmailQueue ";
$query.="WHERE status=1 ORDER BY emailtimestamp limit $batchnumber";

// Add the query to the verbose output, if it is set.
if ($verbose) {echo "<P>EmailQuery Query: $query</P>\n";}

// If the query fails, give the error message both ways, if verbose, or just in the error_log if not.
if (!$result=mysql_query($query,$link)) {
    $message.="Zambia: AutoSendQueuedMail: ".$query." Error querying database.\n";
    error_log($message);
    if ($verbose) {RenderError($title,$message);}
    exit();
    }

// Number of rows returned.
$rows=mysql_num_rows($result);

// If no rows, we are done!
if ($rows==0) exit();

// Set up for the count of good/bad email addresses
$numGood=0;
$numBad=0;

// Build the email array
for ($i=1; $i<=$rows; $i++) {
  $email_array[$i]=mysql_fetch_assoc($result);
 }

// Build the variables, and then send, depending on what send path from above.
for ($i=1; $i<=$rows; $i++) {
  $emailqueueid=$email_array[$i]['emailqueueid'];
  $subject=$email_array[$i]['emailsubject'];
  $from=$email_array[$i]['emailfrom'];
  $cc=$email_array[$i]['emailcc'];
  $to=$email_array[$i]['emailto'];

  // Fix the to to include the cc, if there is one, for php mail.
  if ((isset($cc)) and ($cc !="")) {$toandcc=$to.",".$cc;}else{$toandcc=$to;}

  // Fix quote characters, and wrap the body
  $body=$email_array[$i]['body'];
  $body=str_replace('"','\"',$body);
  $body=str_replace('`','\'',$body);
  $body=wordwrap($body,70,"\r\n");

  $headers="From: $from <$from>\r\nBcc: $cc\r\n$Reply-To: $from <$from>\r\n";
  $flags="-f$from -r$from";

  // If it is verbose, send the information to the screen.
  if ($verbose) {
    echo "<P>Email From: $from</P>\n<P>To: $to</P>\n<P>CC: $cc</P>\n";
    echo "<P>Subject: $subject</P>\n<P>Body: $body</P>\n<P>Headers: $headers</P>\n";
  }

  // Send via php mail else mutt $mailstring, and shell_exec
  if ($usephpmail=="True") {
    $ok=mail($toandcc,$subject,$body,$headers,$flags);
  } else {
    $mailstring="echo -e \"".$body."\" | /usr/bin/mutt -s '".$subject."' -f /dev/null";
    $mailstring.=" -e 'set copy=no' -e 'set from = \"".$from."\"' -c '".$cc."' \"".$to."\"";
    $mailstring.=" > /dev/null ; echo $?";
    if ($verbose) {echo "<P><PRE>$mailstring</PRE></P>\n";}
    $ok=shell_exec("$mailstring");
  }

  // Success or failure on sending.
  if (($ok =="") OR $ok) {

    // Succcess, update the status value to 2, and add to the "good" list.
    $query="UPDATE EmailQueue SET status=2 WHERE emailqueueid=$emailqueueid";

    // If the query fails, give the error message both ways, if verbose, or just in the error_log if not.
    if (!$result=mysql_query($query,$link)) {
      $message.="Zambia: AutoSendQueuedMail: ".$query." Error querying database.\n";
      error_log($message);
      if ($verbose) {RenderError($title,$message);}
      exit();
    }
    $goodList.=sprintf("%d,",$email_array[$i]['emailqueueid']);
    $numGood++;
  } else {

    // Failed, update the status value to 3 and add to the "bad" list.
    $query="UPDATE EmailQueue SET status=3 WHERE emailqueueid=$emailqueueid";

    // If the query fails, give the error message both ways, if verbose, or just in the error_log if not.
    if (!$result=mysql_query($query,$link)) {
      $message.="Zambia: AutoSendQueuedMail: ".$query." Error querying database.\n";
      error_log($message);
      if ($verbose) {RenderError($title,$message);}
      exit();
    }
    $badList.=sprintf("%d,",$email_array[$i]['emailqueueid']);
    $numBad++;
  }

  // Pause between sending instances.
  sleep($sleeptime);
}

// Reporting if verbose on the good/bad states.
$goodList=substr($goodList,0,-1); //remove final trailing comma
$badList=substr($badList,0,-1); //remove final trailing comma
if ($verbose) {echo "Num good: $numGood. Num bad: $numBad.<BR>\n";}
if ($verbose) {echo "Good list: $goodList <BR>\n";}
if ($verbose) {echo "Bad list: $badList <BR>\n";}
if ($verbose) {correct_footer();}
exit();
?>
