<?php
require_once('StaffCommonCode.php');
global $link;

/* Sort variable goes here */
if ($_GET['sort']=="room") {
  $sort="room";
  $orderby="roomname,starttime";
} elseif ($_GET['sort']=="title") {
  $sort="title";
  $orderby="title";
} elseif ($_GET['sort']=="presenter") {
  $sort="presenter";
  $orderby="Presenter,starttime";
} else {
  $sort="time";
  $orderby="starttime,roomname";
}

// Make substitutable
$conid=$_GET['conid'];
if ($conid=="") {$conid=$_POST['conid'];}
if ($conid=="") {$conid=$_SESSION['conid'];}
$conidreturn="&conid=".$conid;

// LOCALIZATIONS
$_SESSION['return_to_page']="ProgVolSchedule.php?sort=$sort";
$title="Schedule the Programming Volunteers";
$description="<P>This is to help schedule all the programming volunteers.</P>\n";
$additionalinfo ="<P>This page is sortable, either pick ";
$additionalinfo.="<A HREF=ProgVolSchedule.php?sort=time$conidreturn>Start Time</A> or ";
$additionalinfo.="<A HREF=ProgVolSchedule.php?sort=room$conidreturn>Location</A> or ";
$additionalinfo.="<A HREF=ProgVolSchedule.php?sort=title$conidreturn>Title</A> or ";
$additionalinfo.="<A HREF=ProgVolSchedule.php?sort=presenter$conidreturn>Presenter</A> or ";
$additionalinfo.="click on the highlighted column headers.</P>\n";
$additionalinfo.="<P>Click on the title to assign/unassign someone.  Get a ";
$additionalinfo.="<A HREF=".$_SESSION['return_to_page']."&csv=y>csv</A> of this page.</P>\n";
$additionalinfo.="<P>Key:\n<TABLE>\n  <TR><TH>&nbsp</TH><TH>Value</TH></TR>\n";
$additionalinfo.="  <TR><TD><B>I</B></TD><TD>Introducer (assigned)</TD></TR>\n";
$additionalinfo.="  <TR><TD><B>V</B></TD><TD>Volunteer (assigned)</TD></TR>\n";
$additionalinfo.="  <TR><TD>#</TD><TD>Requested rank</TD></TR>\n";
$additionalinfo.="  <TR><TD>r</TD><TD>Requested</TD></TR>\n";
$additionalinfo.="  <TR><TD>*</TD><TD>Specific comment about schedule element</TD></TR>\n";
$additionalinfo.="  <TR><TD>a</TD><TD>Within Available time slot</TD></TR>\n";
$additionalinfo.="</TABLE>\n</P>";

$query=<<<EOD
SELECT
    DATE_FORMAT(ADDTIME(constartdate,starttime), "%a %l:%i") AS "<A HREF=ProgVolSchedule.php?sort=time$conidreturn>Start Time</A>",
    roomname AS "<A HREF=ProgVolSchedule.php?sort=room$conidreturn>Location</A>",
    sessionid,
    concat("<A HREF=StaffAssignParticipants.php?selsess=",sessionid,">",title,"</A>") AS "<A HREF=ProgVolSchedule.php?sort=title$conidreturn>Title</A>",
    presenter AS "<A HREF=ProgVolSchedule.php?sort=presenter$conidreturn>Presenter</A>"
  FROM
      Schedule
    JOIN Sessions USING (sessionid,conid)
    JOIN Types USING (typeid)
    JOIN Rooms USING (roomid)
    JOIN ConInfo USING (conid)
    JOIN (SELECT
          sessionid,
          conid,
          GROUP_CONCAT(badgename SEPARATOR ", ") AS presenter
        FROM
            ParticipantOnSession
          JOIN CongoDump USING (badgeid)
        WHERE
          introducer not in ('1','Yes') AND
          volunteer not in ('1','Yes') AND
          aidedecamp not in ('1','Yes')
        GROUP BY
          sessionid,
          conid) PRES USING (sessionid,conid)
  WHERE
    conid=$conid AND
    typename in ('Panel','Class','Author Reading')
  ORDER BY
    $orderby
EOD;

// Retrieve query
list($sessionsrows,$sessionsheader_array,$sessions_array)=queryreport($query,$link,$title,$description,0);

$query=<<<EOD
SELECT
    badgename,
    badgeid
  FROM
      CongoDump
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    permrolename in ('Programming','SuperProgramming') AND
    conid=$conid
  ORDER BY
    badgename
EOD;

// Retrieve query
list($volrows,$volheader_array,$vol_array)=queryreport($query,$link,$title,$description,0);

$query=<<<EOD
SELECT
    sessionid,
    badgename,
    concat(if(introducer in ('1','Yes'),"<B>I</B>",""),if(volunteer in ('1','Yes'),'<B>V</B>','')) AS asgn
  FROM
      ParticipantOnSession
    JOIN CongoDump USING (badgeid)
    JOIN UserHasPermissionRole USING (badgeid,conid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    permrolename in ('Programming','SuperProgramming') AND
    conid=$conid
EOD;

// Retrieve query
list($asgnrows,$asgnheader_array,$asgn_array)=queryreport($query,$link,$title,$description,0);

// Build the $check_array['sessionid']['badgename'] array
for ($m=1; $m<=$asgnrows; $m++) {
  $assigned_array[$asgn_array[$m]['sessionid']][$asgn_array[$m]['badgename']]=$asgn_array[$m]['asgn'];
}

$query=<<<EOD
SELECT
    sessionid,
    badgename,
    concat(if(rank!='NULL',rank,"r"),if((comments!='NULL' AND comments!=''),' *','')) AS rcom
  FROM
      ParticipantSessionInterest
    JOIN CongoDump USING (badgeid)
    JOIN UserHasPermissionRole USING (badgeid,conid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    permrolename in ('Programming','SuperProgramming') AND
    conid=$conid
EOD;

// Retrieve query
list($rcomrows,$rcomheader_array,$rcom_array)=queryreport($query,$link,$title,$description,0);

// Build the $check_array['sessionid']['badgename'] array
for ($k=1; $k<=$rcomrows; $k++) {
  $check_array[$rcom_array[$k]['sessionid']][$rcom_array[$k]['badgename']]=$rcom_array[$k]['rcom'];
}

$query=<<<EOD
SELECT
    sessionid,
    badgename,
  if((SCH.starttime > PAT.starttime AND ADDTIME(SCH.starttime,duration) < PAT.endtime),"a","") AS avail
  FROM
      Schedule SCH
    JOIN Sessions USING (sessionid,conid)
    JOIN ParticipantAvailabilityTimes PAT USING (conid)
    JOIN CongoDump USING (badgeid)
    JOIN UserHasPermissionRole USING (badgeid,conid)
    JOIN PermissionRoles USING (permroleid)
  WHERE 
    permrolename in ("Programming", "SuperProgramming") AND
    conid=$conid
EOD;

// Retrieve query
list($availrows,$availheader_array,$avail_array)=queryreport($query,$link,$title,$description,0);

// Build the $available_array['sessionid']['badgename'] array
for ($l=1; $l<=$availrows; $l++) {
  $available_array[$avail_array[$l]['sessionid']][$avail_array[$l]['badgename']]=$avail_array[$l]['avail'];
}


// Establish the extra rows in sessions
for ($i=1; $i<=$sessionsrows; $i++) {
  for ($j=1; $j<=$volrows; $j++) {
    if (!empty($assigned_array[$sessions_array[$i]['sessionid']][$vol_array[$j]['badgename']])) {
      $sessions_array[$i][$vol_array[$j]['badgename']]=$assigned_array[$sessions_array[$i]['sessionid']][$vol_array[$j]['badgename']];
    } elseif (!empty($check_array[$sessions_array[$i]['sessionid']][$vol_array[$j]['badgename']])) {
      $sessions_array[$i][$vol_array[$j]['badgename']]=$check_array[$sessions_array[$i]['sessionid']][$vol_array[$j]['badgename']];
    } elseif (!empty($available_array[$sessions_array[$i]['sessionid']][$vol_array[$j]['badgename']])) {
      $sessions_array[$i][$vol_array[$j]['badgename']]=$available_array[$sessions_array[$i]['sessionid']][$vol_array[$j]['badgename']];
    } else {
      $sessions_array[$i][$vol_array[$j]['badgename']]="";
    }
  }
}

// Establish the extra headers in sessions
for ($j=1; $j<=$volrows; $j++) {
  $sessionsheader_array[]=$vol_array[$j]['badgename'];
}

// Page Rendering
if ($_GET["csv"]=="y") {
  topofpagecsv("Schedule_Programming_Volunteers.csv");
  echo rendercsvreport(1,$sessionsrows,$sessionsheader_array,$sessions_array);
} else {
  topofpagereport($title,$description,$additionalinfo);
  echo renderhtmlreport(1,$sessionsrows,$sessionsheader_array,$sessions_array);
  correct_footer();
}
?>
