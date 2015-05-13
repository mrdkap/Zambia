<?php
/* This builds the GenInfo output, switched on if it is called from
   the API or called from within the program, and switched on the
   conid.  If no conid is passed, it pulls it from the sessionid.
   Other states and information are pulled either based on the phase,
   or based on the existence of the file in the right place. 
*/
function GenInfoString($api_p,$conid) {
  require_once('PostingCommonCode.php');
  global $link;
  $nowis=time();

  // Test for conid being passed in.
  if ($conid == "") {
    $conid=$_SESSION['conid'];
  }

  // Set the con info from the conid.
  $query="SELECT conname,connumdays,congridspacer,constartdate,conlogo,conurl from ConInfo where conid=$conid";
  list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
  $conname=$conname_array[1]['conname'];
  $connumdays=$conname_array[1]['connumdays'];
  $Grid_Spacer=$conname_array[1]['congridspacer'];
  $constart=$conname_array[1]['constartdate'];
  $logo=$conname_array[1]['conlogo'];
  $conurl=$conname_array[1]['conurl'];

  // Set up the phase array.
  $query="SELECT phasestate,phasetypename FROM Phase JOIN PhaseTypes USING (phasetypeid) WHERE conid=$conid";
  list($phaserows,$phaseheader_array,$fullphase_array)=queryreport($query,$link,$title,$description,0);
  for ($i=1; $i<=$phaserows; $i++) {
    $phase_array[$fullphase_array[$i]['phasetypename']]=$fullphase_array[$i]['phasestate'];
  }

  // Builds the header and footer for each section.
  $divfooter ="    </UL>\n";
  $divfooter.="  </DIV>\n";
  $genheader ="  <DIV style=\"float: left; width: 50%\">\n";
  $genheader.="    <H3>General Information</H3>\n";
  $genheader.="    <UL>\n";
  $progheader ="  <DIV style=\"float: right; width: 50%;\">\n";
  $progheader.="    <H3>Programming Information</H3>\n";
  $progheader.="    <UL>\n";
  $volheader ="  <DIV style=\"float: left; width: 50%;\">\n";
  $volheader.="    <H3>Volunteer Information</H3>\n";
  $volheader.="    <UL>\n";
  $vendheader ="  <DIV style=\"float: right; width: 50%;\">\n";
  $vendheader.="    <H3>Vending Information</H3>\n";
  $vendheader.="    <UL>\n";

  // Set up the description correctly.
  $description.="<DIV style=\" width: 100%; \">\n";

  // Builds the General Block.
  $genbody="";
  $genbody.="      <LI><A HREF=\"ConStaffBios.php?conid=$conid\">Con Staff</A></LI>\n";
  if ($phase_array['Venue Available'] == '0' ) {
    $genbody.="      <LI><A HREF=\"Venue.php?conid=$conid\">Venue Information</A></LI>\n";
  }
  if ($phase_array['Comments Displayed'] == '0' ) {
    $genbody.="      <LI><A HREF=\"CuratedComments.php?conid=$conid\">Comments about the event</A></LI>\n";
  }
  if (file_exists("../Local/$conid/Program_Book.pdf")) {
    $genbody.="      <LI><A HREF=\"../Local/$conid/Program_Book.pdf\">Program Book</A></LI>\n";
  }

  // Includes the Con Chair letter in the choices at the top and in the body, if it exists.
  $conchairletter="";
  if (file_exists("../Local/$conid/Con_Chair_Welcome")) {
    $genbody.="      <LI><A HREF=\"#conchairletter\">Welcome from the Con Chair</A></LI>\n";
    $conchairletter.="<DIV style=\" display: block; width: 100%; float: left; \">\n";
    $conchairletter.="  <A NAME=\"conchairletter\">&nbsp;</A>\n";
    $conchairletter.="  <HR>\n<H3>A welcome letter from the Con Chair:</H3>\n<BR>\n";
    $conchairletter.=file_get_contents("../Local/$conid/Con_Chair_Welcome");
    $conchairletter.="</DIV>\n";
  }

  // Includes the Org Chair letter in the choices at the top and in the body, if it exists.
  $orgletter="";
  if (file_exists("../Local/$conid/Org_Welcome")) {
    $genbody.="      <LI><A HREF=\"#orgletter\">Welcome from the Organization</A></LI>\n";
    $orgletter.="<DIV style=\" display: block; width: 100%; float: left; \">\n";
    $orgletter.="<A NAME=\"orgletter\">&nbsp;</A>\n";
    $orgletter.="<HR>\n<H3>A welcome letter from the Organization:</H3>\n<BR>\n";
    $orgletter.=file_get_contents("../Local/$conid/Org_Welcome");
    $orgletter.="</DIV>\n";
  }

  // Includes the rules in the choices at the top and in the body, if it exists.
  $rules="";
  if (file_exists("../Local/$conid/Rules")) {
    $genbody.="      <LI><A HREF=\"#rules\">Rules</A></LI>\n";
    $rules.="<DIV style=\" display: block; width: 100%; float: left; \">\n";
    $rules.="<A NAME=\"rules\">&nbsp;</A>\n";
    $rules.="<HR>\n<H3>Rules:</H3>\n<BR>\n";
    $rules.=file_get_contents("../Local/$conid/Rules");
    $rules.="</DIV>\n";
  }

  // Includes whatever is in the Gen Info file, if it exists.
  $geninfo="";
  if (file_exists("../Local/$conid/Gen_Info")) {
    $geninfo.="<DIV style=\" display: block; width: 100%; float: left; \">\n";
    $geninfo.=file_get_contents("../Local/$conid/Gen_Info");
    $geninfo.="</DIV>\n";
  }

  // If the General Block has information, include it.
  if ($genbody!="") {$description.=$genheader . $genbody . $divfooter;}

  // Builds the Programming Block.

  // If the programming information is available via the phase, include it.
  $progbody="";
  if ($phase_array['Prog Available'] == '0' ) {
    $progbody.="      <LI><A HREF=\"Postgrid.php?conid=$conid\">Schedule Grid</A></LI>\n";
    $progbody.="      <LI><A HREF=\"PubsSched.php?format=desc&conid=$conid\">Class Descriptions</A>\n";
    $progbody.="        <A HREF=\"PubsSched.php?format=desc&conid=$conid&short=Y\">(short)</A></LI>\n";
    $progbody.="      <LI><A HREF=\"PubsSched.php?format=sched&conid=$conid\">Schedule</A>\n";
    $progbody.="        <A HREF=\"PubsSched.php?format=sched&conid=$conid&short=Y\">(short)</A></LI>\n";
    $progbody.="      <LI><A HREF=\"PubsSched.php?format=tracks&conid=$conid\">Tracks</A>\n";
    $progbody.="        <A HREF=\"PubsSched.php?format=tracks&conid=$conid&short=Y\">(short)</A></LI>\n";
    $progbody.="      <LI><A HREF=\"PubsSched.php?format=trtime&conid=$conid\">Tracks by Time</A>\n";
    $progbody.="        <A HREF=\"PubsSched.php?format=trtime&conid=$conid&short=Y\">(short)</A></LI>\n";
    $progbody.="      <LI><A HREF=\"PubsSched.php?format=rooms&conid=$conid\">Rooms</A>\n";
    $progbody.="        <A HREF=\"PubsSched.php?format=rooms&conid=$conid&short=Y\">(short)</A></LI>\n";
    $progbody.="      <LI><A HREF=\"PubsBios.php?conid=$conid\">Presenter Bios</A>\n";
    $progbody.="        <A HREF=\"PubsBios.php?conid=$conid&short=Y\">(short)</A></LI>\n";
  }

  // If the brainstorm information is available via the phase, include it.
  if ($phase_array['Brainstorm'] == '0' ) {
    $progbody.="      <LI>\n";
    $progbody.="      <FORM name=\"brainstormform\" method=\"POST\" action=\"doLogin.php\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"target\" value=\"brainstorm\">\n";
    $progbody.="        <INPUT type=\"submit\" name=\"submit\" value=\"Class/Presenter Submission\">\n";
    $progbody.="      </FORM>\n";
    $progbody.="      <FORM name=\"brainstormviewform\" method=\"POST\" action=\"doLogin.php\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
    $progbody.="        <INPUT type=\"hidden\" name=\"target\" value=\"brainstorm\">\n";
    $progbody.="        <INPUT type=\"submit\" name=\"submit\" value=\"View Suggested Classes\">\n";
    $progbody.="      </FORM>\n";
    $progbody.="      </LI>\n";
  }

  // If the feedback information is available via the phase and the time, include it.
  if ($phase_array['Feedback Available'] == '0') {
    $progbody.="      <LI><A HREF=\"Feedback.php?conid=$conid\">Feedback</A></LI>\n";
  }

  // Switch the wording on the login, if the con has passed.
  if ($nowis < $constart) { 
    $progbody.="      <LI><A HREF=\"http://$conurl/webpages/login.php?newconid=$conid\">Presenter/Volunteer Login</A></LI>\n";
  } else {
    $progbody.="      <LI><A HREF=\"http://$conurl/webpages/login.php?newconid=$conid\">Presenter Login</A></LI>\n";
  }

  // If the Programming Block has information, include it.
  if ($progbody!="") {$description.=$progheader . $progbody . $divfooter;}

  // Builds the Volunteer Block if it is available via the phase.
  $volbody="";
  if ($phase_array['Vol Available'] == '0' ) {
    $volbody.="      <LI><A HREF=\"Postgrid.php?volunteer=y&conid=$conid\">Volunteer Grid</A></LI>\n";
    $volbody.="      <LI><A HREF=\"VolsSched.php?format=desc&conid=$conid\">Job Descriptions</A>\n";
    $volbody.="        <A HREF=\"VolsSched.php?format=desc&conid=$conid&short=Y\">(short)</A></LI>\n";
    $volbody.="      <LI><A HREF=\"VolsSched.php?format=sched&conid=$conid\">Schedule</A>\n";
    $volbody.="        <A HREF=\"VolsSched.php?format=sched&conid=$conid&short=Y\">(short)</A></LI>\n";
    $volbody.="      <LI><A HREF=\"VolsSched.php?format=rooms&conid=$conid\">Posts</A>\n";
    $volbody.="        <A HREF=\"VolsSched.php?format=rooms&conid=$conid&short=Y\">(short)</A></LI>\n";
  }

  // If the Volunteer Block has information, include it.
  if ($volbody!="") {$description.=$volheader . $volbody . $divfooter;}

  // Builds the Vendor Block.

  // If the vendor information is available by phase, include it.
  $vendbody="";
  if ($phase_array['Vendors Available'] == '0' ) {
    $vendbody.="      <LI><A HREF=\"Vendors.php?conid=$conid\">Vendor List</A></LI>\n";
  }

  // If the vendor submission is available by phase, include it.
  if ($phase_array['Vendor'] == '0' ) {
    $vendbody.="      <LI>\n";
    $vendbody.="      <FORM name=\"vendorform\" method=\"POST\" action=\"doLogin.php\">\n";
    $vendbody.="        <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
    $vendbody.="        <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
    $vendbody.="        <INPUT type=\"hidden\" name=\"target\" value=\"vendor\">\n";
    $vendbody.="        <INPUT type=\"submit\" name=\"submit\" value=\"New Vendor Application\">\n";
    $vendbody.="      </FORM>\n</LI>\n";
  }

  // If the Vendor Block has information, include it.
  if ($vendbody!="") {$description.=$vendheader . $vendbody . $divfooter;}

  // Cleanly close the description.
  $description.="</DIV>\n";

  // Return the information in a useful array.
  $returnarray['title']=$title;
  $returnarray['description']=$description;
  $returnarray['additionalinfo']=$additionalinfo;
  $returnarray['returnstring']=$geninfo . $conchairletter . $orgletter . $rules;
  return($returnarray);
}

/* This builds a report output, switched on if it is called from the
   API or called from within the program, on the conid, and on the
   reportname.  If no conid is passed, it pulls it from the sessionid.
*/
function GenPubReport($conid,$reportid) {
  require_once('PostingCommonCode.php');
  global $link;

  // Test for conid being passed in.
  if ($conid == "") {
    $conid=$_SESSION['conid'];
  }

  // Get the GOH list
  $query = <<<EOD
SELECT
    badgeid
  FROM
      UserHasConRole
    JOIN ConRoles USING (conroleid)
  WHERE
    conid=$conid AND
    conrolename like '%GOH%'
EOD;

  list($gohrows,$gohheader_array,$gohbadge_array)=queryreport($query,$link,$title,$description,0);
  $GohBadgeList="('";
  for ($i=1; $i<=$gohrows; $i++) {
    $GohBadgeList.=$gohbadge_array[$i]['badgeid']."', '";
  }
  $GohBadgeList.="')";

  // Set the con info from the conid.
  $query="SELECT conname,connumdays,congridspacer,constartdate,conlogo from ConInfo where conid=$conid";
  list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
  $conname=$conname_array[1]['conname'];
  $connumdays=$conname_array[1]['connumdays'];
  $Grid_Spacer=$conname_array[1]['congridspacer'];
  $constart=$conname_array[1]['constartdate'];
  $logo=$conname_array[1]['conlogo'];

  // Get the report
  $query = <<<EOD
SELECT
    reportname,
    reportid,
    reporttitle,
    reportdescription,
    reportadditionalinfo,
    reportrestrictions,
    reportquery
  FROM
      Reports
  WHERE
    reportid = $reportid
EOD;

  // Retrieve query
  list($returned_reports,$reportnumber_array,$report_array)=queryreport($query,$link,$title,$description,0);

  // There should only be one report returned, so only taking the first.

  /* Check for permissions - if there is an array of permissions set on the report
     walk through those permissions, to see if the person may see the report.  If
     not, set the query to a null value and return that instead. 
  */
  $empty_permissions_p=1;
  $allowed_p=0;
  if (!empty($report_array[1]['reportrestrictions'])) {
    $empty_permissions_p=0;
    $permissions_array=explode(',',$report_array[1]['reportrestrictions']);
    foreach ($permissions_array as $permission_check) {
      if (may_I($permission_check)) {
	$allowed_p++;
      }
    }
  }
  if (($empty_permissions_p==0) AND ($allowed_p==0)) {
    $report_array[1]['reportquery']="SELECT '' AS 'You do not have permission to view this table.  If you think this is an error, please contact a Zambia Administrator.'";
  }

  // Fix references in the string so variables can be substituted in.
  $query=eval("return<<<EOF\n".$report_array[1]['reportquery']."\nEOF;\n");
  $title=eval("return<<<EOF\n".$report_array[1]['reporttitle']."\nEOF;\n");
  $description=eval("return<<<EOF\n".$report_array[1]['reportdescription']."\nEOF;\n");
  $additionalinfo=eval("return<<<EOF\n".$report_array[1]['reportadditionalinfo']."\nEOF;\n");


  // Retrieve secondary query
  list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

  // Return the information in a useful array.
  $returnarray['title']=$title;
  $returnarray['description']=$description;
  $returnarray['additionalinfo']=$additionalinfo;
  $returnarray['elements']=$elements;
  $returnarray['header_array']=$header_array;
  $returnarray['element_array']=$element_array;
  return($returnarray);
}

/* This builds an array output, switched on format (but only vaguely,
   in terms of ordering and the like, all the information is delivered
   each time, to be dealt with by the calling program) and on the
   conid.
*/

function GenPubsSched($format,$conid) {
  require_once('PostingCommonCode.php');
  global $link;

  // Test for conid being passed in.
  if (($conid == "") or (!is_numeric($conid))) {
    $conid=$_SESSION['conid'];
  }

  // Test for format being passed in.
  if (($format == "") or
      (($format != "tracks") and
       ($format != "trtime") and
       ($format != "rooms") and
       ($format != "sched"))) {
    $format="desc";
  }

  // Additional info setup.
  $additionalinfo['bios']="http://$conurl/api/PubsBios/$conid";
  $additionalinfo['desc']="http://$conurl/api/PubsSched/desc/$conid";
  $additionalinfo['grid']="http://$conurl/webpages/Postgrid.php?conid=$conid";
  $additionalinfo['sched']="http://$conurl/api/PubsSched/sched/$conid";
  $additionalinfo['rooms'].="http://$conurl/api/PubsSched/rooms/$conid";
  $additionalinfo['tracks']="http://$conurl/api/PubsSched/tracks/$conid";
  $additionalinfo['trtime']="http://$conurl/api/PubsSched/trtime/$conid";

  // Set the conname from the conid
  $query="SELECT conname,connumdays,congridspacer,constartdate,conlogo,conurl from ConInfo where conid=$conid";
  list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
  $conname=$conname_array[1]['conname'];
  $connumdays=$conname_array[1]['connumdays'];
  $Grid_Spacer=$conname_array[1]['congridspacer'];
  $ConStart=$conname_array[1]['constartdate'];
  $logo=$conname_array[1]['conlogo'];
  $conurl=$conname_array[1]['conurl'];

  // LOCALIZATIONS
  if ($format == "tracks") {
    $title="Event Track Schedule for $conname by Name";
    $description="Track Schedules for all public sessions sorted by session name.";
    $orderby="trackname,title_good_web,starttime,R.roomname";
    $header_break="Track";
  }
  if ($format == "trtime") {
    $title="Event Track Schedule for $conname by Time";
    $description="Track Schedules for all public sessions sorted by starting time.";
    $orderby="trackname,starttime,title_good_web,R.roomname";
    $header_break="Track";
  }
  if ($format == "rooms") {
    $title="Event Room Schedule for $conname";
    $description="Room Schedules for all public sessions.";
    $orderby="R.Roomname,starttime,title_good_web";
    $header_break="Room";
  }
  if ($format == "desc") {
    $title="Session Descriptions for $conname";
    $description="Descriptions for all public sessions.";
    $orderby="title_good_web";
    $header_break="";
  }
  if ($format == "sched") {
    $title="Event Schedule for $conname";
    $description="Schedule for all public sessions.";
    $orderby="starttime,R.display_order,title_good_web";
    $header_break="Start Time";
  }

  /* This query grabs everything necessary for the schedule to be printed. */
  $query = <<<EOD
SELECT
    concat("http://",conurl,"/api/PubsSched/tracks/",conid,"#",trackname) AS Tracklink,
    trackname AS Track,
    if((DATE_ADD(constartdate,INTERVAL connumdays DAY)>NOW()),concat("http://",conurl,"/webpages/TrackScheduleIcal.php?trackid=",trackid),"") AS TrackIcallink,
    concat("http://",conurl,"/api/PubsSched/desc/",conid,"#",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web))) AS titlelink,
    concat(title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web))) AS Title,
    if ((pubsname is NULL), "", GROUP_CONCAT(DISTINCT "http://",conurl,"PubsBios/",conid,"#",pubsname SEPARATOR "::")) AS "Participantslink",
    if ((pubsname is NULL), "", GROUP_CONCAT(DISTINCT pubsname,if(moderator in ("1","Yes"),"(m)","") SEPARATOR "::")) AS "Participants",
    GROUP_CONCAT(DISTINCT "http://",conurl,"PubsSched/sched/",conid,"#",DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p") SEPARATOR "::") AS "Start Timelink",
    GROUP_CONCAT(DISTINCT DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p") SEPARATOR "::") AS "Start Time",
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    GROUP_CONCAT(DISTINCT "http://",conurl,"PubsSched/rooms/",conid,"#",roomname SEPARATOR "::") AS Roomlink,
    GROUP_CONCAT(DISTINCT roomname SEPARATOR "::") AS Room,
    if(DATE_ADD(constartdate,INTERVAL connumdays DAY) > NOW(),
      concat("http://",conurl,"/webpages/PrecisScheduleIcal.php?sessionid=",sessionid),
      "") AS iCal,
    if((constartdate < NOW() AND phasestate = "0"),
      concat("http://",conurl,"/webpages/Feedback.php?conid=",conid,"&sessionid=",sessionid),
      "") AS Feedback,
    if(desc_good_web IS NULL,"",desc_good_web) AS 'Description'
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks USING (trackid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
    JOIN Phase USING (conid)
    JOIN PhaseTypes USING (phasetypeid)
    LEFT JOIN ParticipantOnSession USING (sessionid,conid)
    LEFT JOIN Participants USING (badgeid)
    JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as title_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('title') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  descriptionlang='en-us') TGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as subtitle_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('subtitle') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
          descriptionlang='en-us') SGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as desc_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('description') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  descriptionlang='en-us') DGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as desc_good_book
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('description') AND
	  biostatename in ('good') AND
	  biodestname in ('book') AND
          descriptionlang='en-us') DGB USING (sessionid,conid)
  WHERE
    conid=$conid AND
    phasetypename like "%Feedback Available%" AND
    pubstatusname in ('Public') AND
    (volunteer IS NULL OR volunteer not in ('1','Yes')) AND
    (introducer IS NULL OR introducer not in ('1','Yes')) AND
    (aidedecamp IS NULL OR aidedecamp not in ('1','Yes'))
  GROUP BY
    sessionid
  ORDER BY
    $orderby
EOD;

  // Retrieve query
  list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

  // Return the information in a useful array.
  $returnarray['title']=$title;
  $returnarray['description']=$description;
  $returnarray['additionalinfo']=$additionalinfo;
  $returnarray['header_break']=$header_break;
  $returnarray['elements']=$elements;
  $returnarray['element_array']=$element_array;
  return($returnarray);
}


/* This builds an array output, switched on format (but only vaguely,
   in terms of ordering and the like, all the information is delivered
   each time, to be dealt with by the calling program) and on the
   conid.
*/

function VolsSchedString($format,$conid,$short) {
  require_once('PostingCommonCode.php');
  global $link;

  // Pass in variables
  if (($conid=="") or (!is_numeric($conid))) {
    $conid=$_SESSION['conid'];
  }

  // Test for format being passed in.
  if (($format == "") or
      (($format != "rooms") and
       ($format != "sched"))) {
    $format="desc";
  }

  $single_line_p="F";
  if ($short == "Y") {
    $single_line_p="T";
  } elseif ($short == "N") {
    $single_line_p="F";
  }

  // Set the conname from the conid
  $query="SELECT conname,connumdays,congridspacer,constartdate,conlogo from ConInfo where conid=$conid";
  list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
  $conname=$conname_array[1]['conname'];
  $connumdays=$conname_array[1]['connumdays'];
  $Grid_Spacer=$conname_array[1]['congridspacer'];
  $ConStart=$conname_array[1]['constartdate'];
  $logo=$conname_array[1]['conlogo'];

  // Check if feedback is allowed
  $feedback_p=false;
  $query="SELECT phasestate FROM PhaseTypes JOIN Phase USING (phasetypeid) WHERE phasetypename like '%Feedback Available%' AND conid=$conid";
  list($phasestatrows,$phaseheader_array,$phase_array)=queryreport($query,$link,$title,$description,0);
  if($phase_array[1]['phasestate'] == '0') {$feedback_p=true;}

  // Defaults
  $track='trackname AS Track';
  $sestitle='concat("<A HREF=\"VolsSched.php?format=desc&conid='.$conid.'#",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),"\">",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),"</A>") AS Title';
  $pubsname='if ((pubsname is NULL), " ", GROUP_CONCAT(DISTINCT pubsname,if(moderator in ("1","Yes"),"(m)","") SEPARATOR ", ")) AS "Participants"';
  $starttime='GROUP_CONCAT(DISTINCT "<A HREF=\"VolsSched.php?format=sched&conid='.$conid.'#",DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p"),"\"><i>",DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p"),"</i></A>" SEPARATOR ", ") AS "Start Time"';
  $room='GROUP_CONCAT(DISTINCT "<A HREF=\"VolsSched.php?format=rooms&conid='.$conid.'#",roomname,"\"><i>",roomname,"</i></A>" SEPARATOR ", ") AS Room';
  $estatten='"" AS Estatten';
  $pubstatus_check="'Volunteer','Reg Staff','Sales Staff'";
  $groupby="desc_good_web";

  // LOCALIZATIONS
  if ($format == "rooms") {
    $title="Volunteer Job Descriptions by location for $conname";
    $description="<P>Job Descriptions for all volunteer locations.</P>\n";
    $room='concat("<A NAME=\"",roomname,"\"></A>",roomname) AS Room';
    $orderby="R.Roomname,starttime,title_good_web";
    $header_break="Room";
  }
  if ($format == "desc") {
    $title="Volunteer Job Descriptions for $conname";
    $description="<P>Descriptions for all volunteer jobs.</P>\n";
    $sestitle='concat("<A NAME=\"",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web)),"\"></A>",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web))) AS Title';
    $orderby="title_good_web";
    $header_break="";
  }
  if ($format == "sched") {
    $title="Volunteer Schedule for $conname";
    $description="<P>Schedule for all volunteer sessions.</P>\n";
    $starttime='concat("<A NAME=\"",DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p"),"\"></A>",DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p")) AS "Start Time"';
    $orderby="starttime,R.display_order,title_good_web";
    $header_break="Start Time";
    $groupby="sessionid";
    $estatten='estatten AS Estatten';
  }

  // Additional info setup.
  $additionalinfo="<P>See also this ";
  if ($single_line_p=="T") {
    $additionalinfo.="<A HREF=\"VolsSched.php?format=$format&conid=$conid\">full</A>,\n";
  } else {
    $additionalinfo.="<A HREF=\"VolsSched.php?format=$format&conid=$conid&short=Y\">short</A>,\n";
  }
  if ($format != "desc") {
    $additionalinfo.="the <A HREF=\"VolsSched.php?format=desc&conid=$conid\">description</A>\n";
    $additionalinfo.="<A HREF=\"VolsSched.php?format=desc&conid=$conid&short=Y\">(short)</A>,\n";
  }
  if ($format != "sched") {
    $additionalinfo.="the <A HREF=\"VolsSched.php?format=sched&conid=$conid\">timeslots</A>\n";
    $additionalinfo.="<A HREF=\"VolsSched.php?format=sched&conid=$conid&short=Y\">(short)</A>,\n";
  }
  if ($format != "rooms") {
    $additionalinfo.="the <A HREF=\"VolsSched.php?format=rooms&conid=$conid\">locations</A>\n";
    $additionalinfo.="<A HREF=\"VolsSched.php?format=rooms&conid=$conid&short=Y\">(short)</A>,\n";
  }
  $additionalinfo.="or the <A HREF=\"Postgrid.php?volunteer=y&conid=$conid\">grid</A>.</P>\n";
  if ((strtotime($ConStart)+(60*60*24*$connumdays)) > time()) {
    $additionalinfo.="<P>Click on the ";
    $additionalinfo.="(iCal) tag to download the iCal calendar for the particular\n";
    $additionalinfo.="activity you want added to your calendar.</P>\n";
  }
  if ((strtotime($ConStart) < time()) AND ($phase_array[1]['phasestate'] == '0')) {
    $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
  }
  /* This query grabs everything necessary for the schedule to be printed. */
  $query = <<<EOD
SELECT
    $track,
    $sestitle,
    $pubsname,
    $starttime,
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat("<i>",date_format(duration,'%i'),'min</i>')
      WHEN MINUTE(duration)=0 THEN
        concat("<i>",date_format(duration,'%k'),'hr</i>')
      ELSE
        concat("<i>",date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min</i>')
      END AS Duration,
    $room,
    $estatten,
    if(DATE_ADD(constartdate,INTERVAL connumdays DAY) > NOW(),
      concat('<A HREF=PrecisScheduleIcal.php?sessionid=',sessionid,'>(iCal)</A>'),
      '') AS iCal,
    if((constartdate < NOW() AND phasestate = "0"),
      concat('<A HREF=Feedback.php?conid=$conid&sessionid=',sessionid,'>(Feedback)</A>'),
      '') AS Feedback,
    if(desc_good_web IS NULL,"",desc_good_web) AS 'Description'
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks USING (trackid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
    JOIN Phase USING (conid)
    JOIN PhaseTypes USING (phasetypeid)
    LEFT JOIN ParticipantOnSession USING (sessionid,conid)
    LEFT JOIN Participants USING (badgeid)
    JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as title_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('title') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  descriptionlang='en-us') TGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as subtitle_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('subtitle') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
          descriptionlang='en-us') SGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as desc_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('description') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  descriptionlang='en-us') DGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as desc_good_book
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('description') AND
	  biostatename in ('good') AND
	  biodestname in ('book') AND
          descriptionlang='en-us') DGB USING (sessionid,conid)
  WHERE
    conid=$conid AND
    phasetypename like "%Feedback Available%" AND
    pubstatusname in ($pubstatus_check) AND
    (volunteer IS NULL OR volunteer not in ('1','Yes')) AND
    (introducer IS NULL OR introducer not in ('1','Yes')) AND
    (aidedecamp IS NULL OR aidedecamp not in ('1','Yes'))
  GROUP BY
    $groupby
  ORDER BY
    $orderby
EOD;

  // Retrieve query
  list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

  // Return the information in a useful array.
  $returnarray['title']=$title;
  $returnarray['description']=$description;
  $returnarray['additionalinfo']=$additionalinfo;
  $returnarray['returnstring']=renderschedreport($format,$header_break,$single_line_p,$elements,$element_array);
  return($returnarray);
}

function GenPubsSchedII($format,$conid) {
  require_once('PostingCommonCode.php');
  global $link;

  // Test for conid being passed in.
  if (($conid == "") or (!is_numeric($conid))) {
    $conid=$_SESSION['conid'];
  }

  // Test for format being passed in.
  if (($format == "") or
      (($format != "tracks") and
       ($format != "trtime") and
       ($format != "rooms") and
       ($format != "bios") and
       ($format != "sched"))) {
    $format="desc";
  }

  // Additional info setup.
  $additionalinfo['bios']="http://$conurl/api/PubsScehd/bios/$conid";
  $additionalinfo['desc']="http://$conurl/api/PubsSched/desc/$conid";
  $additionalinfo['grid']="http://$conurl/webpages/Postgrid.php?conid=$conid";
  $additionalinfo['sched']="http://$conurl/api/PubsSched/sched/$conid";
  $additionalinfo['rooms'].="http://$conurl/api/PubsSched/rooms/$conid";
  $additionalinfo['tracks']="http://$conurl/api/PubsSched/tracks/$conid";
  $additionalinfo['trtime']="http://$conurl/api/PubsSched/trtime/$conid";

  // Set the conname from the conid
  $query="SELECT conname,connumdays,congridspacer,constartdate,conlogo,conurl from ConInfo where conid=$conid";
  list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
  $conname=$conname_array[1]['conname'];
  $connumdays=$conname_array[1]['connumdays'];
  $Grid_Spacer=$conname_array[1]['congridspacer'];
  $ConStart=$conname_array[1]['constartdate'];
  $logo=$conname_array[1]['conlogo'];
  $conurl=$conname_array[1]['conurl'];

  // LOCALIZATIONS
  if ($format == "tracks") {
    $title="Event Track Schedule for $conname by Name";
    $description="Track Schedules for all public sessions sorted by session name.";
    $orderby="trackname,title_good_web,starttime,R.roomname";
    $groupby="sessionid";
    $header_break="Track";
  }
  if ($format == "trtime") {
    $title="Event Track Schedule for $conname by Time";
    $description="Track Schedules for all public sessions sorted by starting time.";
    $orderby="trackname,starttime,title_good_web,R.roomname";
    $groupby="sessionid";
    $header_break="Track";
  }
  if ($format == "rooms") {
    $title="Event Room Schedule for $conname";
    $description="Room Schedules for all public sessions.";
    $orderby="R.Roomname,starttime,title_good_web";
    $groupby="sessionid";
    $header_break="Room";
  }
  if ($format == "desc") {
    $title="Session Descriptions for $conname";
    $description="Descriptions for all public sessions.";
    $orderby="title_good_web";
    $groupby="sessionid";
    $header_break="";
  }
  if ($format == "sched") {
    $title="Event Schedule for $conname";
    $description="Schedule for all public sessions.";
    $orderby="starttime,R.display_order,title_good_web";
    $groupby="sessionid";
    $header_break="Start Time";
  }
  if ($format == "bios") {
    $title="Biographical Information for $conname";
    $description="Biographical Information for all Presenters.";
    $orderby="pubsname,starttime";
    $groupby="ParticipantOnSession.ts";
    $header_break="Participants";
  }

  /* This query grabs everything necessary for the schedule to be printed. */
  $query = <<<EOD
SELECT
    concat("http://",conurl,"/api/PubsSched/tracks/",conid,"#",trackname) AS Tracklink,
    trackname AS Track,
    if((DATE_ADD(constartdate,INTERVAL connumdays DAY)>NOW()),concat("http://",conurl,"/webpages/TrackScheduleIcal.php?trackid=",trackid),"") AS TrackIcallink,
    concat("http://",conurl,"/api/PubsSched/desc/",conid,"#",title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web))) AS titlelink,
    concat(title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web))) AS Title,
    if ((webname is NULL), "", GROUP_CONCAT(DISTINCT "http://",conurl,"PubsSched/bios/",conid,"#",webname SEPARATOR "::")) AS "Participantslink",
    if ((webname is NULL), "", GROUP_CONCAT(DISTINCT webname,if(moderator in ("1","Yes"),"(m)","") SEPARATOR "::")) AS "Participants",
    if ((Bioset is NULL), "Bioset empty", GROUP_CONCAT(DISTINCT Bioset)) AS Bio,
    GROUP_CONCAT(DISTINCT "http://",conurl,"PubsSched/sched/",conid,"#",DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p") SEPARATOR "::") AS "Start Timelink",
    GROUP_CONCAT(DISTINCT DATE_FORMAT(ADDTIME(constartdate,starttime),"%a %l:%i %p") SEPARATOR "::") AS "Start Time",
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    GROUP_CONCAT(DISTINCT "http://",conurl,"PubsSched/rooms/",conid,"#",roomname SEPARATOR "::") AS Roomlink,
    GROUP_CONCAT(DISTINCT roomname SEPARATOR "::") AS Room,
    if(DATE_ADD(constartdate,INTERVAL connumdays DAY) > NOW(),
      concat("http://",conurl,"/webpages/PrecisScheduleIcal.php?sessionid=",sessionid),
      "") AS iCal,
    if((constartdate < NOW() AND phasestate = "0"),
      concat("http://",conurl,"/webpages/Feedback.php?conid=",conid,"&sessionid=",sessionid),
      "") AS Feedback,
    if(desc_good_web IS NULL,"",desc_good_web) AS 'Description'
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks USING (trackid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
    JOIN Phase USING (conid)
    JOIN PhaseTypes USING (phasetypeid)
    LEFT JOIN ParticipantOnSession USING (sessionid,conid)
    LEFT JOIN Participants USING (badgeid)
    JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as title_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('title') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  descriptionlang='en-us') TGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as subtitle_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('subtitle') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
          descriptionlang='en-us') SGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as desc_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('description') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  descriptionlang='en-us') DGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as desc_good_book
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('description') AND
	  biostatename in ('good') AND
	  biodestname in ('book') AND
          descriptionlang='en-us') DGB USING (sessionid,conid)
    LEFT JOIN (SELECT 
        DISTINCT badgeid,
        conid,
        webname,
        concat(if(picture IS NOT NULL,concat("<TABLE>\n  <TR>\n    <TD valign=\"top\" width=310><img width=300 src=\"",picture,"\"></TD>\n    <TD>"),""),
          if(bios IS NOT NULL,concat("<P>",bios,"</P>"),""),
          if(picture IS NOT NULL,concat("</TD>\n  </TR>\n</TABLE>"),"")) AS Bioset
       FROM
           ParticipantOnSession
           LEFT JOIN (SELECT
                   badgeid,
                   biotext as picture
                 FROM
                     Bios
                     JOIN BioTypes USING (biotypeid)
                     JOIN BioStates USING (biostateid)
                     JOIN BioDests USING (biodestid)
                 WHERE
                   biotypename in ('picture') AND
                   biostatename in ('edited') AND
                   biodestname in ('web') AND
                   biolang='en-us') PEW USING (badgeid)
           LEFT JOIN (SELECT
                   badgeid,
                   name as webname,
                   concat("<B>",name,"</B>",
                     if(biostring IS NOT NULL,biostring,""),
                     if(uri IS NOT NULL,concat("</P>\n<P>",uri),"")) AS bios
                 FROM
                     (SELECT
                        badgeid,
                        biotext AS name
                      FROM
                          Bios
                          JOIN BioTypes USING (biotypeid)
                          JOIN BioStates USING (biostateid)
                          JOIN BioDests USING (biodestid)
                      WHERE
                        biotypename in ('name') AND
                        biostatename in ('edited') AND
                        biodestname in ('web') AND
                        biolang='en-us') NAMEEW
                     LEFT JOIN (SELECT
                        badgeid,
                        biotext AS biostring
                      FROM
                          Bios
                          JOIN BioTypes USING (biotypeid)
                          JOIN BioStates USING (biostateid)
                          JOIN BioDests USING (biodestid)
                      WHERE
                        biotypename in ('bio') AND
                        biostatename in ('edited') AND
                        biodestname in ('web') AND
                        biolang='en-us') BEW USING (badgeid)
                     LEFT JOIN (SELECT
                        badgeid,
                        biotext AS uri
                      FROM
                          Bios
                          JOIN BioTypes USING (biotypeid)
                          JOIN BioStates USING (biostateid)
                          JOIN BioDests USING (biodestid)
                      WHERE
                        biotypename in ('uri') AND
                        biostatename in ('edited') AND
                        biodestname in ('web') AND
                        biolang='en-us') UEW USING (badgeid)) CEW USING (badgeid)) PART USING (badgeid,conid)
  WHERE
    conid=$conid AND
    phasetypename like "%Feedback Available%" AND
    pubstatusname in ('Public') AND
    (volunteer IS NULL OR volunteer not in ('1','Yes')) AND
    (introducer IS NULL OR introducer not in ('1','Yes')) AND
    (aidedecamp IS NULL OR aidedecamp not in ('1','Yes'))
  GROUP BY
    $groupby
  ORDER BY
    $orderby
EOD;

  // Retrieve query
  list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

  // Return the information in a useful array.
  $returnarray['title']=$title;
  $returnarray['description']=$description;
  $returnarray['additionalinfo']=$additionalinfo;
  $returnarray['header_break']=$header_break;
  $returnarray['elements']=$elements;
  $returnarray['element_array']=$element_array;
  return($returnarray);
}
?>
