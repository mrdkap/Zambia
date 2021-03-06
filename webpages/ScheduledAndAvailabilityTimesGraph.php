<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
global $link;
$conid=$_SESSION['conid'];
$ConStart=$_SESSION['constartdate']; // make it a variable so it can be substituted
$ConNumDays=$_SESSION['connumdays']; // make it a variable so it can be substituted
$GridSpacer=$_SESSION['congridspacer']; // make it a variable so it can be substituted

/* If there is a passed in badgeid, use just that. */
if ((!empty($_GET['badgeid'])) and (is_numeric($_GET['badgeid']))) {
  $queryperson=$_GET['badgeid'];
}

/* Get all the Permission Roles */
$permrole_query = <<<EOD
SELECT
    permrolename,
    notes
  FROM
      PermissionRoles
  WHERE
    permroleid > 1
EOD;

list($permrole_rows,$permrole_header_array,$permrole_array)=queryreport($permrole_query,$link,"Broken Query",$permrole_query,0);

// Empty Title Switch to begin with.
$TitleSwitch="";

/* Attempt to establish default graph based on permissions */
for ($i=1; $i<=$permrole_rows; $i++) {
  if (may_I($permrole_array[$i]['permrolename'])) {
    $permrolecheck_array[]="'".$permrole_array[$i]['permrolename']."'";
    $TitleSwitch.=$permrole_array[$i]['notes']." ";
   }
 }
$permrolecheck_string=implode(",",$permrolecheck_array);


/* If Participant, or badgeid just return that one graph */
if ($_SESSION['role']=="Participant") {
  $wherestring="permrolename IN ($permrolecheck_string) AND badgeid=".$_SESSION['badgeid'];
} elseif (!empty($queryperson)) {
  $wherestring="permrolename IN ($permrolecheck_string) AND badgeid=$queryperson";
} else {
  $wherestring="permrolename IN ($permrolecheck_string)";
 }

// LOCALIZATIONS
$title="Availability and Scheduled times for $TitleSwitch";
$description="<P>The orange bars are what is scheduled.  The blue bars are what an individual reported as their availiability.</P>";
$additionalinfo="<P>Some useful reports:\n
<UL>\n
  <LI><A HREF=genreport.php?reportname=volstillclockedin>Volunteers Still Clocked In</A></LI>\n
  <LI><A HREF=genreport.php?reportname=progvolexpected>Programming Volunteers Expected To Be On</A></LI>\n
  <LI><A HREF=genreport.php?reportname=genvolexpected>General Volunteers Expected To Be On</A></LI>\n
  <LI><A HREF=genreport.php?reportname=scheduledtimesandclockedintime>Scheduled Times and Clocked in Times</A> (general) with the sub-reports:\n
  <UL>\n
    <LI><A HREF=genreport.php?reportid=196>Liaisons</A></LI>\n
    <LI><A HREF=genreport.php?reportid=197>Lounges</A></LI>\n
    <LI><A HREF=genreport.php?reportid=198>Registration</A></LI>\n
    <LI><A HREF=genreport.php?reportid=199>Logistics</A></LI>\n
    <LI><A HREF=genreport.php?reportid=200>General</A></LI>\n
    <LI><A HREF=genreport.php?reportid=211>Tabling</A></LI>\n
    <LI><A HREF=genreport.php?reportid=212>Events</A></LI>\n
    <LI><A HREF=genreport.php?reportid=213>Load in/Load Out</A></LI>\n
  </UL></LI>\n
</UL></P>\n";

/* ConStartSeconds is the seconds from the epoch that the con started
   from constartdate, for figuring out the times across the bottom
   of the grid.  ConEndSeconds is the total seconds across the entire
   con, added to ConStartSeconds which indicates where the grid is to
   stop. */

/* Pubstatus check gives the right subsets for the check */

if (may_I("SuperProgramming")) {$pubstatus_array[]="'Prog Staff','Public'";}
if (may_I("SuperGeneral")) {$pubstatus_array[]="'Volunteer'";}
if (may_I("SuperWatch")) {$pubstatus_array[]="'Watch Staff'";}
if (may_I("SuperRegistration")) {$pubstatus_array[]="'Reg Staff'";}
if (may_I("SuperVendor")) {$pubstatus_array[]="'Vendor Staff'";}
if (may_I("SuperEvents")) {$pubstatus_array[]="'Event Staff'";}
if (may_I("SuperLogistics")) {$pubstatus_array[]="'Logistics'";}
if (may_I("SuperSales")) {$pubstatus_array[]="'Sales Staff'";}
if (may_I("SuperFasttrack")) {$pubstatus_array[]="'Fast Track'";}
if (may_I("SuperLounge")) {$pubstatus_array[]="'Lounge Staff'";}
if (may_I("SuperCatering")) {$pubstatus_array[]="'Catering Staff'";}
if (may_I("SuperTabling")) {$pubstatus_array[]="'Tabling Staff'";}
if (isset($pubstatus_array)) {
  $pubstatus_string=implode(",",$pubstatus_array);
} else {
  $pubstatus_string="'Public'";
}

/* These queries finds the first and last second that is actually
   scheduled so we don't waste grid-space. */
$start_query = <<<EOD
SELECT
    TIME_TO_SEC(starttime) as 'beginschedule'
  FROM
      Schedule
    JOIN Sessions USING (sessionid,conid)
    JOIN PubStatuses USING (pubstatusid)
  WHERE
    conid=$conid AND
    pubstatusname in ($pubstatus_string)
  ORDER BY
    starttime ASC LIMIT 0,1
EOD;

list($earliest,$unneeded_array_c,$grid_start_sec_array)=queryreport($start_query,$link,$title,$description,0);
$StartSeconds=$grid_start_sec_array[1]['beginschedule'];
$ColumnAdjust=$StartSeconds/$GridSpacer;
$ConStartSeconds=strtotime($ConStart)+$StartSeconds;

$end_query = <<<EOD
SELECT
    (TIME_TO_SEC(starttime) + TIME_TO_SEC(duration)) as 'endschedule'
  FROM
      Schedule
    JOIN Sessions USING (sessionid,conid)
    JOIN PubStatuses USING (pubstatusid)
  WHERE
    conid=$conid AND
    pubstatusname in ($pubstatus_string)
  ORDER BY
    endschedule DESC
  LIMIT
    0,1
EOD;

list($latest,$unneeded_array_d,$grid_end_sec_array)=queryreport($end_query,$link,$title,$description,0);
$EndSeconds=$grid_end_sec_array[1]['endschedule'];
$ConEndSeconds=strtotime($ConStart)+$EndSeconds;

/* Build the dtime array.  This basically jumps the number of seconds
   in the congridspacer variable and uses that to generate the lables.
   At midnight switch to the dayname, and at noon, use noon. */

$tmp_sec=$ConStartSeconds;
$i=0;
while ($tmp_sec <= $ConEndSeconds) {
  if (date("H:i",$tmp_sec)=="00:00") {
    $dtime_array[$i]=date("D",$tmp_sec);
  } elseif (date("H:i",$tmp_sec)=="12:00") {
    $dtime_array[$i]="noon";
  } elseif (date("i",$tmp_sec) != "00") {
    $dtime_array[$i]=date(":i",$tmp_sec);
  } else {
    $dtime_array[$i]=date("g:i",$tmp_sec);
  }
  $i++;
  $tmp_sec+=$GridSpacer;
}

// The number of elements in dtime_array
$dtime_rows=count($dtime_array);

// Initialize and zeroize the badgeid_array and pubsname_array variables
$badgeid_array=array();
$pubsname_array=array();

/* Pull the badgeid, pubsname, Starttime, and Endtime from the
   participant's availability table in the database.  The Starttime is
   figured from the count of seconds for the starttime from the start
   of the con, divided by the congridspacer variable, so it matches the
   grids.  The Endtime is figured from the count of seconds for the
   endtime from the start of the con, divided by the congridspacer
   variable, so it matches the grids. */

$avail_query = <<<EOD
SELECT
    badgeid,
    pubsname,
    (TIME_TO_SEC(starttime) DIV $GridSpacer) - $ColumnAdjust AS Starttime,
    (TIME_TO_SEC(endtime) DIV $GridSpacer) - $ColumnAdjust AS Endtime
  FROM
      ParticipantAvailabilityTimes
    JOIN Participants USING (badgeid)
    JOIN UserHasPermissionRole USING (badgeid,conid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    permrolename in ('Programming') AND
    conid=$conid
  ORDER BY
    pubsname
EOD;

list($avail_rows,$avail_header_array,$avail_array)=queryreport($avail_query,$link,$title,$description,0);

/* Create the badgeid and pubsname for those that don't yet exist, so,
   when walking the data, the appropriate information can be displayed
   next to the correct name. */

for ($i=1; $i<=$avail_rows; $i++) {
  if (! in_array($avail_array[$i]['badgeid'], $badgeid_array)) {
    $badgeid_array[]=$avail_array[$i]['badgeid'];
    $pubsname_array[]=$avail_array[$i]['pubsname'];
  }
}

/* Pull the badgeid, pubsname, sessionid, Starttime, and Endtime from
   the participant being on a session in the database.  The Starttime
   is figured from the count of seconds for the starttime from the
   start of the con, divided by the congridspacer variable, so it
   matches the grids.  The Endtime is figured from the count of
   seconds for the starttime from the start of the con, divided by the
   congridspacer variable, added to the count of seconds for the
   duration, divided by the congridspacer variable, so it matches the
   grids. */

$sched_query = <<<EOD
SELECT
    badgeid,
    pubsname,
    sessionid,
    title,
    (TIME_TO_SEC(starttime) DIV $GridSpacer) - $ColumnAdjust AS Starttime,
    (TIME_TO_SEC(starttime) DIV $GridSpacer )+(TIME_TO_SEC(duration) DIV $GridSpacer) - $ColumnAdjust AS Endtime
  FROM
      ParticipantOnSession
    JOIN Participants USING (badgeid)
    JOIN Sessions USING (sessionid,conid)
    JOIN Schedule USING (sessionid,conid)
  WHERE
    conid=$conid AND
    (introducer in ('0','1','Yes') OR
     volunteer in ('0','1','Yes'))
EOD;

list($sched_rows,$sched_header_array,$sched_array)=queryreport($sched_query,$link,$title,$description,0);

/* Create the badgeid and pubsname for those that don't yet exist, so,
   when walking the data, the appropriate information can be displayed
   next to the correct name. */

for ($i=1; $i<=$sched_rows; $i++) {
  if (! in_array($sched_array[$i]['badgeid'], $badgeid_array)) {
    $badgeid_array[]=$sched_array[$i]['badgeid'];
    $pubsname_array[]=$sched_array[$i]['pubsname'];
  }
}

// The number of elements in badgeid_array (and pubsname_array)
$badgeid_rows=count($badgeid_array);

// Figure out the number of characters in the longest name.
$longestname=0;
for ($i=0; $i<$badgeid_rows; $i++) {
  if (strlen($pubsname_array[$i]) > $longestname) {
    $longestname=strlen($pubsname_array[$i]);
  }
}

// Invert the badgeid_array to number mapping
$nums_array=array_flip($badgeid_array);

// The font size for the labels
$fontsize=12;

// Should become programmatic, somehow off of the fontsize.
$fontwidth=$fontsize*.6;

// The size of each of the grid-boxes
$xoffset=$fontsize*3;
$yoffset=$fontsize*3;

/* x is the space at the right, allow for 6 characters, y is the space
   at the top visually one character height. */

$xpad=$fontwidth*4;
$ypad=$fontsize;

/* x starts after the names determined by the fontwidth times the
   number of characters in the longest name plus the same whitespace
   as on the right, y is above the numbers, two character
   heights. */

$xstart=($fontwidth*$longestname)+$xpad;
$yend=$fontsize*2;

/* Printing body.  Uses the page-init then creates the page. */
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

/* The height is figured from the height above the numbers plus the
   height of the padding at the top, plus the number of people times
   the yoffset.  The width is figured from the width of the names
   plus the padding at the right, plus the number of time-elements to
   be shown minus one (because the time elements are on the lines,
   whereas the people elements are between the lines) times the
   yoffset. */

$height=($yend+$ypad+($yoffset*$badgeid_rows));
$heightpx=$height."px";
$width=($xstart+$xpad+($xoffset*($dtime_rows-1)));
$widthpx=$width."px";

echo "<DIV style=\"height:400px;width:$widthpx;overflow:scroll;\">\n";
?>

<svg
   xmlns:xlink="http://www.w3.org/1999/xlink"
   xmlns="http://www.w3.org/2000/svg"
   viewbox="0 0 <?php echo $width; ?> <?php echo $height; ?>"
   preserveAspectRatio="xMinYMin meet"
   style="overflow:auto"
   version="1.1"
   onload="init(evt)">
   <title><?php echo $title; ?></title>
<defs>

<?php /* Background as grey */ ?>
<clipPath id="canvas"><rect width="100%" height="100%" fill="#eee" stroke-width="0px"/>
</clipPath>

<?php
  /* Make the bars pretty.  The gradient0 is red/yellow, the gradient1
     is blue/white, the gradient2 is green/yellow.  Using gradient0
     for scheduled and gradient2 for clocked in times. */
?>
<linearGradient id="gradient0" x1="0" x2="0" y1="0" y2="100%"><stop offset="0%" stop-color="red"/>
<stop offset="100%" stop-color="yellow"/>
</linearGradient>
<linearGradient id="gradient1" x1="0" x2="0" y1="0" y2="100%"><stop offset="0%" stop-color="blue"/>
<stop offset="100%" stop-color="white"/>
</linearGradient>
<linearGradient id="gradient2" x1="0" x2="0" y1="0" y2="100%"><stop offset="0%" stop-color="green"/>
<stop offset="100%" stop-color="yellow"/>
</linearGradient>
</defs>

<?php
  /* This is the script that does the mouseover stuff.  Yes, I really
     don't like it being here, but, currently there is no other way to
     do it, and the information is too dense, otherwise.  This starts
     with creating the init(evt) function, then has the two
     ShowToolTip elements, Upper and Lower for each of the two bars,
     so that the labels end up in about the same place.  Then there
     is the simple HideTooltip piece to make it go away. */
?>

<script type="text/ecmascript">
  <![CDATA[

    function init(evt) {
        if ( window.svgDocument == null ) {
        // Define SGV
        svgDocument = evt.target.ownerDocument;
        }
        tooltip = svgDocument.getElementById('tooltip');
    }

    function ShowTooltipUpper(evt) {
        // Put tooltip in the right position, change the text and make it visible
        var placex = parseFloat(evt.target.getAttributeNS(null,"x"));
        var placey = parseFloat(evt.target.getAttributeNS(null,"y"));
        tooltip.setAttributeNS(null,"x",placex-2);
        tooltip.setAttributeNS(null,"y",placey+<?php echo (($yoffset*2)/3); ?>);
        tooltip.firstChild.data = evt.target.getAttributeNS(null,"mouseovertext");
        tooltip.setAttributeNS(null,"visibility","visible");
    }

    function ShowTooltipLower(evt) {
        // Put tooltip in the right position, change the text and make it visible
        var placex = parseFloat(evt.target.getAttributeNS(null,"x"));
        var placey = parseFloat(evt.target.getAttributeNS(null,"y"));
        tooltip.setAttributeNS(null,"x",placex-2);
        tooltip.setAttributeNS(null,"y",placey+<?php echo $yoffset/3; ?>);
        tooltip.firstChild.data = evt.target.getAttributeNS(null,"mouseovertext");
        tooltip.setAttributeNS(null,"visibility","visible");
    }

    function HideTooltip(evt) {
        tooltip.setAttributeNS(null,"visibility","hidden");
    }
    ]]></script>

<?php
  /* I don't know why this is done twice, but for now, I'm leaving
     it. */
?>
<g clip-path="url(#canvas)">
  <rect width="100%" height="100%" fill="#eee" stroke-width="0px"/>

<?php
  /* Draws the grid.
     The horizontal lines bracket the people, so we need one more line
     than we have values, so we count 0 through badgeid_rows, although the
     bottommost line should overlap with the darker line from below.
     Since the vertical lines are on the time-values (centered), we go
     from the xstart (plus 0) through the xstart + one less than
     dtime_rows (as if we started counting from 0 rather than 1) that
     gives us exactly as many lines as we have values, again with the
     leftmost line overlapping with the darker line from below. */

echo '<path d="';
for ($i=0; $i<=$badgeid_rows; $i++) {
  echo 'M',$xstart,' ',($ypad+($yoffset*$i)),' L',$width-$xpad,' ',($ypad+($yoffset*$i)),' ';
}
echo '
';
for ($i=0; $i<$dtime_rows; $i++) {
  echo 'M',($xstart+($xoffset*$i)),' ',$height-$yend,' L',($xstart+($xoffset*$i)),' ',$ypad,' ';
}
echo '" stroke="#666"/>
';

/* These are each of the bars, height should be a fixed value
   (currently 1/3 the yoffset), the y should be determined by the
   yoffset to seperate out the people from each other the first one
   starting at 1/6th down, the second at the halfway point, leaving a
   blank 1/6th above and below, the x should be their start time, the
   width should be how long they are available for, the id should just
   be generated, and the style has gradient1 for the availability and
   gradient0 as the scheduled. */

for ($i=1; $i<=$avail_rows; $i++) {
  echo '<rect height="',($yoffset/3);
  echo '" y="',($ypad+($yoffset/6)+($yoffset*$nums_array[$avail_array[$i]['badgeid']]));
  echo '" x="',($xstart+($avail_array[$i]['Starttime']*$xoffset));
  echo '" width="',($xoffset*($avail_array[$i]['Endtime']-$avail_array[$i]['Starttime']));
  echo '" id="barelement',$i;
  echo '" onmousemove="ShowTooltipUpper(evt)" onmouseout="HideTooltip(evt)" mouseovertext="',htmlspecialchars($avail_array[$i]['pubsname']);
  echo '" style="stroke:#000;stroke-width:1px;fill:url(#gradient1);"/>
';
}

for ($i=1; $i<=$sched_rows; $i++) {
  echo '<a xlink:href="StaffAssignParticipants.php?selsess=',$sched_array[$i]['sessionid'],'" target="_blank">';
  echo '<rect height="',($yoffset/3);
  echo '" y="',($ypad+($yoffset/2)+($yoffset*$nums_array[$sched_array[$i]['badgeid']]));
  echo '" x="',($xstart+($sched_array[$i]['Starttime']*$xoffset));
  echo '" width="',($xoffset*($sched_array[$i]['Endtime']-$sched_array[$i]['Starttime']));
  echo '" id="barelement',$i;
  echo '" onmousemove="ShowTooltipLower(evt)" onmouseout="HideTooltip(evt)" mouseovertext="',htmlspecialchars($sched_array[$i]['title']),' - ',htmlspecialchars($sched_array[$i]['pubsname']);
  echo '" style="stroke:#000;stroke-width:1px;fill:url(#gradient0);"/></a>
';
}

/* Dark edge bars between labels and graph */
echo '  <g stroke-width="2px" stroke="#333">
';
echo '    <line x1="',$xstart-2,'" x2="',$width-$xpad+2,'" y2="',$height-$yend,'" y1="',$height-$yend,'"/>
';
echo '    <line x1="',$xstart,'" x2="',$xstart,'" y2="',$height-$yend+2,'" y1="',$ypad-2,'"/>
';
echo '  </g>
';

/* Labels.
   These are calculated via offest, the first set the y should be
   calculated from the total height minus the y padding and the x
   should be the xoffset apart, starting with the xstart.  The second
   set should have a fixed x, just short of the xstart and the y
   should be adjusted off of the ypad by the yoffset steps separating
   the names.  There is a little more complexity to get them centered
   on their rows, for otherwise they just line up with the lines.  By
   bringing it half an offset down, they are vaguely centered.
   Technically they should also be brought half a letter-height down
   as well, but that gets a little too complex for this. */

echo '  <g font-size="',$fontsize,'px" font-family="Georgia" fill="#000">
';
echo '    <g text-anchor="middle">
';

for ($i=0; $i<$dtime_rows; $i++) {
  echo '      <text x="'.($xstart+($xoffset*$i)).'" y="'.($height-$ypad).'">'.$dtime_array[$i].'</text>
';
}
echo '    </g>
';
echo '    <g text-anchor="end">
';
for ($i=0; $i<$badgeid_rows; $i++) {
  echo '      <text x="'.($xstart-2).'" y="'.($ypad+($yoffset/2)+($yoffset*$i)).'">'.htmlspecialchars($pubsname_array[$i]).'</text>
';
}

?>
    </g>
  </g>
</g>

<?php /* The necessary bit to get the Tooltip to work */ ?>
<g font-size = "<?php echo $fontsize; ?>px" font-family="Georgia" fill="#000">
  <g text-anchor="end">
    <text id="tooltip" x="0" y="0" visibility="hidden">Tooltip</text>
  </g>
</g>
</svg>

</DIV>

<?php
$height=$fontsize*2;
$viewheight=$fontsize*3;
$viewheightpx=$viewheight."px";
echo "<DIV style=\"height:$viewheightpx;width:$widthpx;overflow:scroll;\">\n";
?>

<svg
   xmlns:xlink="http://www.w3.org/1999/xlink"
   xmlns="http://www.w3.org/2000/svg"
   viewbox="0 0 <?php echo $width; ?> <?php echo $viewheight; ?>"
   preserveAspectRatio="none"
   style="overflow-x:auto"
   version="1.1"
   onload="init(evt)">
   <title>Times</title>
<defs>

<?php /* Background as grey */ ?>
<clipPath id="canvas"><rect width="100%" height="100%" fill="#eee" stroke-width="0px"/>
</clipPath>

</defs>

<?php
  /* I don't know why this is done twice, but for now, I'm leaving
     it. */
?>
<g clip-path="url(#canvas)">
  <rect width="100%" height="100%" fill="#eee" stroke-width="0px"/>

<?php
echo '  <g font-size="',$fontsize,'px" font-family="Georgia" fill="#000">
';
echo '    <g text-anchor="middle">
';

for ($i=0; $i<$dtime_rows; $i++) {
  echo '      <text x="'.($xstart+($xoffset*$i)).'" y="'.($height-$ypad).'" font-size="'.$fontsize.'px">'.$dtime_array[$i].'</text>
';
}
?>
      </g>
    </g>
  </g>
</svg>

</DIV>

<?php

correct_footer();
?>
