<html>
  <head>
    <script src="../Local/48/timeline.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['timeline']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var count;
        for(count = 0; count <= chartnum ; count++) {
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

$conid=48;

// Test for conid being passed in
if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}

if ($conid == "") {
  $conid=$_SESSION['conid'];
}

require_once("../Local/$conid/timeline.php");

for ($graphrow=0; $graphrow<$graph_count; $graphrow++) {
  echo "    <H2>" . $graph_day[$graphrow] . "</H2><br/>\n";
  echo "    <div id=\"timeline".$graphrow."\" style=\"height: 750px; width: " . $graph_slots[$graphrow]*75 . "px;\"></div>\n";
}
?>
  </body>
</html>