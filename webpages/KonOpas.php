<?php
require_once('PostingCommonCode.php');
global $link;

// Pass in variables
if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}
if ($conid=="") {$conid=$_SESSION['conid'];}

// Set the conname from the conid
$conquery="SELECT conname,connumdays,congridspacer,constartdate,conurl,conlogo from ConInfo where conid=$conid";
list($connamerows,$connameheader_array,$conname_array)=queryreport($conquery,$link,$title,$description,0);
$conname=$conname_array[1]['conname'];
$connumdays=$conname_array[1]['connumdays'];
$Grid_Spacer=$conname_array[1]['congridspacer'];
$ConStart=$conname_array[1]['constartdate'];
$conurl=$conname_array[1]['conurl'];
$logo=$conname_array[1]['conlogo'];

// Set up the phase array
$query="SELECT phasestate,phasetypename FROM Phase JOIN PhaseTypes USING (phasetypeid) WHERE conid=$conid";
list($phaserows,$phaseheader_array,$fullphase_array)=queryreport($query,$link,$title,$description,0);
for ($i=1; $i<=$phaserows; $i++) {
  $phase_array[$fullphase_array[$i]['phasetypename']]=$fullphase_array[$i]['phasestate'];
}

?>
<!DOCTYPE html>
<html><!-- manifest="konopas.appcache" -->
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="../../konopas/skin/skin.css">
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

<title>KonOpus Instance for <?php echo $conname ?></title>

<body><div id="top"></div>
<div id="load_disable"></div>

<div id="time"></div>
<div id="scroll"><a href="#top" id="scroll_link" data-txt>Scroll up</a></div>

<div id="banner">
<div id="server_connect"></div>

<h1><a href="#info" alt="<?php echo $conname ?>" title="<?php echo $conname ?>">
<img id="title-small" src="../Local/logo.gif" style="width: 240px;">
<img id="title" src="../Local/logo.gif" style="width: 200px;"></a></h1>

<ul id="tabs">
<li id="tab_star"><a href="#star" data-txt>My con</a>
<li id="tab_prog"><a href="#" data-txt>Program</a><div id="day-sidebar" class="sub-side"></div>
<li id="tab_part"><a href="#part" data-txt>People</a><div id="part-sidebar" class="sub-side"></div>
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

<div id="info_view" class="view">
<p>This is the programme guide for <?php echo "<A HREF=\"http://$conurl/webpages/GenInfo.php?conid=$conid\">$conname</A>" ?>. It should be suitable for use on most browsers and devices. It is an instance of <a href="http://konopas.org/">KonOpas</a>, an open-source project providing conventions with easy-to-use mobile-friendly guides.

<p><div id="last-updated">Programme and participant data were last updated <span></span>.</div>

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

<style>
#map { width: 100% !important; }
#map td { text-align: center !important; padding: 6px 32px; }
#map a, #map span { font-family: 'Oswald'; font-weight: 400; font-size: 1.2em; cursor: pointer; }
#map a:hover, #map span:hover { text-decoration: underline; }
#map tr:hover, .quick-ref tr:hover { background: inherit !important; }
.quick-ref dt { margin: 6px 0 3px; }
.quick-ref table { width: auto !important; border-spacing: 0; margin-bottom: 6px; }
.quick-ref td { text-align: left !important; }
#info_view .quick-ref td.c { text-align: center !important; }
.quick-ref td:nth-child(2) { text-align: right !important; padding: 0 0 0 5px; }
.quick-ref td:nth-child(3) { padding-left: 0; }
</style>

<!-- SAMPLE SECTIONS FOR "INFO" VIEW, UNCOMMENT TO SEE
<h2>Sample Maps</h2>
<table id="map">
<tr><td><a class="popup-link" href="http://guide.2014.arisia.org/data/arisia-westin-3W.png">Mezzanine Level&nbsp;(3W)</a>
    <td><a class="popup-link" href="http://guide.2014.arisia.org/data/arisia-westin-3E.png">Conference Level&nbsp;(3E)</a>
<tr><td colspan="2"><a class="popup-link" href="http://guide.2014.arisia.org/data/arisia-westin-2.png">Lobby Level&nbsp;(2)</a>
<tr><td><a class="popup-link" href="http://guide.2014.arisia.org/data/arisia-westin-1W.png">Concourse Level&nbsp;(1W)</a>
    <td><a class="popup-link" href="http://guide.2014.arisia.org/data/arisia-westin-1E.png">Galleria Level&nbsp;(1E)</a>
</table>
-->

<?php
// Taken from GenInfo
$genbody ="  <DIV style=\"float: left;\">\n";
$genbody.="    <H3>General Information</H3>\n";
$genbody.="    <UL>\n";
$genbody.="      <LI class=collapse>Org Chart</LI>\n";
$genbody.="      <DIV id=\"org_chart_div\" style=\"width:100%\"></DIV></LI>\n";
if ($phase_array['Prog Available'] == '0' ) {
  require_once("../Local/$conid/timeline.php");
  for ($graphrow=0; $graphrow<$graph_count; $graphrow++) {
    $genbody.="      <LI class=collapse>" . $graph_day[$graphrow] . "</LI>\n";
    $genbody.="      <DIV id=\"timeline".$graphrow."\" style=\"width:100%\"></DIV></LI>\n";
  }
}
if ($phase_array['Comments Displayed'] == '0' ) {
  $genbody.="      <LI><A HREF=\"CuratedComments.php?conid=$conid\">Comments about the event</A></LI>\n";
}
if (file_exists("../Local/$conid/Program_Book.pdf")) {
  $genbody.="      <LI><A HREF=\"../Local/$conid/Program_Book.pdf\">Program Book</A></LI>\n";
}
if ($phase_array['Vendors Available'] == '0' ) {
  $genbody.="      <LI><A HREF=\"Vendors.php?conid=$conid\">Vendor List</A></LI>\n";
}
$genbody.="      <LI><A HREF=\"login.php?newconid=$conid\">Presenter/Volunteer Login</A></LI>\n";
$genbody.="    </UL>\n  </DIV>\n";

$venueinfo="";
if ($phase_array['Venue Available'] == '0' ) {
  $venueinfo.="  <DIV style=\" display: block; width: 100%; float: left; \">\n";
  $venueinfo.="    <A NAME=\"Venue\">&nbsp;</A>\n";
  $venueinfo.="    <H3>Venu Information</H3>\n";

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

$conchairletter="";
if (file_exists("../Local/$conid/Con_Chair_Welcome")) {
  $conchairletter.="  <DIV style=\" display: block; width: 100%; float: left; \">\n";
  $conchairletter.="    <A NAME=\"conchairletter\">&nbsp;</A>\n";
  $conchairletter.="    <H3>A welcome letter from the Con Chair:</H3>\n";
  $conchairletter.=file_get_contents("../Local/$conid/Con_Chair_Welcome");
  $conchairletter.="  </DIV>\n";
}

$orgletter="";
if (file_exists("../Local/$conid/Org_Welcome")) {
  $orgletter.="  <DIV style=\" display: block; width: 100%; float: left; \">\n";
  $orgletter.="    <A NAME=\"orgletter\">&nbsp;</A>\n";
  $orgletter.="    <H3>A welcome letter from the Organization:</H3>\n";
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

// Progbody, with the exception of login and the grid elements, addeed above.

// Brainstorm taken out.

// Photo Submission taken out.

// Feedback taken out.

// Volunteer taken out.

// Vendor taken out except for vendor list added above.

$geninfo="";
if (file_exists("../Local/$conid/Gen_Info")) {
  $geninfo.="<DIV class=\"collapse\" style=\" display: block; width: 100%; float: left; \">\n";
  $geninfo.=file_get_contents("../Local/$conid/Gen_Info");
  $geninfo.="</DIV>\n";
}

// Now, put it all here.
echo $genbody;
echo $geninfo;
echo $venueinfo;
echo $conchairletter;
echo $orgletter;
echo $rules;

?>

<!-- SAMPLE SECTIONS FOR "INFO" VIEW, UNCOMMENT TO SEE
<h2>Sample Quick Reference</h2>
<dl class="quick-ref">
<dt><b>Access/Handicapped Services</b>: see Info Desk near elevators

<dt><b>Art Show</b>: Harbor Ballroom II/III (3E)
<dd><table>
<tr><td>Friday<td>6pm–<td>9pm
<tr><td>Saturday<td>10am–<td>6pm, 8pm–10pm
<tr><td>Sunday<td>10am–<td>1:30pm
</table>
<b>Sales Pickup / Artist Checkout</b>: Sunday 3:30pm–7:30pm<br>
<b>Voice Auction</b>: Sunday 4:30pm<br>
<b>Sales Pickup / Artist Checkout</b>: Monday 10am–1pm

<dt><b>...</b>
</dl>
-->

</div>


<div id="prog_ls" class="ls"><br><div class="ls_loading">Loading programme data&hellip;</div></div>

</div><!-- /main -->

<script> var konopas_set = {
	'id': 'zambia',
	'default_duration': 90,
        'tag_categories': ['track', 'type'],
	'time_show_am_pm': true,
	'show_all_days_by_default': true,
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
<script type="text/javascript">

  // Load the appropraite tools from google
  google.charts.load('current', {packages:['orgchart', 'timeline']});
  google.charts.setOnLoadCallback(drawChart);

  // Draws the charts
  function drawChart() {

    // Apply data from above orgchart.js file.
    var data = new google.visualization.DataTable(orgchartData);

    // Create the org chart.
    var chart = new google.visualization.OrgChart(document.getElementById('org_chart_div'));
    // Draw the org chart, setting the allowHtml option to true for the tooltips.
    chart.draw(data, {allowHtml:true, allowCollapse:true});

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
  }
</script>

<script src="../Local/<?php echo $conid ?>/program.js"></script>
<script src="../Local/<?php echo $conid ?>/people.js"></script>
<script src="../../konopas/konopas.min.js"></script>
