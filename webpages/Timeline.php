<?php
// set some random default
$conid=48;

// Test for conid being passed in
if ((!empty($_GET['conid'])) AND (is_numeric($_GET['conid']))) {
  $conid=$_GET['conid'];
}
?>
<html>
  <head>
    <script src="../Local/<?php echo $conid ?>/timeline.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      // Load the appropraite tools from google
      google.charts.load('current', {'packages':['timeline']});
      google.charts.setOnLoadCallback(drawChart);

      // Draws the charts
      function drawChart() {

        // row hight of each row
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
          var chartHeight = (tlheight[count] + 1) * rowHeight;
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
  </head>
  <body>
<?php

require_once("../Local/$conid/timeline.php");

for ($graphrow=0; $graphrow<$graph_count; $graphrow++) {
  echo "    <H2 class=collapse>" . $graph_day[$graphrow] . "</H2><br/>\n";
  echo "    <div id=\"timeline".$graphrow."\"></div>\n";
}
?>
  </body>
</html>
