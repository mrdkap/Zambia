<?php
// This page has two completely different entry points from a user flow standpoint:
//   1) Beginning of send email flow -- start to specify parameters
//   2) After verify -- 'back' can change parameters -- 'send' fire off email sending code
require_once('email_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
global $title, $message, $link;
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

if (isset($_POST['sendto'])) { // page has been visited before so restore previous values to form
  $email=get_email_from_post();
} else { // page hasn't just been visited
  $email=set_email_defaults();
}
$subst_list=array("\$BADGEID\$","\$FIRSTNAME\$","\$LASTNAME\$","\$EMAILADDR\$","\$PUBNAME\$","\$BADGENAME\$","\$SCHEDULE\$","\$FULLSCHEDULE\$","\$BIOS\$");
$message_warning="";
if ($_POST['navigate']!='send') {
  render_send_email($email,$subst_list,$message_warning);
  exit(0);
}
// Queue email to be sent into db.  Cron job will actually send it at a pace not to trigger outgoing spam filters. 
$title="Staff Send Email";
// This should be done above?
//$email=get_email_from_post();
$query="SELECT emailtoquery FROM EmailTo where emailtoid=".$email['sendto'];
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$emailto=mysql_fetch_array($result,MYSQL_ASSOC);
$query=eval("return<<<EOD\n".$emailto['emailtoquery']."\nEOD;\n");
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$i=0;
while ($recipientinfo[$i]=mysql_fetch_array($result,MYSQL_ASSOC)) {
    $i++;
    }
$recipient_count=$i;
for ($i=0; $i<$recipient_count; $i++) {

  // variablized for substitution
  $individual=$recipientinfo[$i]['badgeid'];

  /* This query pulls the schedule information for an individual, and
   then collects it, and stuffs it into a single variable, for
   expansion later. */
  $query = <<<EOD
SELECT 
    DISTINCT CONCAT(title, 
        if((moderator=1),' (moderating)',''), 
        if ((aidedecamp=1),' (assisting)',''), 
        if((volunteer=1),' (outside wristband checker)',''), 
        if((introducer=1),' (announcer/inside room attendant)',''),
        ' - ',
        DATE_FORMAT(ADDTIME(constartdate,starttime),'%a %l:%i %p'),
        ' - ',
        CASE
          WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')
          WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')
          ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
          END,
        ' in room ',
	roomname) as Title,
    pubsname,
    WEB.descriptiontext AS "description_good_web",
    BOOK.descriptiontext AS "description_good_book"
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms USING (roomid)
    JOIN ParticipantOnSession USING (sessionid,conid)
    JOIN Participants USING (badgeid)
    JOIN UserHasPermissionRole USING (badgeid,conid)
    JOIN PermissionRoles USING (permroleid)
    JOIN ConInfo USING (conid)
    LEFT JOIN (SELECT
	      descriptiontext,
	      sessionid
	    FROM
                Descriptions
	      JOIN DescriptionTypes USING (descriptiontypeid)
	      JOIN BioStates USING (biostateid)
	      JOIN BioDests USING (biodestid)
	    WHERE
              conid=$conid AND
              descriptiontypename="description" AND
	      biostatename="good" AND
	      biodestname="web") AS WEB USING (sessionid)
    LEFT JOIN (SELECT
	      descriptiontext,
	      sessionid
	    FROM
                Descriptions
	      JOIN DescriptionTypes USING (descriptiontypeid)
	      JOIN BioStates USING (biostateid)
	      JOIN BioDests USING (biodestid)
	    WHERE
              conid=$conid AND
              descriptiontypename="description" AND
	      biostatename="good" AND
	      biodestname="book") AS BOOK USING (sessionid)
  WHERE
    permrolename in ('Participant','General','Programming','SuperProgramming','SuperGeneral') AND
    conid=$conid AND
    badgeid='$individual'
  ORDER BY
    starttime

EOD;

  // Retrieve query
  list($rows,$schedule_header,$schedule_array)=queryreport($query,$link,$title,$description,0);
  $recipientinfo[$i]['schedule'].="Your schedule:
";
  for ($j=1; $j<=$rows; $j++) {
    $recipientinfo[$i]['schedule'].=$schedule_array[$j]['Title']."
";
    $recipientinfo[$i]['fullschedule'].=$schedule_array[$j]['Title']."
Web Description: ".$schedule_array[$j]['description_good_web']."
Bood Description: ".$schedule_array[$j]['description_good_book']."

";
  }

  /* This pulls the schedule information for an individual, and
   then collects it, and stuffs it into a single variable, for
   expansion later. */
  $bioinfo=getBioData($individual);

  /* Presenting all the type pieces.
     Currently we are only using en-us as the language,
     at some point this should expand beyond that.
     Currently we are using edited as the state, at some
     point we should move to good. */
  $biostate='edited'; // for ($l=0; $l<count($bioinfo['biostate_array']); $l++) {
  $biolang='en-us'; // for ($k=0; $k<count($bioinfo['biolang_array']); $k++) {
  $recipientinfo[$i]['bios'].="Your bios:
";
  for ($j=0; $j<count($bioinfo['biotype_array']); $j++) {
    for ($m=0; $m<count($bioinfo['biodest_array']); $m++) {

      // Setup for keyname, to collapse all four variables into one passed name.
      $biotype=$bioinfo['biotype_array'][$j];
      // $biolang=$bioinfo['biolang_array'][$k];
      // $biostate=$bioinfo['biostate_array'][$l];
      $biodest=$bioinfo['biodest_array'][$l];
      $keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";

      // Fold the information into the bios variable so it can be substituted below.
      $recipientinfo[$i]['bios'].=$biotype." bio: ".$bioinfo[$keyname]."

";
    }
  }
}

$query="SELECT email FROM CongoDump WHERE badgeid=".$email['sendfrom'];
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$emailfrom=mysql_result($result,0);

if ($email['sendcc'] != 0) {
  $query="SELECT email FROM CongoDump WHERE badgeid=".$email['sendcc'];
  if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
  }
  $emailcc=mysql_result($result,0);
}
$goodCount=0;
$badCount=0;
unset($arrayOfGood);
unset($arrayOfBad);
for ($i=0; $i<$recipient_count; $i++) {
  $name=(strlen($recipientinfo[$i]['pubsname'])>0)?$recipientinfo[$i]['pubsname']:$recipientinfo[$i]['firstname']." ".$recipientinfo[$i]['lastname'];
  if (!filter_var($recipientinfo[$i]['email'],FILTER_VALIDATE_EMAIL)) {
    // bad email address
    $badCount++;
    $arrayOfBad[]=array('badgeid'=>$recipientinfo[$i]['badgeid'],'name'=>$name,'email'=>$recipientinfo[$i]['email']);
  } else {
    $goodCount++;
    $arrayOfGood[]=array('badgeid'=>$recipientinfo[$i]['badgeid'],'name'=>$name,'email'=>$recipientinfo[$i]['email']);
    $repl_list=array($recipientinfo[$i]['badgeid'],
		     $recipientinfo[$i]['firstname'],
		     $recipientinfo[$i]['lastname'],
		     $recipientinfo[$i]['email'],
		     $recipientinfo[$i]['pubsname'],
		     $recipientinfo[$i]['badgename'],
		     $recipientinfo[$i]['schedule'],
                     $recipientinfo[$i]['fullschedule'],
                     $recipientinfo[$i]['bios']);
    $emailverify['body']=str_replace($subst_list,$repl_list,$email['body']);
    $query="INSERT INTO EmailQueue (emailto, emailfrom, emailcc, emailsubject, body, status) ";
    // to address
    $query.="VALUES (\"".mysql_real_escape_string($recipientinfo[$i]['email'],$link)."\",";
    // from address
    $query.="\"".mysql_real_escape_string($emailfrom,$link)."\",";
    // cc (bcc) address
    $query.="\"".mysql_real_escape_string($emailcc,$link)."\",";
    // subject
    $query.="\"".mysql_real_escape_string($email['subject'],$link)."\",";
    // body
    $query.="\"".mysql_real_escape_string(wordwrap(preg_replace("/(?<!\\r)\\n/","\r\n",$emailverify['body']),70,"\r\n"),$link)."\",";
    // status 1 is unsent (queued)
    $query.="1);";
    if (!$result=mysql_query($query,$link)) {
      db_error($title,$query,$staff=true); // outputs messages regarding db error
      exit(0);
    }
  }
}   

renderQueueEmail($goodCount,$arrayOfGood,$badCount,$arrayOfBad);
?>
