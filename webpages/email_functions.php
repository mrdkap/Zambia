<?php
// function $email=get_email_from_post()
// reads post variable to populate email array
// returns email array or false if an error was encountered.
// A message describing the problem will be stored in global variable $message_error
function get_email_from_post() {
  global $message_error;
  $message_error="";
  $email['sendto']=$_POST['sendto'];
  $email['sendfrom']=$_POST['sendfrom'];
  $email['sendcc']=$_POST['sendcc'];
  $email['subject']=stripslashes($_POST['subject']);
  $email['body']=stripslashes($_POST['body']);
  return($email);
}

// function $OK=validate_email($email)
// Checks if values in $email array are acceptible
function validate_email($email) {
  global $message;
  $message="";
  $OK=true;
  if (strlen($email['subject'])<6) {
    $message.="Please enter a more substantive subject.<BR>\n";
    $OK=false;
  }
  if (strlen($email['body'])<16) {
    $message.="Please enter a more substantive body.<BR>\n";
    $OK=false;
  }
  return($OK);
}

// function $email=set_email_defaults()
// Sets values for $email array to be used as defaults for the email
// form when first entering page.
function set_email_defaults() {
  $email['sendto']=1; // default to all participants
  $email['sendfrom']=1; // default to Arisia Programming
  $email['sendcc']=1; // default to None
  $email['subject']="";
  $email['body']="";
  return($email);
}

// function render_send_email($email,$message_warning)
// $email is an array with all values for the send email form:
//   sendto, sendfrom, sendcc, subject, body
// $message_warning will be displayed at the top, only if set
// This function will render the entire page.
// This page will next go to the StaffSendEmailCompose_POST page
function render_send_email($email,$substitutions,$message_warning) {
  require_once('StaffCommonCode.php');
  $title="Send Email to Participants";
  $description="<H3>Step 1 -- Compose Email</H3>\n";
  $conid=$_SESSION['conid'];

  // From/CC email query
  $ccquery = <<<EOD
SELECT
    badgeid,
    badgename
  FROM
      UserHasConRole
    JOIN ConRoles USING (conroleid)
    JOIN CongoDump USING (badgeid)
  WHERE
    conid=$conid AND
    conrolename not like '%GOH%'
  GROUP BY
    email
  ORDER BY
    display_order
EOD;

  // Start the output
  topofpagereport($title,$description,$additionalinfo);

  if (strlen($message_warning)>0) {
    echo "<P class=\"message_warning\">$message_warning</P>\n";
  }
  echo "<FORM name=\"emailform\" method=POST action=\"StaffSendEmailCompose_POST.php\">\n";
  echo "<TABLE><TR>";
  echo "    <TD><LABEL for=\"sendto\">To: </LABEL></TD>\n";
  echo "    <TD><SELECT name=\"sendto\">\n";
  populate_select_from_table("EmailTo", $email['sendto'], "", false);
  echo "    </SELECT></TD></TR>";
  echo "<TR><TD><LABEL for=\"sendfrom\">From: </LABEL></TD>\n";
  echo "    <TD><SELECT name=\"sendfrom\">\n";
  populate_select_from_query($ccquery, 0, "None", true);
  echo "    </SELECT></TD></TR>";
  echo "<TR><TD><LABEL for=\"sendcc\">CC: </LABEL></TD>\n";
  echo "    <TD><SELECT name=\"sendcc\">\n";
  populate_select_from_query($ccquery, 0, "None", true);
  echo "    </SELECT></TD></TR>";
  echo "<TR><TD><LABEL for=\"subject\">Subject: </LABEL></TD>\n";
  echo "    <TD><INPUT name=\"subject\" type=\"text\" size=\"40\" value=\"";
  echo htmlspecialchars($email['subject'],ENT_NOQUOTES)."\">\n";
  echo "    </TD></TR></TABLE><BR>\n";
  echo "<TEXTAREA name=\"body\" cols=\"80\" rows=\"25\">";
  echo htmlspecialchars($email['body'],ENT_NOQUOTES)."</TEXTAREA><BR>\n";
  echo "<BUTTON class=\"ib\" type=\"reset\" value=\"reset\">Reset</BUTTON>\n";
  echo "<BUTTON class=\"ib\" type=\"submit\" value=\"seeit\">See it</BUTTON>\n";
  echo "</FORM><BR>\n";
  echo "<P>Available substitutions:</P>\n";
  echo "<TABLE>\n";
  $i=0;
  foreach ($substitutions as $substitution) {
    if ($i==0) {
      echo "<TR><TD>$substitution</TD>";
      $i++;
    } else {
      echo "<TD>$substitution</TD></TR>\n";
      $i--;
    }
  }
  echo "</TABLE>\n";
  correct_footer();
}

// function renderQueueEmail($goodCount,$arrayOfGood,$badCount,$arrayOfBad)
//
function renderQueueEmail($goodCount,$arrayOfGood,$badCount,$arrayOfBad) {
  $title="Results of Queueing Email";
  $description="<P>Good and bad count of messages queued.</P>\n";
  $additionalinfo.="<P>If cron job is not enabled, the next step is to visit\n";
  $additionalinfo.="<A HREF=AutoSendQueuedMail.php>Auto Send</A> or\n";
  $additionalinfo.="<A HREF=HandSendQueuedMail.php>Hand Send</A> to send the email.</P>\n";
  require_once('StaffCommonCode.php');
  topofpagereport($title,$description,$additionalinfo);

  echo "<P>$goodCount message(s) were queued for email transmission.<BR>\n";
  echo "$badCount message(s) failed.</P>\n";
  echo "<P>List of messages successfully queued:<BR>\n";
  echo "Badgeid, Name for Publications, Email Address<BR>\n";
  if ($goodCount > 0) {
    foreach ($arrayOfGood as $recipient) {
      echo htmlspecialchars($recipient['badgeid']).", ";
      echo htmlspecialchars($recipient['name']).", ";
      echo htmlspecialchars($recipient['email'])."<BR>\n";
    }
  }
  echo"</P>\n";
  echo "<P>List of recipients which failed:<BR>\n";
  echo "Badgeid, Name for Publications, Email Address<BR>\n";
  if ($badCount > 0) {
    foreach ($arrayOfBad as $recipient) {
      echo htmlspecialchars($recipient['badgeid']).", ";
      echo htmlspecialchars($recipient['name']).", ";
      echo htmlspecialchars($recipient['email'])."<BR>\n";
    }
  }
  echo"</P>\n";
  correct_footer();
}

// function render_verify_email($email,$emailverify)
// $email is an array with all values for the send email form:
//   sendto, sendfrom, subject, body
// $emailverify is an array with all values for the verify form:
//   recipient_list, emailfrom, body
// This function will render the entire page.
// This page will next go to the StaffSendEmailResults_POST page
function render_verify_email($email,$email_verify,$message_warning) {
  $title="Send Email";
  $description="<H3>Step 2 -- Verify </H3>\n";
  require_once('StaffCommonCode.php');
  topofpagereport($title,$description,$additionalinfo);

  if (strlen($message_warning)>0) {
    echo "<P class=\"message_warning\">$message_warning</P>\n";
  }

  echo "<FORM name=\"emailverifyform\" method=POST action=\"StaffSendEmailCompose.php\">\n";
  echo "<P>Recipient List:<BR>\n";
  echo "<TEXTAREA readonly rows=\"8\" cols=\"70\">";
  echo $email_verify['recipient_list']."</TEXTAREA></P>\n";
  echo "<P>Rendering of message body to first recipient:<BR>\n";
  echo "<TEXTAREA readonly rows=\"12\" cols=\"70\">";
  echo $email_verify['body']."</TEXTAREA></P>\n";
  echo "<INPUT type=\"hidden\" name=\"sendto\" value=\"".$email['sendto']."\">\n";
  echo "<INPUT type=\"hidden\" name=\"sendfrom\" value=\"".$email['sendfrom']."\">\n";
  echo "<INPUT type=\"hidden\" name=\"sendcc\" value=\"".$email['sendcc']."\">\n";
  echo "<INPUT type=\"hidden\" name=\"subject\" value=\"".htmlspecialchars($email['subject'])."\">\n";
  echo "<INPUT type=\"hidden\" name=\"body\" value=\"".htmlspecialchars($email['body'])."\">\n";
  echo "<BUTTON class=\"ib\" type=\"submit\" name=\"navigate\" value=\"goback\">Go Back</BUTTON>\n";
  echo "<BUTTON class=\"ib\" type=\"submit\" name=\"navigate\" value=\"send\">Send</BUTTON>\n";
  echo "</FORM><BR>\n";
  correct_footer();
}

function render_send_email_engine($email,$message_warning) {
  require_once('StaffCommonCode.php');

  $title="Pretend to actually send email.";
  $description="<H3>Step 3 -- Actually Send Email </H3>\n";
  $additionalinfo.="<P>If cron job is not enabled, the next step is to visit\n";
  $additionalinfo.="<A HREF=AutoSendQueuedMail.php>Auto Send</A> or\n";
  $additionalinfo.="<A HREF=HandSendQueuedMail.php>Hand Send</A> to send the email.</P>\n";

  topofpagereport($title,$description,$additionalinfo);

  if (strlen($message_warning)>0) {
    echo "<P class=\"message_warning\">$message_warning</P>\n";
  }
  correct_footer();
}

?>
