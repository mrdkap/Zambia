<?php
require_once ('StaffCommonCode.php');
global $link;
$title="Feedback Central Control";
$description="<P>Select, add, or set up the various elements that would allow feedback for this con instance.</P>\n";
$additionalinfo ="<P>Setting up feedback for your con is somewhat complex.  I'm not sure ";
$additionalinfo.="what motivated me, originally to be so baroque, but I think it was ";
$additionalinfo.="flexibility at the time.</P>\n";
$additionalinfo.="<P>Todo: reorder questions build</P>\n";
$conid=$_SESSION['conid'];

// Who can modify what:
if ((may_I("SuperProgramming")) or (may_I("SuperLogistics")) or (may_I("Maint"))) {
  $additionalinfo.="<P>Check or uncheck the Services and Features you desire to have ";
  $additionalinfo.="available or unavailable for request in the schedule elements.</P>\n";
} else {
  $additionalinfo="<P>You don't have permission to change any elements at this time.</P>\n";
}

// Show history
$hist="";
if ((!empty($_GET['history'])) AND (is_numeric($_GET['history']))) {
  $hist=$_GET['history'];
  $additionalinfo.="<P>To see this with all previous con information included, ";
  $additionalinfo.="<A HREF=AdminSetupFeedback.php?history=Y>click here</A>.</P>\n";
  $additionalinfo.="<P>To see this without the clutter of any of the previous con information, ";
  $additionalinfo.="<A HREF=AdminSetupFeedback.php>click here</A>.</P>\n";
  $additionalinfo.="<P><A HREF=AdminSetupFeedback.php?history=$hist&replicate_p=Y>Replicate</A>";
  $additionalinfo.=" that event's Location setup.</P>\n";
} elseif ((!empty($_POST['history'])) AND (is_numeric($_POST['history']))) {
  $hist=$_POST['history'];
  $additionalinfo.="<P>To see this with all previous con information included, ";
  $additionalinfo.="<A HREF=AdminSetupFeedback.php?history=Y>click here</A>.</P>\n";
  $additionalinfo.="<P>To see this without the clutter of any of the previous con information, ";
  $additionalinfo.="<A HREF=AdminSetupFeedback.php>click here</A>.</P>\n";
  $additionalinfo.="<P><A HREF=AdminSetupFeedback.php?history=$hist&replicate_p=Y>Replicate</A>";
  $additionalinfo.=" that event's Location setup.</P>\n";
} elseif ((!empty($_GET['history'])) AND ($_GET['history'] == "Y")) {
  $hist="Y";
  $additionalinfo.="<P>To see this without the clutter of any of the previous con information, ";
  $additionalinfo.="<A HREF=AdminSetupFeedback.php>click here</A>.</P>\n";
} elseif ((!empty($_POST['history'])) AND ($_POST['history'] == "Y")) {
  $hist="Y";
  $additionalinfo.="<P>To see this without the clutter of any of the previous con information, ";
  $additionalinfo.="<A HREF=AdminSetupFeedback.php>click here</A>.</P>\n";
} else {
  $additionalinfo.="<P>To see this with all previous con information included, ";
  $additionalinfo.="<A HREF=AdminSetupFeedback.php?history=Y>click here</A>.</P>\n";
}

// To select a particular con's info for display
$queryOtherCons=<<<EOD
SELECT
    conid,
    conname
  FROM
      ConInfo
  WHERE
    conid!=$conid AND
    conid!=0
  ORDER BY
    conid+0
EOD;
$additionalinfo.="<FORM name=\"conshow\" action=\"AdminSetupFeedback.php\" method=GET>\n";
$additionalinfo.="  <SPAN><LABEL for=\"history\">To see this with a perticular previous con information included: </LABEL><SELECT name=\"history\">\n";
$additionalinfo.=populate_select_from_query_inline($queryOtherCons, $hist, "SELECT", true);
$additionalinfo.="  </SELECT></SPAN>\n";
$additionalinfo.="  <BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"Select\">Select</BUTTON>\n</FORM>\n";

// If the view is limited, then show just this years, otherwise
// appropriate previous years and this years in the history section.
$wherestring="";
if ($hist == "") {
  $wherestring="WHERE conid=$conid";
} elseif (is_numeric($hist)) {
  $wherestring="WHERE conid=$conid or conid=$hist";
}

// Replicate an event as this event:
// . FeedbackPages - copy from selected con
// . FeedbackPageHasType - for all the selected con FeedbackPages, mapping to the current con (tricky)
// . TypeHasQuestionType - copy from selected con
if ((!empty($hist)) and (is_numeric($hist)) and ($_GET['replicate_p'] == "Y")) {
  /*
  // Gets the values for the current conid
  $query="SELECT * FROM Location WHERE conid=$hist";
  list($rows,$header_array,$table_array)=queryreport($query,$link,$title,$description,0);

  // Walk each row of the table
  for ($i=1 ; $i<=$rows; $i++) {

    // Empties the element array and value array for each instance
    $element_array=array();
    $value_array=array();

    // Populate each new row, by column for the new con instance
    foreach ($header_array as $column) {
      if ($column != "locationid") {
	$element_array[] = $column;
	if ($column == "conid") {
	  $value_array[] = $conid;
	} else {
	  $value_array[] = $table_array[$i][$column];
	}
      }
    }
    $message.=submit_table_element($link, $title, "Location", $element_array, $value_array);
  }
  */
}


// Section: Adding Questions Types
$AQT_info.="<A NAME=\"QuestionTypes\"></A>\n";
$AQT_info.=<<<EOD
<P>If your existing questions are not appropriate for this particular
event, you might want to add a question type specific to this
event, or a new general category for the new thing this event will
be covering.  Adding this question type into the QuestionTypes
table is the first step, then subsequently add the questions to the
QuestionsForSurvey table.</P>

<P>The existing question types in the QuestionTypes table are:</P>
EOD;

$queryAQT=<<<EOD
SELECT
    concat("(",questiontypeid,") ",questiontypename) AS "Name",
    questiontypenotes AS "Notes"
  FROM
      QuestionTypes
  WHERE
    questiontypeid in (SELECT
        questiontypeid
      FROM
          FeedbackPages
      $wherestring)
EOD;

list($aqtrows,$aqtheader_array,$aqt_array)=queryreport($queryAQT,$link,$title,$description,0);

// If there are no rows, rewrite the header more usefully
if ($aqtrows==0) {
  $aqtheader_array[0]="No Question Types set up for this event";
}

$AQT_info.=renderhtmlreport(1,$aqtrows,$aqtheader_array,$aqt_array);

// Section: Adding Questions
$AQ_info.="<A NAME=\"QuestionsForSurvey\"></A>\n";
$AQ_info.=<<<EOD
<P>Since each set of questions has already circulated and has
answers inside Zambia already, changing the questions invalidates
all the data collected. To avoid such a state adding questions (no
matter how similar they seem) is a better choice than invalidating
previous data.</P>

<P>The existing question types in the QuestionTypes table broken down
by the question types above are:</P>
EOD;

// If there are no rows, rewrite the header more usefully
if ($aqtrows==0) {
  $AQ_info.="<P>No Question Types, so no questions selected.</P>\n";
}

for ($i=1; $i<=$aqtrows; $i++) {
  $tmpheader=$aqt_array[$i]['Name'];
  preg_match('/[\d]+/',$tmpheader,$matches);
  $tmpqid=$matches[0];
  $queryAQ=<<<EOD
SELECT
    questionid AS "id",
    questiontext AS "$tmpheader",
    display_order,
    if (Countnum IS NULL,"",Countnum) AS "Num"
  FROM
      QuestionsForSurvey
    LEFT JOIN (SELECT
        questionid,
        count(*) AS "Countnum"
      FROM
          Feedback
      GROUP BY
	questionid) FB USING (questionid)
  WHERE
    questiontypeid=$tmpqid
EOD;
  list($aqrows,$aqheader_array,$aq_array)=queryreport($queryAQ,$link,$title,$description,0);

  // If there are no rows, rewrite the header to solicit questions
  if ($aqrows==0) {
    $aqrows++;
    $aqheader_array[0]="New Question for $tmpheader";
    $aq_array[$aqrows]["New Question for $tmpheader"]="New Question:<input type=\"text\" size=50 id=\"test\" name=\"test\" value=\"\">\n";
  } else {
    $aqheader_array[0]="";
  }

  // Check to see if any of the questions have ever been filled out.
  $noupdate_p=0;
  for ($j=1; $j<=$aqrows; $j++) {
    if (is_numeric($aq_array[$j]["Num"])) {
      $noupdate_p++;
    }
  }

  // If none of the questions have been filled out
  if ($noupdate_p==0) {
    for ($j=1; $j<=$aqrows; $j++) {
      $aq_array[$j]["Num"]="Update Question:<br /><input type=\"text\" size=50 id=\"test\" name=\"test\" value=\"\">\n";
    }
    $aqrows++;
    $aq_array[$aqrows]["$tmpheader"]="New Question:<br /><input type=\"text\" size=50 id=\"test\" name=\"test\" value=\"\">\n";
  }

  // Collapse the various for easier viewing
  $AQ_info.="<P>$tmpheader <A id=\"my$tmpheader\" HREF=\"javascript:toggle('$tmpheader','my$tmpheader');\">show</A></div>\n<div style=\"clear:both;\"></div>";
  $AQ_info.="<div id=\"$tmpheader\" style=\"display: none;\">";
  //$AQ_info.="<div id=\"$tmpheader\" style=\"display: block;\">";
  $AQ_info.=renderhtmlreport(1,$aqrows,$aqheader_array,$aq_array);
  $AQ_info.="</div></P>\n";
}

// Section: Feedback Pages
$FP_info.="<A NAME=\"FeedbackPages\"></A>\n";
$FP_info.=<<<EOD
<P>For your event, there might be several different feedback forms
you want available, so that an individual is not overwhelmed by the
number of possible choices.  One of the ways to do this is to break
it down by time period, another is to break it down by class type.
Once you have broken them down, this table sets up the various
possible views.<P>

<P>At least one view should be set to "single class", with a start
and end at the start and end of your con so that the reference from
the descriptions etc will work.</P>

<P>If you want to have a "General" feedback page, it also should
start and end at the start and end of your con as well.</P>


<P>The existing feedback pages in the FeedbackPages table are:</P>
EOD;

$queryFP=<<<EOD
SELECT
    connamelong AS "Event",
    concat("(",fpageid,") ",fpagedesc) AS "Desc",
    concat(DATE_FORMAT(ADDTIME(constartdate,SEC_TO_TIME(fpagestart)),"%a %l:%i %p"), " (",fpagestart,")") AS "Start",
    concat(DATE_FORMAT(ADDTIME(constartdate,SEC_TO_TIME(fpageend)),"%a %l:%i %p"), " (",fpageend,")") AS "End",
    fpagecols AS "# Cols",
    concat("(",questiontypeid,") ",questiontypename) AS "Question Type"
  FROM
      FeedbackPages
    JOIN ConInfo USING (conid)
    JOIN QuestionTypes USING (questiontypeid)
  $wherestring
  ORDER BY
    conid+0,
    fpagestart,
    fpageend desc,
    fpagedesc
EOD;

list($fprows,$fpheader_array,$fp_array)=queryreport($queryFP,$link,$title,$description,0);

// Check to see if there are no entries for this con, and make the return more useful.
if ($fprows==0) {
  $fpheader_array[0]="No Feedback Pages set up for this event";
}

// Commented out the Show/Hide for this, since it's selected by con info
//$FP_info.="<P><A id=\"myFeedbackPages\" HREF=\"javascript:toggle('FeedbackPages','myFeedbackPages');\">show</A></div>\n<div style=\"clear:both;\"></div>";
//$FP_info.="<div id=\"FeedbackPages\" style=\"display: none;\">";
$FP_info.=renderhtmlreport(1,$fprows,$fpheader_array,$fp_array);
//$FP_info.="</div></P>\n";

// Section: Feedback Page Has Type
$FPHT_info.="<A NAME=\"FeedbackPageHasType\"></A>\n";
$FPHT_info.=<<<EOD
<P>The type of schedule elements included in each possible
Feedback page is set in the FeedbackPageHasType.  This can have
multiple mappings or no mappings at all.</P>

<P>For example, if you have both classes and panels, they
have different values for their typeid in the Sessions table.  To
have them both show up in the "single class" page, that page id
needs two entries in the FeedbackPageHasType table, each one
mapping the typeid to that fpageid.  "single class" should have a
mapping for every element you reference, but the feedback page for
just the classes would only have the typeid of classes mapped to
it.</P>

<P>Also the "General" page should have no types mapped to it,
since it should not be called anywhere but from the General
reference.</P>

<P>The existing mapping between Feedback Page and Type in the FeedbackPageHasType table are:</P>
EOD;
$queryFPHT=<<<EOD
SELECT
    concat("(",fpageid,") ",fpagedesc) AS "Desc",
    concat("(",typeid,") ",typename) AS "Type"
  FROM
      FeedbackPageHasType
    JOIN FeedbackPages USING (fpageid)
    JOIN Types USING (typeid)
  $wherestring
  ORDER BY
    conid+0,
    fpagestart,
    fpageend desc,
    fpagedesc
EOD;
list($fphtrows,$fphtheader_array,$fpht_array)=queryreport($queryFPHT,$link,$title,$description,0);

// If there are no rows, rewrite the header more usefully
if ($fphtrows==0) {
  $fphtheader_array[0]="No mapping to Feedback Pages set up for this event";
}

$FPHT_info.=renderhtmlreport(1,$fphtrows,$fphtheader_array,$fpht_array);

// Section: Type Has Question Type
$THQT_info.="<A NAME=\"TypeHasQuestionType\"></A>\n";
$THQT_info.=<<<EOD
<P>The existing mapping between Types and Question Types in the TypeHasQuestionType table are:</P>
EOD;
$queryTHQT=<<<EOD
SELECT
    connamelong AS "Event",
    concat("(",questiontypeid,") ",questiontypename) AS "Question",
    concat("(",typeid,") ",typename) AS "Type"
  FROM
      TypeHasQuestionType
    JOIN ConInfo USING (conid)
    JOIN Types USING (typeid)
    JOIN QuestionTypes USING (questiontypeid)
  $wherestring
  ORDER BY
    conid+0,
    questiontypeid,
    typeid
EOD;
list($thqtrows,$thqtheader_array,$thqt_array)=queryreport($queryTHQT,$link,$title,$description,0);

// If there are no rows, rewrite the header more usefully
if ($thqtrows==0) {
  $thqtheader_array[0]="No mapping between Types and Question Types set up for this event";
}

$THQT_info.=renderhtmlreport(1,$thqtrows,$thqtheader_array,$thqt_array);

// Section: TOC
$TOC_info.=<<<EOD
<OL>
  <LI><A HREF="#FeedbackPages">Feedback Pages</A></LI>
  <LI><A HREF="#FeedbackPageHasType">Feedback Page Type Mapping</A></LI>
  <LI><A HREF="#TypeHasQuestionType">Type to Question Type Mapping</A></LI>
  <LI><A HREF="#QuestionTypes">Question Types</A></LI>
  <LI><A HREF="#QuestionsForSurvey">Questions</A></LI>
</OL>
EOD;

// Begin the page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// Exit out if there is no permissions.
if ((!may_I("SuperProgramming")) and (!may_I("SuperLogistics")) and (!may_I("Maint"))) {
  correct_footer();
  exit;
}


?>
<SCRIPT>
function toggle(showHideDiv, switchTextDiv) {
	var ele = document.getElementById(showHideDiv);
	var text = document.getElementById(switchTextDiv);
	if(ele.style.display == "block") {
    		ele.style.display = "none";
		text.innerHTML = "show";
  	}
	else {
		ele.style.display = "block";
		text.innerHTML = "hide";
	}
}
</SCRIPT>

<?php
echo $TOC_info;
echo "\n<hr>\n";
echo $FP_info;
echo $FP_work;
echo "\n<hr>\n";
echo $FPHT_info;
echo $FPHT_work;
echo "\n<hr>\n";
echo $THQT_info;
echo $THQT_work;
echo "\n<hr>\n";
echo $AQT_info;
echo $AQT_work;
echo "\n<hr>\n";
echo $AQ_info;
echo $AQ_work;

// Close the page
correct_footer();
