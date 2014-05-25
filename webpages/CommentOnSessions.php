<?php
require_once('StaffCommonCode.php');
require_once('SubmitCommentOn.php');
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

/* Adjust conid */
$conid=$_POST['conid'];
if ($conid=="") {$conid=$_GET['conid'];}
if ($conid=="") {$conid=$_SESSION['conid'];}

$query="SELECT conname FROM $ReportDB.ConInfo WHERE conid=$conid";
list($coninforows,$coninfoheader_array,$coninfo_array)=queryreport($query,$link,$title,$description,0);
$conname=$coninfo_array[1]['conname'];

$title="Comment On Session";
$description="<P>Add commentary for a particular session.</P>\n";

if (isset($_POST["comment"])) {
  $element_array = array('sessionid','conid','rbadgeid','commenter','comment');
  $value_array = array($_POST['sessionid'],
		       $conid,
		       $_SESSION['badgeid'],
		       htmlspecialchars_decode($_POST['commenter']),
		       htmlspecialchars_decode($_POST['comment']));
  $message.=submit_table_element($link,$title,"$ReportDB.CommentsOnSessions",$element_array, $value_array);
}

if (isset($_POST["sessionid"])) { // session was passed in, most likely from this page
  $sessionid=$_POST["sessionid"];
} elseif (isset($_GET["sessionid"])) { // session was selected by external page such as a report
  $sessionid=$_GET["sessionid"];
} else {
  $sessionid=0; // session was not yet selected.
  unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
}

$query=<<<EOD
SELECT
    sessionid,
    concat(trackname," - ",sessionid," - ",title) AS sessiontitle
  FROM
      $ReportDB.Sessions
    JOIN $ReportDB.Tracks USING (trackid)
    JOIN $ReportDB.SessionStatuses USING (statusid)
  WHERE
    may_be_scheduled=1 AND
    conid=$conid
  ORDER BY
    trackname,
    sessionid,
    title
EOD;

topofpagereport($title,$description,$additionalinfo);

echo "<P class=\"regmsg\">".$message."\n";

?>

<FORM name="selsesform" method=POST action="CommentOnSessions.php">
  <INPUT type="hidden" name="conid" value="<?php echo $conid; ?>">
  <DIV><LABEL for="sessionid">Select Session</LABEL>
    <SELECT name="sessionid">
      <?php populate_select_from_query($query,$sessionid,"Select Session",false); ?>
    </SELECT>
  </DIV>
  <P>&nbsp;</P>
  <DIV class="SubmitDiv">
<?php if (isset($_SESSION['return_to_page'])) {
    echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>"; } ?>
    <BUTTON type="submit" name="submit" class="SubmitButton">Select Session</BUTTON>
  </DIV>
</FORM>

<?php
// Stop page here if a schedule element has yet to be chosen.
if ($sessionid==0) {
    staff_footer();
    exit();
    }
else
?>
<HR>&nbsp;<BR>
<FORM name="sesscommentform" method=POST action="CommentOnSessions.php">
  <P>Comment on/for session <?php echo htmlspecialchars($sessionid)." for ".htmlspecialchars($conname); ?>:
  <INPUT type="hidden" name="sessionid" value="<?php echo $sessionid; ?>">
  <INPUT type="hidden" name="conid" value="<?php echo $conid; ?>">
  <DIV class="titledtextarea">
    <LABEL for="comment">Comment:</LABEL>
    <TEXTAREA name="comment" rows=6 cols=72></TEXTAREA>
  </DIV>
  <DIV class="password">
    <SPAN class="password2">Identifying tag of individual offering comment:</SPAN>
    <SPAN class="value"><INPUT type="text" size="30" name="commenter"></SPAN>
  </DIV>
  <BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>
<?php
staff_footer();
?>
