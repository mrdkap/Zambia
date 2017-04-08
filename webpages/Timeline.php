<?php
require_once('PostingCommonCode.php');
global $link; 
$title="Sessions Grid";
$pagetitle=$title;
?>
<html>
  <head>
    <script src="../Local/48/timeline.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['timeline']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
<?php

// Test for conid being passed in
if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}

if ($conid == "") {
  $conid=$_SESSION['conid'];
}

// Set the conname etc. from the conid
$query="SELECT conname,constartdate,connumdays,congridspacer,conurl,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($query,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$constartdate=$conname_array[1]['constartdate'];
$connumdays=$conname_array[1]['connumdays'];
$conurl=$conname_array[1]['conurl'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$logo=$conname_array[1]['conlogo'];

// Establish the start and stop of the various grids.

// Start time at zero hour.
$grid_start_sec=0;

// End time is a day past the number of days, just in case things go past midnight.
$grid_end_sec=($connumdays + 1)*86400;

// This query pulls all the distinct start and end times.  There is probably a computationally
// less intense way to do this, but ... this works, and it's not that hard.
$gridtimequery = <<<EOF
SELECT
  DISTINCT concat("('",TIME_TO_SEC(starttime), "','", TIME_TO_SEC(ADDTIME(starttime,duration))+congridspacer+congridspacer,"')") AS timing,
  TIME_TO_SEC(starttime) AS "Start",
  TIME_TO_SEC(ADDTIME(starttime,duration))+congridspacer+congridspacer AS "End"
  FROM
      Schedule
    JOIN Sessions USING (sessionid,conid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
  WHERE
    conid=$conid AND
    pubstatusname in ('Public')
  ORDER BY
 starttime
EOF;

// Retrieve the time signatures
list($gridtimerows,$gridtimeheader_array,$gridtime_array)=queryreport($gridtimequery,$link,$title,$description,0);

// Find all the times that there should be grid elements.

// In or out of a grid state
$in_p=0;

// The count of the grids
$graph_count=0;

// This walks all the possible grid break points, and checks to see if they are durring any
// class element (with a windage of two slots after) and then notes where the graphs should be.
for ($time=$grid_start_sec; $time<=$grid_end_sec; $time = $time + $Grid_Spacer) {
  $timeslot[$time]=0;
  for ($i=1; $i<=$gridtimerows; $i++) {
    $starttime=$gridtime_array[$i]['Start'];
    $stoptime=$gridtime_array[$i]['End'];
    if ($time >= $starttime && $time <= $stoptime) {
      $timeslot[$time]++;
    }
  }
  if (($timeslot[$time] > 0) && ($in_p == 0)) {
    $graph_start[$graph_count]=$time;
    $in_p=1;
    $dateinsec = strtotime($constartdate);
    $newdate=$dateinsec+$time;
    $graph_day[$graph_count]=date("l", $newdate);
  } elseif (($timeslot[$time] == 0) && ($in_p == 1)) {
    $graph_end[$graph_count]=$time-$Grid_Spacer;
    $graph_slots[$graph_count]=($graph_end[$graph_count]-$graph_start[$graph_count])/$Grid_Spacer;
    $in_p=0;
    $graph_count++;
  }
}

// This walks each of the appropriate graphs.
$tlname="        var tlname = [";
for ($graphrow=0; $graphrow<$graph_count; $graphrow++) {
  $graphstarttime=$graph_start[$graphrow];
  $graphendtime=$graph_end[$graphrow];

// Get the roomname, the title and presenters, the descripton, the starttime, the endtime.
  $timelinequery = <<<EOD
SELECT
    roomname AS "Where",
    replace(title_good_web,'"',"''") AS "What",
    GROUP_CONCAT(name_good_web,if((moderator in ('1','Yes')),'(m)','') SEPARATOR ", ") AS "Who",
    DATE_FORMAT(ADDTIME(constartdate, starttime),"Date(%Y, %m, %d, %k, %i, %s)") AS "Start Time",
    DATE_FORMAT(ADDTIME(ADDTIME(constartdate, starttime),duration),"Date(%Y, %m, %d, %k, %i, %s)") AS "End Time"
  FROM
      Sessions
    JOIN Schedule USING (sessionid, conid)
    JOIN Rooms USING (roomid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ParticipantOnSession USING (sessionid,conid)
    JOIN ConInfo USING (conid)
    JOIN (SELECT
        badgeid,
	biotext AS name_good_web
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('name') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  biolang='en-us') NGW USING (badgeid)
         JOIN (SELECT
        sessionid,
	conid,
	descriptiontext AS title_good_web
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
  WHERE
    conid=$conid AND
    TIME_TO_SEC(starttime) >= $graphstarttime AND
    TIME_TO_SEC(starttime) <= $graphendtime AND
    pubstatusname in ('Public') AND
    volunteer not in ("1","Yes") AND
    introducer not in ("1","Yes") AND
    aidedecamp not in ("1","Yes")
  GROUP BY
    sessionid
EOD;

  // Retrieve query
  list($timelinerows,$timelineheader_array,$timeline_array)=queryreport($timelinequery,$link,$title,$description,0);

  // Walk the rows and build the array
  $timelinedata_array=array();
  for ($i=1; $i<=$timelinerows; $i++) {
    $timelinedata_array[$i]="\t{\"c\":[{\"v\":\"".$timeline_array[$i]['Where']."\"},";
    $timelinedata_array[$i].="{\"v\":\"".$timeline_array[$i]['What']."\"},";
    $timelinedata_array[$i].="{\"v\":\"".$timeline_array[$i]['Who']."\"},";
    $timelinedata_array[$i].="{\"v\":\"".$timeline_array[$i]['Start Time']."\"},";
    $timelinedata_array[$i].="{\"v\":\"".$timeline_array[$i]['End Time']."\"}]}";
  } 

  // Build the string ... header first

  $timelinestring="  var timelineData" . $graphrow . " = {\n";
  $timelinestring.=<<<EOD
    "cols" :[
	{"type":"string","id":"Room"},
	{"type":"string","id":"Name"},
	{"type":"string","role":"tooltip","p":{"role":"tooltip"}},
	{"type":"date","id":"Start"},
	{"type":"date","id":"End"}
    ],
    "rows":[
EOD;

  // ... then the chewy array center
  $timelinestring.=implode(",\n",$timelinedata_array);

  // Followed by the close string
  $timelinestring.="\n    ]\n};\n";

  // produce the timelinestring for the javascript to use.
  echo $timelinestring;

$tlname.="timelineData" . $graphrow . ", ";
} // for ($graphrow=1; $graphrow<$graph_count; $graphrow++)

$tlname.="];";
$chartnum="        var chartnum = ". ($graphrow - 1) .";\n";
echo "\n$chartnum\n$tlname\n";

?>
        var count;
        for(count = 0; count <=chartnum ; count++) {
            var container = document.getElementById('timeline' + count);
	    var chart = new google.visualization.Timeline(container);
	    var dataTable = new google.visualization.DataTable(tlname[count]);
	
	    var options = {
	        tooltip: {isHtml: true},
	        timeline: { singleColor: '#8d8' }
	    };

	    chart.draw(dataTable, options);
	}
      }
    </script>
  </head>
  <body>
<?php
for ($graphrow=0; $graphrow<$graph_count; $graphrow++) {
  echo "    <H2>" . $graph_day[$graphrow] . "</H2><br/>\n";
  echo "    <div id=\"timeline".$graphrow."\" style=\"height: 750px; width: " . $graph_slots[$graphrow]*75 . "px;\"></div>\n";
}
?>
  </body>
</html>
