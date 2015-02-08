<?php
require_once('StaffCommonCode.php');
global $link;
$_SESSION['return_to_page']="manualGRIDS.php";
$title="All Grids";
$description="<P>All the grids are listed below, in the grid. Or you can try your <A HREF=grid.php>default grid</A>.</P>\n";
$additionalinfo="<P>The type of grid is listed as the headers and the area of interest it pertains to is listed down the side.\n";
$additionalinfo.="The choice of color or not is inside each grid element.</P>\n";

$additionalinfo.="<P>Also useful is the\n";
$additionalinfo.="<A HREF=\"StaffBios.php?conid=$conid\">bios of the people involved with their schdule</A>\n";
$additionalinfo.="<A HREF=\"StaffBios.php?short=Y&conid=$conid\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffBios.php?pic_p=N&conid=$conid\">(without images)</A>,\n";

$additionalinfo.="the <A HREF=\"StaffSched.php?format=desc&conid=$conid\">descriptions of scheduled precis</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=desc&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=desc&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
$additionalinfo.="the <A HREF=\"StaffSched.php?format=sched&conid=$conid\">schedule precis</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=sched&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=sched&conid=$conid&feedback=Y\">(w/feedback)</A> in time order, the\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=tracks&conid=$conid\">tracks sorted by name</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=tracks&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=tracks&conid=$conid&feedback=Y\">(w/feedback)</A>, the\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=trtime&conid=$conid\">tracks sorted by start time</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=trtime&conid=$conid&short=Y\">(short)</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=trtime&conid=$conid&feedback=Y\">(w/feedback)</A>,\n";
$additionalinfo.="and the <A HREF=\"StaffSched.php?format=rooms&conid=$conid\">room's schedule</A>\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=rooms&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="<A HREF=\"StaffSched.php?format=rooms&conid=$conid&feedback=Y\">(w/feedback)</A>.</P>\n";

$additionalinfo.="<P>There is also the public version of all of these:\n";
$additionalinfo.="the <A HREF=\"PubsBios.php?conid=$conid\">bios</A>\n";
$additionalinfo.="<A HREF=\"PubsBios.php?short=Y&conid=$conid\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=desc&conid=$conid\">description</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=desc&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=sched&conid=$conid\">schedule</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=sched&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=tracks&conid=$conid\">tracks</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=tracks&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=trtime&conid=$conid\">tracks by time</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=trtime&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=rooms&conid=$conid\">rooms</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=rooms&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"VolsSched.php?format=desc&conid=$conid\">volunteer description</A>\n";
$additionalinfo.="<A HREF=\"VolsSched.php?format=desc&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"VolsSched.php?format=sched&conid=$conid\">volunteer schedule</A>\n";
$additionalinfo.="<A HREF=\"VolsSched.php?format=sched&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"VolsSched.php?format=rooms&conid=$conid\">volunteer locations</A>\n";
$additionalinfo.="<A HREF=\"VolsSched.php?format=rooms&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="and the <A HREF=Postgrid.php>public version</A> of the Grids.</P>\n";

$additionalinfo.="<P>And there is the book versions of all of these, as well:\n";
$additionalinfo.="the <A HREF=\"BookBios.php\">bios</A>\n";
$additionalinfo.="<A HREF=\"BookBios.php?pic_p=N\">(without images)</A>\n";
$additionalinfo.="<A HREF=\"BookBios.php?short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"BookSched.php?format=desc\">description</A>\n";
$additionalinfo.="<A HREF=\"BookSched.php?format=desc&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"BookSched.php?format=sched\">schedule</A>\n";
$additionalinfo.="<A HREF=\"BookSched.php?format=sched&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"BookSched.php?format=tracks\">tracks</A>\n";
$additionalinfo.="<A HREF=\"BookSched.php?format=tracks&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"BookSched.php?format=trtime\">tracks by time</A>\n";
$additionalinfo.="<A HREF=\"BookSched.php?format=trtime&short=Y\">(short)</A>,\n";
$additionalinfo.="and the <A HREF=\"BookSched.php?format=rooms\">rooms</A>\n";
$additionalinfo.="<A HREF=\"BookSched.php?format=rooms&short=Y\">(short)</A>.</P>\n";

// Replacement for the query
$how_array['Description']="";
$how_array['Start Time']="starttime=y";
$how_array['Start Time<br>Unabridged']="starttime=y&unpublished=y";
$how_array['Start Time<br>Staff Only']="starttime=y&staffonly=y";
$how_array['Time Filled']="timefilled=y";
$how_array['Time Filled<br>Unabridged']="timefilled=y&unpublished=y";
$how_array['Time Filled<br>Staff Only']="timefilled=y&staffonly=y";
$how_array['Time Semi-filled']="standard=y";
$how_array['Time Semi-filled<br>Unabridged']="unpublished=y";
$how_array['Time Semi-filled<br>Staff Only']="staffonly=y";

$type_array['Complete']="standard=y&";
$type_array['Fast Track']="fasttrack=y&";
$type_array['Event']="events=y&";
$type_array['GoH']="goh=y&";
$type_array['Programming']="programming=y&";
$type_array['Volunteer']="volunteer=y&";
$type_array['Registration']="registration=y&";
$type_array['Sales']="sales=y&";
$type_array['Vending']="vending=y&";
$type_array['Watch']="watch=y&";
$type_array['Logistics/Tech']="logistics=y&";
$type_array['Lounges']="lounge=y&";

//build the returned array
$header_array=array_keys($how_array);
$body_array=array_keys($type_array);
$rows=0;
foreach ($body_array as $y_element) {
  $rows++;
  foreach ($header_array as $x_element) {
    if ($x_element == "Description") {
      $grid_array[$rows]["$x_element"]="<B>".$y_element."</B>";
    } else {
      $grid_array[$rows]["$x_element"]="<A HREF=grid.php?".$type_array["$y_element"].$how_array["$x_element"].">Color</A> \n";
      $grid_array[$rows]["$x_element"].="<A HREF=grid.php?".$type_array["$y_element"].$how_array["$x_element"]."&track=y>(by track)</A> / \n";
      $grid_array[$rows]["$x_element"].="<A HREF=grid.php?".$type_array["$y_element"].$how_array["$x_element"]."&nocolor=y>No Color</A>\n";
    }
  }
}

// Page Rendering
topofpagereport($title,$description,$additionalinfo);
echo renderhtmlreport(1,$rows,$header_array,$grid_array);
correct_footer();
?>
