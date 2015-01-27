<?php
// Not sure if there is any need to support post/been here before
require_once('email_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
global $message,$link;
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

// List of replacements
$subst_list=array("\$BADGEID\$","\$FIRSTNAME\$","\$LASTNAME\$","\$EMAILADDR\$","\$PUBNAME\$","\$BADGENAME\$","\$SCHEDULE\$","\$FULLSCHEDULE\$","\$BIOS\$","\$HOTELROOM\$","\$CONFIRMNUM\$");

$title="Send Email (Step 2 - verify)";

if (!isset($_POST['sendto'])) { // page has not been visited before
    $message_error="Expected POST data was missing.  This page is intended to be reached via a form.";
    $message_error.=" It will not work if you link to it directly.\n";
    RenderError ($title, $message_error);
    exit(0);
    }
$email=get_email_from_post();
if (!validate_email($email)) {
    render_send_email($email,$message); // $message came from validate_email
    exit(0);
    }
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
$emailverify['recipient_list']="";
for ($i=0; $i<$recipient_count; $i++) {
  $emailverify['recipient_list'].=$recipientinfo[$i]['pubsname']." - ";
  $emailverify['recipient_list'].=htmlspecialchars($recipientinfo[$i]['email'],ENT_NOQUOTES)."\n";
}
// variablized for substitution
$individual=$recipientinfo[0]['badgeid'];

/* This query pulls the compensation information for an individual,
   and provides it in the appropriate variables for expansion later.
*/
$query = <<<EOD
SELECT
    badgeid,
    hotelroom,
    confirmnum
  FROM
      (SELECT
          badgeid,
          compdescription AS confirmnum
        FROM
            Compensation
          JOIN CompensationTypes USING (comptypeid)
        WHERE
          conid=$conid AND
          comptypename in ('Room Cost')) CFN
    LEFT JOIN (SELECT
          badgeid,
          compdescription AS hotelroom
        FROM
            Compensation
          JOIN CompensationTypes USING (comptypeid)
        WHERE
          conid=$conid AND
          comptypename in ('Room Count')) HLR USING (badgeid)
    WHERE
      badgeid=$individual
EOD;

// Retrieve query
list($comprows,$comp_header,$comp_array)=queryreport($query,$link,$title,$description,0);

// Assign the values
$recipientinfo[$i]['hotelroom']=$comp_array[1]['hotelroom'];
$recipientinfo[$i]['confirmnum']=$comp_array[1]['confirmnum'];

/* This query pulls the schedule information for an individual, and
   then collects it, and stuffs it into a single variable, for
   expansion later. */
$query = <<<EOD
SELECT
    DISTINCT CONCAT(title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),
        if((moderator in ('1','Yes')),' (moderating)',''),
        if ((aidedecamp in ('1','Yes')),' (assisting)',''),
        if((volunteer in ('1','Yes')),' (outside wristband checker)',''),
        if((introducer in ('1','Yes')),' (announcer/inside room attendant)',''),
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
    desc_good_web,
    desc_good_book
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
        sessionid,
	descriptiontext as title_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='title' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') TGW USING (sessionid)
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as subtitle_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='subtitle' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') SGW USING (sessionid)
    LEFT JOIN (SELECT
	      sessionid,
	      descriptiontext AS desc_good_web
	    FROM
                Descriptions
	      JOIN DescriptionTypes USING (descriptiontypeid)
	      JOIN BioStates USING (biostateid)
	      JOIN BioDests USING (biodestid)
	    WHERE
              conid=$conid AND
              descriptiontypename="description" AND
	      biostatename="good" AND
	      biodestname="web") AS DGW USING (sessionid)
    LEFT JOIN (SELECT
	      sessionid,
	      descriptiontext AS desc_good_book
	    FROM
                Descriptions
	      JOIN DescriptionTypes USING (descriptiontypeid)
	      JOIN BioStates USING (biostateid)
	      JOIN BioDests USING (biodestid)
	    WHERE
              conid=$conid AND
              descriptiontypename="description" AND
	      biostatename="good" AND
	      biodestname="book") AS DGB USING (sessionid)
  WHERE
     permrolename in ('Participant','General','Programming','SuperProgramming','SuperGeneral') AND
    conid=$conid AND
    badgeid='$individual'
  ORDER BY
    starttime

EOD;

// Retrieve query
list($rows,$schedule_header,$schedule_array)=queryreport($query,$link,$title,$description,0);
$recipientinfo[0]['schedule'].="Your schedule:
";
for ($j=1; $j<=$rows; $j++) {
  $recipientinfo[0]['schedule'].=$schedule_array[$j]['Title']."
";
    $recipientinfo[0]['fullschedule'].=$schedule_array[$j]['Title']."
Web Description: ".$schedule_array[$j]['desc_good_web']."
Bood Description: ".$schedule_array[$j]['desc_good_book']."

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
$recipientinfo[0]['bios'].="Your bios:
";
for ($j=0; $j<count($bioinfo['biotype_array']); $j++) {
  for ($m=0; $m<count($bioinfo['biodest_array']); $m++) {

    // Setup for keyname, to collapse all four variables into one passed name.
    $biotype=$bioinfo['biotype_array'][$j];
    // $biolang=$bioinfo['biolang_array'][$k];
    // $biostate=$bioinfo['biostate_array'][$l];
    $biodest=$bioinfo['biodest_array'][$m];
    $keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";

    // Fold the information into the bios variable so it can be substituted below.
    $recipientinfo[0]['bios'].=$biodest." ".$biotype.": ".$bioinfo[$keyname]."

";
  }
}

$query="SELECT email FROM CongoDump WHERE badgeid=".$email['sendfrom'];
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$emailverify['emailfrom']=mysql_result($result,0);
$repl_list=array($recipientinfo[0]['badgeid'],
		 $recipientinfo[0]['firstname'],
		 $recipientinfo[0]['lastname'],
		 $recipientinfo[0]['email'],
		 $recipientinfo[0]['pubsname'],
		 $recipientinfo[0]['badgename'],
		 $recipientinfo[0]['schedule'],
                 $recipientinfo[0]['fullschedule'],
		 $recipientinfo[$i]['bios'],
		 $recipientinfo[$i]['hotelroom'],
		 $recipientinfo[$i]['confirmnum']);
$emailverify['body']=str_replace($subst_list,$repl_list,$email['body']);
render_verify_email($email,$emailverify,$message_warning="");
?>
