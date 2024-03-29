<?php
require_once('PostingCommonCode.php');
global $link;

// Pass in variables
if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}
if ($conid=="") {$conid=$_SESSION['conid'];}

// Set the conname from the conid
$conquery="SELECT conname,connamelong,connumdays,congridspacer,constartdate,conurl,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($conquery,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connamelong=$conname_array[1]['connamelong'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$ConStart=$conname_array[1]['constartdate'];
$conurl=$conname_array[1]['conurl'];
$logo=$conname_array[1]['conlogo'];

// Pretty con date
$constartdateraw = date_create($ConStart);
$conenddateraw = date_create($ConStart);
$delta=($connumdays - 1) . " days";
date_add($conenddateraw, date_interval_create_from_date_string($delta));
if ($connumdays < 2) {
  $condate=date_format($constartdateraw, 'l, F j, Y');
} else {
  $condate =date_format($constartdateraw, 'l - ');
  $condate.=date_format($conenddateraw, 'l, F ');
  $condate.=date_format($constartdateraw, 'j - ');
  $condate.=date_format($conenddateraw, 'j, Y');
}

// Set up the phase array
$query="SELECT phasestate,phasetypename FROM Phase JOIN PhaseTypes USING (phasetypeid) WHERE conid=$conid";
list($phaserows,$phaseheader_array,$fullphase_array)=queryreport($query,$link,$title,$description,0);
for ($i=1; $i<=$phaserows; $i++) {
  $phase_array[$fullphase_array[$i]['phasetypename']]=$fullphase_array[$i]['phasestate'];
}

// Set up the start/stop times for Applications
$queryapplications=<<<EOF
SELECT
    if((constartdate > NOW()),
       if((targettime < NOW()),
	  concat("        <DT>",activity," has already closed.</DT><br />\n"),
	  if((activitystart <= NOW()),
	     concat("        <DT><A HREF=\"",
		    activitynotes,
		    "\" target=\"_blank\">",
		    activity,
		    "</A>:</DT>\n        <DD>Opens ",
		    DATE_FORMAT(activitystart,'%b %D'),
		    "</DD>\n        <DD>Closes ",
		    DATE_FORMAT(targettime,'%b %D'),
		    "</DD><br />\n"),
	     concat("        <DT>",
		    activity,
		    ":</DT>\n        <DD>Opens ",
		    DATE_FORMAT(activitystart,'%b %D'),
		    "</DD>\n        <DD>Closes ",
		    DATE_FORMAT(targettime,'%b %D'),
		    "</DD><br />\n"))),"") AS Applications
  FROM
      TaskList
    JOIN ConInfo USING (conid)
  WHERE
    conid=$conid AND
    activity like "%Applications%"
EOF;

list($approws,$appheader_array,$app_array)=queryreport($queryapplications,$link,$title,$description,0);

// Create the application string

// Nulls to start with
$appstring="";
$tmpappstring="";

// Walk the possible activities
for ($i=1; $i<=$approws; $i++) {
  $tmpappstring.=$app_array[$i]['Applications'];
}

// If any of the activities came up, put them in the string.
if ($tmpappstring != "") {
  $appstring="      <DL>\n" . $tmpappstring . "     </DL>\n";
}

?>
<!DOCTYPE html>
<html><!-- manifest="konopas.appcache" -->
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="/konopas/skin/skin.css">
<meta name="HandheldFriendly" content="true">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Android + favicon -->
<meta name="mobile-web-app-capable" content="yes">
<link rel="shortcut icon" sizes="196x196" href="../Local/favicon.196.png">

<!-- iOS -->
<!--
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="apple-touch-icon"                 href="../Local/icon.apple.60.png">
<link rel="apple-touch-icon" sizes="76x76"   href="../Local/icon.apple.76.png">
<link rel="apple-touch-icon" sizes="120x120" href="../Local/icon.apple.120.png">
<link rel="apple-touch-icon" sizes="152x152" href="../Local/icon.apple.152.png">
<link rel="apple-touch-startup-image" href="../Local/start.apple.1536x2008.png" media="(device-width: 1536px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)">
<link rel="apple-touch-startup-image" href="../Local/start.apple.2048x1496.png" media="(device-width: 1536px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)">
<link rel="apple-touch-startup-image" href="../Local/start.apple.768x1004.png"  media="(device-width: 768px) and (orientation: portrait)">
<link rel="apple-touch-startup-image" href="../Loal/start.apple.1024x748.png"  media="(device-width: 768px) and (orientation: landscape)">
<link rel="apple-touch-startup-image" href="../Local/start.apple.640x920.png"   media="(device-width: 320px) and (-webkit-device-pixel-ratio: 2)">
<link rel="apple-touch-startup-image" href="../Local/start.apple.320x460.png"   media="(device-width: 320px)">
-->

<!-- Windows Phone 8.1 -->
<!--
<meta name="msapplication-TileColor"         content="#ffffff">
<meta name="msapplication-square70x70logo"   content="../Local/icon.ie.png">
<meta name="msapplication-square150x150logo" content="../Local/icon.ie.png">
<meta name="msapplication-square310x310logo" content="../Local/icon.ie.png">
-->

<title>KonOpus Instance for <?php echo $connamelong ?></title>

<body><div id="top"></div>
<div id="load_disable"></div>

<div id="time"></div>
<div id="scroll"><a href="#top" id="scroll_link" data-txt>Scroll up</a></div>

<div id="banner">
<div id="server_connect"></div>

<h1><a href="<?php echo $conurl ?>" target="_blank" alt="<?php echo $connamelong ?>" title="<?php echo $connamelong ?>">
<img id="title-small" src="../Local/logo.gif" style="width: 240px;">
<img id="title" src="../Local/logo.gif" style="width: 200px;">
<?php echo str_replace(" ", "&nbsp;", $conname) ?></a></h1>

<ul id="tabs">
<li id="tab_star"><a href="#star" data-txt>My con</a>
<li id="tab_prog"><a href="#" data-txt>Program</a><div id="day-sidebar" class="sub-side"></div>
<li id="tab_part"><a href="#part" data-txt>People</a><div id="part-sidebar" class="sub-side"></div>
<li id="tab_vend"><a href="#vend" data-txt>Vendors</a><div id="vend-sidebar" class="sub-side"></div>
<li id="tab_comm"><a href="#comm" data-txt>Community Tables</a><div id="comm-sidebar" class="sub-side"></div>
<li id="tab_info"><a href="#info" data-txt>Info</a>
</ul>

<div id="refresh"><i class="refresh_icon"></i><span data-txt>Refresh data</span></div>
</div>

<div id="main">

<div id="star_view" class="view">
	<div id="star_data"></div>
</div>

<div id="prog_view" class="view">
	<div id="prog_filters" class="filters">
		<div id="prog_lists"></div>
		<form name="search" id="search">
			<input type="text" id="q" required data-txt="Search" data-txt-attr="placeholder">
			<div id="q_ctrl">
			<input type="submit" id="q_submit" data-txt="Go" data-txt-attr="value">
			<input type="reset" id="q_clear" data-txt="Clear all" data-txt-attr="value">
			</div>
			<div id="q_hint" class="hint"></div>
		</form>
		<div id="filter_sum"></div>
	</div>
	<div id="day-narrow" class="sub-narrow"></div>
</div>

<div id="part_view" class="view">
	<div id="part-narrow" class="sub-narrow"></div>
	<ul id="part_names"></ul>
	<div id="part_info"></div>
</div>

<div id="vend_view" class="view">
	<div id="vend-narrow" class="sub-narrow"></div>
	<ul id="vend_names"></ul>
	<div id="vend_info"></div>
</div>

<div id="comm_view" class="view">
	<div id="comm-narrow" class="sub-narrow"></div>
	<ul id="comm_names"></ul>
	<div id="comm_info"></div>
</div>

<div id="info_view" class="view">
<p>This is the program guide for <?php echo "<A HREF=\"http://$conurl/webpages/GenInfo.php?conid=$conid\" target=\"_blank\">$connamelong</A>" ?>. It should be suitable for use on most browsers and devices. <?php echo "(<A HREF=\"http://$conurl/\" target=\"_blank\">other events</A>)" ?> It is an instance of <a href="http://konopas.org/" target="_blank">KonOpas</a>, an open-source project providing conventions with easy-to-use mobile-friendly guides.

<p><div id="last-updated">Program and participant data were last updated <span></span>.</div>

<div id="install-instructions">
<p>This guide will work in your browser even when you do not have an internet connection.
<h4 class="collapse">You can also install it as a home screen app</h4>
<dl class="compact-dl">
<dt>Safari on iPhone/iPad<dd>Tap on <i class="ios_share_icon"></i> (share), then tap <b>Add to Home Screen</b>.
<dt>Chrome on Android<dd>Tap on <i class="android_menu_icon"></i> (menu), then tap <b>Add to Home Screen</b>.
<dt>Firefox on Android<dd>Tap on <i class="android_menu_icon"></i> (menu), then tap <b>Bookmark</b> and select <b>Options</b>, and <b>Add to Home Screen</b>.
<dt>IE on Windows Phone<dd>Tap on <i class="ie_menu_icon"></i> (menu), then tap <b>Pin to Start</b>
</dl>
</div>
<script>
if (navigator.standalone) document.getElementById('install-instructions').style.display = 'none';
</script>

<?php
// Taken from GenInfo
$genbody ="  <DIV style=\"float: left;\">\n";
$genbody.="    <H2>$connamelong</H2>\n";
$genbody.="    <H2>$condate</H2>\n";
$genbody.="    <H3>General Information</H3>\n";
$genbody.="    <UL>\n";
if ($phase_array['OrgChart'] == '0' ) {
  $genbody.="      <LI class=collapse>Org Chart</LI>\n";
  $genbody.="      <DIV id=\"org_chart_div\" style=\"width:100%\"></DIV></LI>\n";
}
if ($phase_array['Grid Available'] == '0') {
  require_once("../Local/$conid/timeline.php");
  for ($graphrow=0; $graphrow<$graph_count; $graphrow++) {
    $genbody.="      <LI class=collapse>Grid for " . $graph_day[$graphrow] . "</LI>\n";
    $genbody.="      <DIV id=\"timeline".$graphrow."\" style=\"width:100%\"></DIV></LI>\n";
  }
}
if (file_exists("../Local/$conid/FAQ")) {
  $genbody.="      <LI class=collapse>FAQ</LI>\n";
  $genbody.=file_get_contents("../Local/$conid/FAQ");
}
if ($phase_array['Feedback Available'] == '0') {
  $genbody.="      <LI><A HREF=\"Feedback.php?conid=$conid\" target=\"_blank\">Feedback</A></LI>\n";
}
if ($phase_array['Comments Displayed'] == '0' ) {
  $genbody.="      <LI><A HREF=\"CuratedComments.php?conid=$conid\" target=\"_blank\">Comments about the event</A></LI>\n";
}
if (file_exists("../Local/$conid/Program_Book.pdf")) {
  $genbody.="      <LI><A HREF=\"../Local/$conid/Program_Book.pdf\" target=\"_blank\">Program Book</A></LI>\n";
}
$genbody.=$appstring;

/*
if ($phase_array['Brainstorm'] == '0' ) {
  $genbody.="      <LI><A HREF=\"BrainstormRedirectLogin.php?conid=$conid\" target=\"blank\">Class/Presenter Submission</A></LI>\n";
  $genbody.="      <LI><A HREF=\"BrainstormRedirectLogin.php?conid=$conid\" target=\"blank\">View Suggested Classes</A></LI>\n";
}
*/

if ($phase_array['Photo Submission'] == '0') {
  $progbody.="      <LI><A HREF=\"PhotoLoungeProposed.php\">Propose to Submit to the Photo Lounge</A></LI>\n";
}

if ($phase_array['Photo Submission'] == '0') {
  $genbody.="      <LI><A HREF=\"login.php?newconid=$conid\" target=\"blank\">Presenter/Photo Submissions/Volunteer Login</A></LI>\n";
} else {
  $genbody.="      <LI><A HREF=\"login.php?newconid=$conid\" target=\"blank\">Presenter/Volunteer Login</A></LI>\n";
}

$genbody.="    </UL>\n  </DIV>\n";

$venueinfo="";
if ($phase_array['Venue Available'] == '0' ) {
  $venueinfo.="  <DIV style=\" display: block; width: 100%; float: left; \">\n";
  $venueinfo.="    <A NAME=\"Venue\">&nbsp;</A>\n";
  $venueinfo.="    <H3>Venue Information</H3>\n";

  // Map of the venue (not to the venue)
  if (file_exists("../Local/$conid/Venue_Map.svg")) {
    $venueinfo.="      <LI class=collapse style=\"width:100%\">Map of the Venue</LI>\n";
    $venueinfo.=file_get_contents("../Local/$conid/Venue_Map.svg");
  }

  // Venue information (possibly including mapping instructions)
  if (file_exists("../Local/$conid/Venue_Info")) {
    $venueinfo.=file_get_contents("../Local/$conid/Venue_Info");
  }
  $venueinfo.="  </DIV>\n";
}

$vendorinfo="";
$vendorbodyinfo="";
if ($phase_array['Vendors Available'] == '0' ) {

  // SVG Map of the vendor layout
  if (file_exists("../Local/$conid/Vendor_Map.svg")) {
    $vendorbodyinfo.="      <LI class=collapse style=\"width:100%\">Map of the Vendor</LI>\n";
    $vendorbodyinfo.=file_get_contents("../Local/$conid/Vendor_Map.svg");
  }

  // PDF Map of the vendor layout
  if (file_exists("../Local/$conid/Vendor_Map.pdf")) {
    $vendorbodyinfo.="<A HREF=\"../Local/$conid/Vendor_Map.pdf\" target=\"_blank\">Click for the Map</A>\n";
  }

  // Vendor information
  if (file_exists("../Local/$conid/Vendor_List")) {
    $vendorbodyinfo.="      <LI class=collapse style=\"width:100%\">Vendor List</LI>\n";
    $vendorbodyinfo.=file_get_contents("../Local/$conid/Vendor_List");
  }

  // Check for empty
  if ($vendorbodyinfo != "") {
  $vendorinfo.="  <DIV style=\" display: block; width: 100%; float: left; \">\n";
  $vendorinfo.="    <A NAME=\"Vendors\">&nbsp;</A>\n";
  $vendorinfo.="    <H3>Vendors</H3>\n";
  $vendorinfo.=$vendorbodyinfo;
  $vendorinfo.="  </DIV>\n";
  }
}

$conchairletter="";
if (file_exists("../Local/$conid/Con_Chair_Welcome")) {
  $conchairletter.="  <DIV style=\" display: block; width: 100%; float: left; \">\n";
  $conchairletter.="    <A NAME=\"conchairletter\">&nbsp;</A>\n";
  $conchairletter.="    <H3 class=\"collapse\">A welcome letter from the Con Chair:</H3>\n";
  $conchairletter.=file_get_contents("../Local/$conid/Con_Chair_Welcome");
  $conchairletter.="  </DIV>\n";
}

$orgletter="";
if (file_exists("../Local/$conid/Org_Welcome")) {
  $orgletter.="  <DIV style=\" display: block; width: 100%; float: left; \">\n";
  $orgletter.="    <A NAME=\"orgletter\">&nbsp;</A>\n";
  $orgletter.="    <H3 class=\"collapse\">A welcome letter from the Organization:</H3>\n";
  $orgletter.=file_get_contents("../Local/$conid/Org_Welcome");
  $orgletter.="  </DIV>\n";
}

$rules="";
if (file_exists("../Local/$conid/KRules")) {
  $rules.="  <DIV style=\" display: block; width: 100%; float: left; \">\n";
  $rules.="    <A NAME=\"rules\">&nbsp;</A>\n";
  $rules.="    <HR>\n    <H3><center>Rules</center></H3>\n";
  $rules.=file_get_contents("../Local/$conid/KRules");
  $rules.="  </DIV>\n";
}

// Progbody taken out, with the exception of login and the grid elements, addeed above.

// Brainstorm commented out above until it can work.

// Photo Submission added above.

// Volunteer taken out.

// Vendor now part of the main app, but because not everything is entered in, $vendor_list exists

$geninfo="";
if (file_exists("../Local/$conid/Gen_Info")) {
  $geninfo.="<DIV style=\" display: block; width: 100%; float: left; \">\n";
  $geninfo.=file_get_contents("../Local/$conid/Gen_Info");
  $geninfo.="</DIV>\n";
}

// Now, put it all here.
echo $genbody;
echo $geninfo;
echo $venueinfo;
echo $vendorinfo;
echo $conchairletter;
echo $orgletter;
echo $rules;

?>

</div>


<div id="prog_ls" class="ls"><br><div class="ls_loading">Loading program data&hellip;</div></div>

</div><!-- /main -->

<script> var konopas_set = {
	'id': 'zambia',
	'default_duration': 90,
        'tag_categories': ['track', 'type'],
	'time_show_am_pm': true,
	'show_all_days_by_default': true,
	'non_ascii_people': true,
	'use_server': false,
	'filters': {
		'day': {},
		'area': {},
		'tag': {
		      'categories': ['track', 'type'],
		      'labels': {
		          'all_tags': 'All tracks & types',
		          'track': 'Track',
		          'type': 'Type'
		       }
		 }
	}
};
</script>

<!-- Google charts load script -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<!-- The org chart and timeline variables -->
<script src="../Local/<?php echo $conid ?>/orgchart.js"></script>
<script src="../Local/<?php echo $conid ?>/timeline.js"></script>

<!-- the Org Chart and Timeline script -->
<?php
if (($phase_array['OrgChart'] == '0' ) || ($phase_array['Grid Available'] == '0' )) {
?>
<script type="text/javascript">

  // Load the appropraite tools from google

<?php
  if (($phase_array['OrgChart'] == '0' ) && ($phase_array['Grid Available'] == '0' )) {
?>
  google.charts.load('current', {packages:['orgchart', 'timeline']});
<?php
  } elseif ($phase_array['OrgChart'] == '0' ) {
?>
  google.charts.load('current', {packages:['orgchart']});
<?php
  } else {
?>
  google.charts.load('current', {packages:['timeline']});
<?php
  }
?>
  google.charts.setOnLoadCallback(drawChart);

  // Draws the charts
  function drawChart() {

<?php
  if ($phase_array['OrgChart'] == '0' ) {
?>
    // Apply data from above orgchart.js file.
    var data = new google.visualization.DataTable(orgchartData);

    // Create the org chart.
    var chart = new google.visualization.OrgChart(document.getElementById('org_chart_div'));
    // Draw the org chart, setting the allowHtml option to true for the tooltips.
    chart.draw(data, {allowHtml:true, allowCollapse:true});

<?php
  }
  if ($phase_array['Grid Available'] == '0' ) {
?>
    // row hight of each row = 41
    var rowHeight = 46;
    var columnWidth = 75;

    // Loops across the number of charts to draw, chartnum from timeline.js
    var count;
    for(count = 0; count <= chartnum ; count++) {

      // Create each container
      var container = document.getElementById('timeline' + count);
      var chart = new google.visualization.Timeline(container);

      // Apply data from timeline.js
      var dataTable = new google.visualization.DataTable(tlname[count]);

      // Set the height and width of the chart
      var chartHeight = (tlheight[count] +1) * rowHeight;
      var chartWidth = tlwidth[count] * columnWidth;

      // Allows for html-style tooltips and makes everything a single color
      var options = {
        tooltip: {isHtml: true},
	  timeline: { singleColor: '#8d8' },
	  height : chartHeight,
          width: chartWidth,
	  forceIFrame: true
      };

      // Draws the chart, with the options set above.
      chart.draw(dataTable, options);
    }
<?php
  }
?>
  }
</script>
<?php
}

if ($phase_array['Prog Available'] == '0') {
   echo "<script src=\"../Local/$conid/program.js\"></script>\n";
   echo "<script src=\"../Local/$conid/people.js\"></script>\n";
}
if ($phase_array['Vendors Available'] == '0') {
   echo "<script src=\"../Local/$conid/vendor.js\"></script>\n";
   echo "<script src=\"../Local/$conid/community.js\"></script>\n";
}
?>
<script src="/konopas/konopas.min.js"></script>
<!-- <script src="/konopas/src/konopas.min.js"></script> -->
<!-- <script src="/konopas/src/konopas.js"></script> -->
