<?php
require_once('StaffCommonCode.php');
global $link;

// LOCALIZATIONS
$title="Edit Reports";
$description="<P>Use this page to edit reports.</P>\n";
$additionalinfo="<P>A report has to be in a <A HREF=EditGroupFlows.php>Group</A> to work.</P>\n";
$additionalinfo.="<P>There should be some way to modify the permissions restriction.</P>\n";

// Check to see if page can be displayed
if (!may_I("Maint")) {
  $message_error ="Alas, you do not have the proper permissions to view this page.";
  $message_error.=" If you think this is in error, please, get in touch with an administrator.";
  RenderError($title,$message_error);
  exit();
}

// Submit the report, if there was one, when this is called
if ((isset($_POST["reportupdate"])) and ($_POST["reportupdate"]!="")) {
  if ($_POST["selreport"] == "-1") {
    $element_array=array('reportname','reporttitle','reportdescription','reportadditionalinfo','reportrestrictions','reportquery');
    $value_array=array(htmlspecialchars_decode($_POST["reportname"]),
		       htmlspecialchars_decode($_POST["reporttitle"]),
		       htmlspecialchars_decode($_POST["reportdescription"]),
		       htmlspecialchars_decode($_POST["reportadditionalinfo"]),
		       htmlspecialchars_decode($_POST["reportrestrictions"]),
		       htmlspecialchars_decode(refrom($_POST["reportquery"])));
    $message.=submit_table_element($link, $title, "Reports", $element_array, $value_array);
  } else {
    $pairedvalue_array=array("reportdescription='".mysqli_real_escape_string($link,stripslashes(htmlspecialchars_decode($_POST["reportdescription"])))."'",
			     "reportadditionalinfo='".mysqli_real_escape_string($link,stripslashes(htmlspecialchars_decode($_POST["reportadditionalinfo"])))."'",
			     "reportrestrictions='".mysqli_real_escape_string($link,stripslashes(htmlspecialchars_decode($_POST["reportrestrictions"])))."'",
			     "reportquery='".mysqli_real_escape_string($link,stripslashes(htmlspecialchars_decode(refrom($_POST["reportquery"]))))."'");
    $match_field="reportid";
    $match_value=$_POST["selreport"];
    $message.=update_table_element($link, $title, "Reports", $pairedvalue_array, $match_field, $match_value);
  }
}

// Clear the reportupdate value
$reportupdate="";

//Carry over the report number, if it was passed in
if (isset($_POST["selreport"])) { // reportid was passed probably from this form
  $selreportid=$_POST["selreport"];
 } elseif (isset($_GET["selreport"])) { // reportid was select by external page such as a report
  $selreportid=$_GET["selreport"];
 } else {
  $selreportid=0; // reportid was not yet selected.
 }

// Build the top of form query
$query=<<<EOD
SELECT
    reportid,
    reportname,
    reporttitle,
    reportdescription
  FROM
      Reports
  ORDER BY
    reportid
EOD;

if (!$Sresult=mysqli_query($link,$query)) {
  $message_error=$query."<BR>Error querying database. Unable to continue.<BR>";
  RenderError($title,$message_error);
  exit();
 }

// Begin the page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

?>

<FORM name="selreportform" method=POST action="EditReport.php">
<DIV><LABEL for="selreport">Select Report</LABEL>
<SELECT name="selreport">

<?php
echo "     <OPTION value=0";
if ($selreportid==0) {echo " selected";}
echo ">Select Report</OPTION>\n";
echo "     <OPTION value=-1>New Report</OPTION>\n";
while (list($reportid,$reportname,$reporttitle,$reportdescription)= mysqli_fetch_array($Sresult, MYSQLI_NUM)) {
  if (is_numeric($selreportid)) {
    echo "";
   } else { 
    if ($selreportid==$reportname) {
      $selreportid=$reportid;
   }
  }
  echo "     <OPTION value=\"$reportid\"";
  if ($selreportid==$reportid) {echo " selected";}
  echo ">".htmlspecialchars($reporttitle).": ".htmlspecialchars($reportdescription)."</OPTION>\n";
 }
?>

</SELECT>
</DIV>
<DIV class="SubmitDiv">
<BUTTON type="submit" name="submit" class="SubmitButton">Select</BUTTON>
</DIV>
</FORM>

<?php
// Stop page here if and individual has not yet been selected
if ($selreportid == 0) {
    correct_footer();
    exit();
    }

// Switch on if it is a new report or not
if ($selreportid == "-1") {
  $reportname='No Name Yet';
  $reporttitle='No Title Yet';
  $reportdescription='No Description Yet';
  $reportadditionalinfo='';
  $reportquery='No Query Yet';
 } else {

$query=<<<EOD
SELECT
    reporttitle,
    reportname,
    reportdescription,
    reportadditionalinfo,
    reportquery,
    reportrestrictions
  FROM
      Reports
  WHERE reportid='$selreportid'
EOD;

  list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

  $reporttitle=$report_array[1]['reporttitle'];
  $reportname=$report_array[1]['reportname'];
  $reportdescription=$report_array[1]['reportdescription'];
  $reportadditionalinfo=$report_array[1]['reportadditionalinfo'];
  $reportquery=$report_array[1]['reportquery'];
  $reportrestrictions=$report_array[1]['reportrestrictions'];

  // Return pointer
  echo "<P><A HREF=\"genreport.php?reportname=$reportname\">Return to report</A></P>";

 }
// Update form
?>
<HR>
<FORM name="reporteditform" method=POST action="EditReport.php">
<?php if ($selreportid=="-1") { ?>
<DIV class="titledtextarea">
  <SPAN><LABEL for="reportname"><B>Name: (No spaces, lower case, please.)</B><BR></LABEL>
  <INPUT type="text" size=72 name="reportname" id="reportname" value="<?php echo htmlspecialchars($reportname) ?>"></SPAN>
  <SPAN><LABEL for="reporttitle"><BR><B>Title:</B><BR></LABEL>
  <INPUT type="text" size=72 name="reporttitle" id="reporttitle" value="<?php echo htmlspecialchars($reporttitle) ?>"></SPAN>
<?php } else { ?>
  <P>Edit <?php echo htmlspecialchars($reporttitle)?> for <?php echo $_SESSION['conname']; ?>:
<DIV class="titledtextarea">
<?php } ?>
  <LABEL for="reportdescription">Description:</LABEL>
  <TEXTAREA name="reportdescription" rows=2 cols=72><?php echo htmlspecialchars($reportdescription) ?></TEXTAREA>
  <LABEL for="reportadditionalinfo">Additional Information:</LABEL>
  <TEXTAREA name="reportadditionalinfo" rows=2 cols=72><?php echo htmlspecialchars($reportadditionalinfo) ?></TEXTAREA>
  <LABEL for="reportrestrictions">Restricted To:</LABEL>
  <TEXTAREA name="reportrestrictions" rows=2 cols=72><?php echo htmlspecialchars($reportrestrictions) ?></TEXTAREA>
  <LABEL for="reportquery">Query: (note, due to a strange anomoly in the system, "FROM" is rendered in pig-latin.  Do not worry, it gets fixed.)</LABEL>
  <TEXTAREA name="reportquery" rows=15 cols=72><?php echo htmlspecialchars(unfrom($reportquery)) ?></TEXTAREA>
</DIV> 
<INPUT type="hidden" name="selreport" value="<?php echo $selreportid; ?>">
<INPUT type="hidden" name="reportupdate" value="Yes">
<BUTTON class="SubmitButton" type="submit" name="submit">Update</BUTTON>
</FORM>

<?php
correct_footer();
?>
