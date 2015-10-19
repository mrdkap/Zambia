<?php
require_once('StaffCommonCode.php');
require_once('SubmitMaintainRoom.php');
global $daymap;
$conid=$_SESSION['conid']; // make it a variable so it can be substituted
$newroomslots=$_SESSION['newroomslots']; // make it a variable so it can be substituted

$title="Maintain Room Schedule";

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

echo "<P>newroomslots=$newroomslots</P>\n";
$topsectiononly=true; // no room selected -- flag indicates to display only the top section of the page

if (isset($_POST["numrows"])) {
  $ignore_conflicts=(isset($_POST['override']))?true:false;
  if(!SubmitMaintainRoom($ignore_conflicts)) $conflict=true;
}

if (isset($_POST["selroom"])) { // room was selected by this form
  $selroomid=$_POST["selroom"];
  $topsectiononly=false;
  //unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
} elseif (isset($_GET["selroom"])) { // room was select by external page such as a report
  $selroomid=$_GET["selroom"];
  $topsectiononly=false;
} else {
  $selroomid=0; // room was not yet selected.
  unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
}

if ($conflict!=true) {
  $query="SELECT roomid, concat(roomname,'(',function,')') as Room FROM Rooms ORDER BY display_order";
?>
<FORM name="selroomform" method=POST action="MaintainRoomSched.php">
  <DIV>
    <LABEL for="selroom">Select Room</LABEL>
    <SELECT name="selroom">
      <?php populate_select_from_query($query, $selroomid, "Select Room", true); ?>
    </SELECT>
  </DIV>
<BR><P>For any session where you are rescheduling, please read the Notes for Programming Committee.</P>
  <DIV class="SubmitDiv">
    <?php if (isset($_SESSION['return_to_page'])) { ?>
    <A HREF="<?php echo $_SESSION['return_to_page'];?>">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>
    <?php } ?>
    <BUTTON type="submit" name="submit" class="SubmitButton">Submit</BUTTON>
  </DIV>
</FORM>
<HR>
<?php
  // unset all stuff from posts so input fields get reset to blank
  for ($i=1;$i<=$newroomslots;$i++) {
    unset($_POST["day$i"]);
    unset($_POST["hour$i"]);
    unset($_POST["min$i"]);
    unset($_POST["ampm$i"]);
    unset($_POST["sess$i"]);
  }
}
if ($topsectiononly) {
  correct_footer();
  exit();
}
echo "<FORM name=\"rmschdform\" method=POST action=\"MaintainRoomSched.php\">\n";

// Override button
if ($conflict==true) {
	echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"override\" class=\"SubmitButton\">Save Anyway!</BUTTON></DIV>\n";
	echo "<BR><HR>\n";
	}

// Get the room's physicial characteristics, if they exist, including open and close times.
$query = <<<EOD
SELECT roomid, roomname, opentime1, closetime1, opentime2, closetime2, opentime3, closetime3,
function, floor, height, dimensions, area, notes FROM Rooms WHERE roomid=$selroomid
EOD;
if (!$result=mysql_query($query,$link)) {
  $message=$query."<BR>Error querying database. Unable to continue.<BR>";
  RenderError($title,$message);
  exit();
}
echo "<H2>$selroomid - ".htmlspecialchars(mysql_result($result,0,"roomname"))."</H2>";
echo "<H4>Open Times</H4>\n";
echo "<DIV class=\"border1111 lrpad lrmargin\"><P class=\"lrmargin\">";
if (mysql_result($result,0,"opentime1")!="") {
    echo time_description(mysql_result($result,0,"opentime1"))." through ".time_description(mysql_result($result,0,"closetime1"))."<BR>\n";
    }
if (mysql_result($result,0,"opentime2")!="") {
    echo time_description(mysql_result($result,0,"opentime2"))." through ".time_description(mysql_result($result,0,"closetime2"))."<BR>\n";
    }
if (mysql_result($result,0,"opentime3")!="") {
    echo time_description(mysql_result($result,0,"opentime3"))." through ".time_description(mysql_result($result,0,"closetime3"))."<BR>\n";
    }
echo "</DIV>\n";
echo "<H4>Characteristics</H4>\n";
echo "   <TABLE class=\"border1111=\">\n";
echo "      <TR>\n";
echo "         <TH class=\"lrpad border1111\">Function</TH>\n";
echo "         <TH class=\"lrpad border1111\">Floor</TH>\n";
echo "         <TH class=\"lrpad border1111\">Dimensions</TH>\n";
echo "         <TH class=\"lrpad border1111\">Area</TH>\n";
echo "         <TH class=\"lrpad border1111\">Height</TH>\n";
echo "         </TR>\n";
echo "      <TR>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"function"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"floor"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"dimensions"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"area"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"height"))."</TD>\n";
echo "         </TR>\n";
echo "      <TR>\n";
echo "         <TD colspan=5 class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"notes"))."</TD>\n";
echo "         </TR>\n";
echo "      </TABLE>\n";
echo "<H4>Room Sets</H4>\n";

// Get the Room Sets possible and the Capacity.
$query = <<<EOD
SELECT roomsetname, capacity FROM RoomSets JOIN RoomHasSet USING (roomsetid) WHERE roomid=$selroomid
EOD;
if (!$result=mysql_query($query,$link)) {
  $message=$query."<BR>Error querying database. Unable to continue.<BR>";
  RenderError($title,$message);
  exit();
}

$i=1;
while ($bigarray[$i] = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $i++;
    }
$numrows=$i;
echo "   <TABLE class=\"border1111=\">\n";
echo "      <TR>\n";
echo "         <TH class=\"lrpad border1111\">Room Set</TH>\n";
echo "         <TH class=\"lrpad border1111\">Capacity</TH>\n";
echo "         </TR>\n";
for ($i=1;$i<=$numrows;$i++) {
    echo "   <TR>\n";
    echo "      <TD class=\"vatop lrpad border1111\">".$bigarray[$i]["roomsetname"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad border1111\">".$bigarray[$i]["capacity"]."</TD>\n";
    echo "      </TR>\n";
    }
echo "      </TABLE>\n";

// Get the scheduled elements.
$query = <<<EOD
SELECT 
    scheduleid,
    starttime,
    duration,
    trackname,
    sessionid,
    title,
    roomsetname 
  FROM
      Schedule
    JOIN Sessions USING (sessionid,conid)
    JOIN Tracks USING (trackid)
    JOIN RoomSets USING (roomsetid)
  WHERE
    roomid=$selroomid AND
    conid=$conid
  ORDER BY
    starttime
EOD;

list($numrows,$bigheader_array,$bigarray)=queryreport($query,$link,$title,$description,0);

// Build the table for the scheduled classes.
echo "<HR>\n";
echo "<H4>Current Room Schedule</H4>\n";
echo "<TABLE>\n";
echo "   <TR>\n";
echo "      <TH>Delete</TH>\n";
echo "      <TH>Start Time</TH>\n";
echo "      <TH>Duration</TH>\n";
echo "      <TH>Track</TH>\n";
echo "      <TH>Session ID</TH>\n";
echo "      <TH>Title</TH>\n";
echo "      <TH>Room Set</TH>\n";
echo "      </TR>\n";
for ($i=1;$i<=$numrows;$i++) {
  echo "   <TR>\n";
  echo "      <TD class=\"border0010\"><INPUT type=\"checkbox\" name=\"del$i\" value=\"1\"></TD>\n";
  echo "<INPUT type=\"hidden\" name=\"row$i\" value=\"".$bigarray[$i]["scheduleid"]."\">";
  echo "<INPUT type=\"hidden\" name=\"rowsession$i\" value=\"{$bigarray[$i]["sessionid"]}\"></TD>\n";
  echo "      <TD class=\"vatop lrpad border0010\">".time_description($bigarray[$i]["starttime"])."</TD>\n";
  echo "      <TD class=\"vatop lrpad border0010\">".$bigarray[$i]["duration"]."</TD>\n";
  echo "      <TD class=\"vatop lrpad border0010\">".$bigarray[$i]["trackname"]."</TD>\n";
  echo "      <TD class=\"vatop lrpad border0010\"> <A HREF=StaffAssignParticipants.php?selsess=".$bigarray[$i]["sessionid"].">".$bigarray[$i]["sessionid"]."</A></TD>\n";
  echo "      <TD class=\"vatop lrpad border0010\"> <A HREF=EditSession.php?id=".$bigarray[$i]["sessionid"].">",$bigarray[$i]["title"]."</A></TD>\n";
  echo "      <TD class=\"vatop lrpad border0010\">".$bigarray[$i]["roomsetname"]."</TD>\n";
  echo "      </TR>\n";
}
echo "   </TABLE>\n";

// Add to Room Schedule
echo "<H4>Add To Room Schedule</H4>\n";
echo "<TABLE>\n";

$query = <<<EOD
SELECT
    sessionid,
    trackname,
    title,
    roomid
  FROM
      Sessions 
    JOIN Tracks USING (trackid)
    JOIN SessionStatuses USING (statusid)
    LEFT JOIN Schedule USING (sessionid,conid)
  WHERE
    may_be_scheduled=1 AND
    conid=$conid
EOD;
if (strtoupper(DOUBLE_SCHEDULE)!="TRUE") {$query.= " HAVING roomid is null ";}
$query.= <<<EOD
  ORDER BY
    trackname,
    sessionid
EOD;

list($numsessions,$bigheader_array,$bigarray)=queryreport($query,$link,$title,$description,0);

// Build the selects for filling the slots.
for ($i=1;$i<=$newroomslots;$i++) {
    echo "   <TR>\n";
    echo "      <TD>";
    // ****DAY****
    if ($_SESSION['connumdays']>1) {
        echo "<Select name=day$i><Option value=0 ";
        if ((!isset($_POST["day$i"])) or $_POST["day$i"]==0) echo "selected";
        echo ">Day&nbsp;</Option>";
        for ($j=1; $j<=$_SESSION['connumdays']; $j++) {
            $x=$daymap["long"]["$j"];
            echo"         <OPTION value=$j ";
            if ($_POST["day$i"]==$j) echo "selected";
            echo ">$x</OPTION>\n";
            }
        echo "</Select>&nbsp;\n";
        }
	// ****HOUR****
    echo "          <Select name=\"hour$i\"><Option value=\"-1\" ";
    if (!isset($_POST["hour$i"])) $_POST["hour$i"]=-1;
    if ($_POST["hour$i"]==-1) echo "selected";
    echo ">Hour&nbsp;</Option><Option value=0 ";
	if ($_POST["hour$i"]==0) echo "selected";
	echo ">12</Option>";
    for ($j=1;$j<=11;$j++) {
        echo "<Option value=$j ";
        if ($_POST["hour$i"]==$j) echo "selected";
        echo ">$j</Option>";
        }
    echo "</select>\n";
	// ****MIN****
    echo "          <Select name=\"min$i\"><Option value=\"-1\" ";
	if (!isset($_POST["min$i"])) $_POST["min$i"]=-1;
    if ($_POST["min$i"]==-1) echo "selected";
	echo">Min&nbsp;</Option>";
    for ($j=0;$j<=55;$j+=5) {
        echo "<Option value=$j ";
        if ($_POST["min$i"]==$j) echo "selected";
		echo ">".($j<10?"0":"").$j."</Option>";
        }
    echo "</select>\n";
	// ****AM/PM****
    echo "          <Select name=\"ampm$i\"><Option value=0 ";
    if ((!isset($_POST["ampm$i"])) or $_POST["ampm$i"]==0) echo "selected";
    echo ">AM&nbsp;</Option><Option value=1 ";
    if ($_POST["ampm$i"]==1) echo "selected";
	echo ">PM</Option>";
    echo "</select>\n";
    echo "          </TD>";
    // ****Session****
    echo "      <TD><Select name=\"sess$i\"><Option value=\"unset\" ";
	if ((!isset($_POST["sess$i"])) or $_POST["sess$i"]=="unset") echo "selected";
    echo ">Select Session</Option>\n";
    for ($j=1;$j<=$numsessions;$j++) {
        echo "          <Option value=\"".$bigarray[$j]["sessionid"]."\" ";
        if ($_POST["sess$i"]==$bigarray[$j]["sessionid"]) echo "selected";
		echo ">{$bigarray[$j]['trackname']} - {$bigarray[$j]['sessionid']} - {$bigarray[$j]['title']}</option>\n";
        }
    echo "</select>\n";
    echo "          </TD>\n";
    echo "       </TR>\n";
    }
echo "</TABLE>";
echo "<INPUT type=\"hidden\" name=\"selroom\" value=\"$selroomid\">\n";
echo "<INPUT type=\"hidden\" name=\"numrows\" value=\"$numrows\">\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"update\" class=\"SubmitButton\">Update</BUTTON></DIV>\n";
echo "</FORM>\n";
correct_footer();
?>


