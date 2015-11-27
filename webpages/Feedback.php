<?php
require_once('PostingCommonCode.php');
require_once('../../tcpdf/config/lang/eng.php');
require_once('../../tcpdf/tcpdf.php');

if (isset($_GET['conid']) and ($_GET['conid'] != "")) {
  $conid=$_GET['conid'];
} elseif (isset($_POST['conid']) and ($_POST['conid'] != "")) {
  $conid=$_POST['conid'];
} else {
  $conid=$_SESSION['conid']; // make it a variable so it can be substituted
}

/* Global Variables */
global $link;
$badgename=$_SESSION['badgename']; // make it a variable so it can be substituted
$badgeid=$_SESSION['badgeid']; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($badgename=="") {$badgename='Anonymous';}
if ($badgeid=="") {$badgeid='100';}

// LOCALIZATIONS
$_SESSION['return_to_page']="Feedback.php?conid=$conid";
//$print_p=$_GET['print_p'];
$formstring="";

/* This query pulls all the questions, to be surveyed 
   questionid and questiontext are the key and text of the questions
   questiontypeid differentiates between the target of the questions*/
$query=<<<EOD
SELECT
    questiontext,
    questionid,
    questiontypeid
  FROM
      QuestionsForSurvey
  ORDER BY
    display_order

EOD;

// Retrive query
list($questioncount,$header_array1,$question_array)=queryreport($query,$link,$title,$description,0);

/* This query pulls the page description information for presentation 
   fpageid is the unique page number for this con and set of feedback
   fppagedesc is the short description of the page contents
   fpagestart and fpageend is the range of time that this page covers
   fpagecols is the number of columns at the top of the printed page
   questiontypeid differentiates between the target of the questions
*/
$query=<<<EOD
SELECT
    fpageid,
    fpagedesc,
    fpagestart,
    fpageend,
    fpagecols,
    questiontypeid
  FROM
      FeedbackPages
  WHERE
    conid=$conid

EOD;

// Retrive query
list($fpagecount,$fpageheader_array,$fpage_array)=queryreport($query,$link,$title,$description,0);

// Find single class and establish selday_array
for ($i=1; $i<=$fpagecount; $i++) {
  if ($fpage_array[$i]['fpagedesc']=="single class") {
    $single_class_no=$i;
  }
  $selday_array[$fpage_array[$i]['fpageid']]=$i;
}

// Zero the counter for the actual feedback elements
$questiontotal=0;

// Insert the passed values.
if ((isset($_POST["selsess"])) && ($_POST["selsess"]!=0)) {
  $query= "INSERT INTO Feedback (sessionid,conid,questionid,questionvalue) VALUES ";
  for ($i=1; $i<=$questioncount; $i++) {
    if ((isset($_POST["$i"])) && ($_POST["$i"]!="")) {
      $questiontotal++;
      $query.="(".$_POST['selsess'].",".$conid.",".$i.",".$_POST["$i"]."),";
    }
  }
  $query=substr($query,0,-1);
  // Only do if there are actual feedback question elements
  if ($questiontotal > 0) {
    if (!mysql_query($query,$link)) {
      $message_error=$query."<BR>Error updating $table.  Database not updated.";
      RenderError($title,$message_error);
      exit;
    }
  }
  $message.="Response database updated successfully.<BR>";

  // Only do if there are comments on the Session
  if ((isset($_POST['classcomment'])) && ($_POST['classcomment']!="")) {
    $element_array = array('sessionid','conid','rbadgeid','commenter','comment');
    $value_array = array($_POST['selsess'],$conid,$badgeid,$badgename,
			 htmlspecialchars_decode($_POST['classcomment']));
    $message.=submit_table_element($link,$title,"CommentsOnSessions",$element_array, $value_array);
  }

  // Only do if there are comments for Programming
  if ((isset($_POST['progcomment'])) && ($_POST['progcomment']!="")) {
    $element_array = array('rbadgeid','conid','commenter','comment');
    $value_array = array($badgeid,$conid,$badgename,htmlspecialchars_decode($_POST['progcomment']));
    $message.=submit_table_element($link,$title,"CommentsOnProgramming",$element_array, $value_array);
  }
}

$sessionid=$_GET['sessionid'];
$selday=$_GET['selday'];

// Set selday to "single class"
if ($sessionid!="") {$selday=$fpage_array[$single_class_no]['fpageid'];}

// Set the passed variables, or drop to default page
if (isset($selday) and ($selday!="")) {
  $dayname=$fpage_array[$selday_array[$selday]]['fpagedesc'];
  $time_start=$fpage_array[$selday_array[$selday]]['fpagestart'];
  $time_end=$fpage_array[$selday_array[$selday]]['fpageend'];
  $fpageid=$fpage_array[$selday_array[$selday]]['fpageid'];
  $questiontypeid=$fpage_array[$selday_array[$selday]]['questiontypeid'];
  $NumOfColumns=$fpage_array[$selday_array[$selday]]['fpagecols'];
} else {
  $title="Feedback Page";
  $description="<P>Please select the day/type you wish to generate the feedback form for:</P>\n";
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo "<UL>\n";
  for ($i=1; $i<=$fpagecount; $i++) {
    if ($i!=$single_class_no) {
      echo "  <LI><A HREF=\"Feedback.php?conid=$conid&selday=" . $fpage_array[$i]['fpageid'] . "\">" . $fpage_array[$i]['fpagedesc'] . "</A></LI>\n";
    }
  }
  echo "</UL>\n";
  correct_footer();
  exit();
}

// Set standard headers across the pages.
$title="$dayname Feedback";
$description="<P>Not sure which class?</P>";
$additionalinfo="<P>See ";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=desc&conid=$conid\">description</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=desc&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=sched&conid=$conid\">timeslots</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=sched&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=tracks&conid=$conid\">tracks</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=tracks&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=trtime&conid=$conid\">tracks by time</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=trtime&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="the <A HREF=\"PubsSched.php?format=rooms&conid=$conid\">rooms</A>\n";
$additionalinfo.="<A HREF=\"PubsSched.php?format=rooms&conid=$conid&short=Y\">(short)</A>,\n";
$additionalinfo.="or the <A HREF=\"PubsBios.php?conid=$conid\">bios</A>\n";
$additionalinfo.="<A HREF=\"PubsBios.php?short=Y&conid=$conid\">(short)</A> pages to choose from.</P>\n";
//$additionalinfo="<P><A HREF=\"Feedback.php?conid=$conid&selday=$selday&print_p=y\">Printable</A> version.</P>\n";
$additionalinfo.="<P>Done with this time block?  Pick a different one:</P>\n<UL>\n";
  for ($i=1; $i<=$fpagecount; $i++) {
    if ($i!=$single_class_no) {
      $additionalinfo.="  <LI><A HREF=\"Feedback.php?conid=$conid&selday=" . $fpage_array[$i]['fpageid'] . "\">" . $fpage_array[$i]['fpagedesc'] . "</A></LI>\n";
    }
  }
$additionalinfo.="</UL>\n";

// Document information
class MYPDF extends TCPDF {
  public function Footer() {
    $this->SetY(-15);
    $this->SetFont("helvetica", 'I', 8);
    $this->Cell(0, 10, "Copyright 2011 New England Leather Alliance, a Coalition Partner of NCSF and a subscribing organization of CARAS", 'T', 1, 'C');
  }
}

$pdf = new MYPDF('p', 'mm', 'letter', true, 'UTF-8', false);
$pdf->SetCreator('Zambia');
$pdf->SetAuthor('Programming Team');
$pdf->SetTitle('Feedback for '.$_SESSION['conname']);
$pdf->SetSubject('Gathering Feedback on this event');
$pdf->SetKeywords('Zambia, Presenters, Volunteers, Sessions, Feedback');
$pdf->SetHeaderData($_SESSION['conlogo'], 70, $_SESSION['conname'], $_SESSION['conurl']);
$pdf->setHeaderFont(Array("helvetica", '', 10));
$pdf->setFooterFont(Array("helvetica", '', 8));
$pdf->SetDefaultMonospacedFont("courier");
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_HEADER, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->setFontSubsetting(true);
$pdf->SetFont('helvetica', '', 8, '', true);
// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

/* This query finds what Type(s) of schedule elements need to be selected */
$query=<<<EOD
SELECT
    typeid
  FROM
      FeedbackPageHasType
  WHERE
    fpageid=$fpageid

EOD;

// Retrive query
list($typescount,$typesheader_array,$types_array)=queryreport($query,$link,$title,$description,0);

// Reduce query to a string of types, to be passed to the next query.
// Currently with a failure for full-con, so we might fix that later.
if ($typescount > 0) {
  for ($i=1; $i<=$typescount; $i++) {
    $shorttypes_array[]=$types_array[$i]['typeid'];
  }
  $types_string="(typeid = " . implode(" OR typeid = ",$shorttypes_array) . ") AND";
} else {$types_string="(typeid = 1 and typeid = 2) AND";}

/* This query grabs all the schedule elements to be rated, for the selected time period. */
$query=<<<EOD
SELECT
    DISTINCT title,
    DATE_FORMAT(ADDTIME(constartdate,starttime), '%l:%i %p') as time,
    sessionid,
    questiontypeid
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN TypeHasQuestionType USING (typeid,conid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
  WHERE
    $types_string
    Time_TO_SEC(starttime) > $time_start AND
    Time_TO_SEC(starttime) < $time_end AND
    conid=$conid AND
    pubstatusname in ('Public')
EOD;

if ($sessionid!="") {
  $query.=" AND sessionid=$sessionid";
 }
$query.=" ORDER BY title";

// Retrive query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

// Fix the questiontypeid for a single page
if ($sessionid!="") {$questiontypeid=$element_array[1]['questiontypeid'];}
  
/* Printing body. */
$printstring="<TABLE border=\"0\" cellpadding=\"4\"><TR><TD colspan=\"$NumOfColumns\" align=\"center\">Please, indicate the $dayname class you are offering feedback on.</TD></TR>";
$printstring.="<TR><TD>";
$formstring.="<FORM name=\"feedbackform\" method=POST action=\"Feedback.php?conid=$conid&selday=$selday\">\n";

if ($elements > 0) {
  /* Get the number of elements into $NumOfColumns rows */
  $NumPerColumn=ceil($elements/$NumOfColumns);

  if ($sessionid!="") {
    $formstring.="<INPUT type=\"hidden\" name=\"selsess\" value=\"".$element_array[1]['sessionid']."\">\n";
    $formstring.="<P>Feedback on ".$element_array[1]['title']." (".$element_array[1]['time'].")</P>\n";
  } else {
    $formstring.="<DIV><LABEL for=\"feedbackclass\">Select the $dayname class you are offering feedback on.</LABEL>\n";
    $formstring.="<SELECT name=\"selsess\">\n";
    $formstring.="    <OPTION value=0 SELECTED>Select Session</OPTION>\n";
    for ($i=1; $i<=$elements; $i++) {
      $printstring.="<img border=\"1\" src=\"images/whitebox.png\"> ";
      $printstring.=$element_array[$i]['title']." (".$element_array[$i]['time'].")<br>";
      $formstring.="    <OPTION value=\"".$element_array[$i]['sessionid']."\">";
      $formstring.=$element_array[$i]['title']." (".$element_array[$i]['time'].")</OPTION>\n";
      if ($i % $NumPerColumn == 0) {
	$printstring.="</TD><TD>";
      }
    }
    $printstring.="</TD></TR>";
    $printstring.="</TABLE>";
    $formstring.="</SELECT></DIV>\n";
  }
}

$printheaders="  <TR><TH colspan=\"2\">&nbsp;</TH><TH align=\"center\">Totally Agree</TH>";
$printheaders.="<TH align=\"center\">Somewhat Agree</TH><TH align=\"center\">Neutral</TH>";
$printheaders.="<TH align=\"center\">Somewhat Disagree</TH><TH align=\"center\">Totally Disagree</TH></TR>";
$printchoices="<TD align=\"center\">5</TD><TD align=\"center\">4</TD><TD align=\"center\">3</TD>";
$printchoices.="<TD align=\"center\">2</TD><TD align=\"center\">1</TD></TR>";

$formheaders="  <TR><TH>&nbsp;</TH><TH>Totally Agree</TH><TH>Somewhat Agree</TH><TH>Neutral</TH>";
$formheaders.="<TH>Somewhat Disagree</TH><TH>Totally Disagree</TH></TR>";

$printstring.="<TABLE border=\"1\">";
$printstring.="<TR><TD colspan=\"7\" align=\"center\">Please answer the following questions where 5 = totally agree, 1 = totally disagree.</TD></TR>";
$formstring.="<P>&nbsp;&nbsp;Please answer the following questions from totally agree to totally disagree.\n";
$formstring.="If the question does not apply to you, feel free to leave it blank.\n";
$formstring.="<TABLE border=1>\n";
$printstring.=$printheaders;
$formstring.=$formheaders."\n";
for ($i=1; $i<=$questioncount; $i++) {
  if ($question_array[$i]['questiontypeid'] == $questiontypeid) {
    $printstring.="  <TR><TD colspan=\"2\">".$question_array[$i]['questiontext'].":<br>&nbsp;</TD>".$printchoices;
    $formstring.="  <TR><TD>".$question_array[$i]['questiontext'].":<br>&nbsp;</TD>";
    $formstring.="<TD align=\"center\">";
    $formstring.="<INPUT type=\"radio\" name=\"".$question_array[$i]['questionid']."\" id=\"".$question_array[$i]['questionid']."\" value=\"5\">";
    $formstring.="</TD><TD align=\"center\">";
    $formstring.="<INPUT type=\"radio\" name=\"".$question_array[$i]['questionid']."\" id=\"".$question_array[$i]['questionid']."\" value=\"4\">";
    $formstring.="</TD><TD align=\"center\">";
    $formstring.="<INPUT type=\"radio\" name=\"".$question_array[$i]['questionid']."\" id=\"".$question_array[$i]['questionid']."\" value=\"3\">";
    $formstring.="</TD><TD align=\"center\">";
    $formstring.="<INPUT type=\"radio\" name=\"".$question_array[$i]['questionid']."\" id=\"".$question_array[$i]['questionid']."\" value=\"2\">";
    $formstring.="</TD><TD align=\"center\">";
    $formstring.="<INPUT type=\"radio\" name=\"".$question_array[$i]['questionid']."\" id=\"".$question_array[$i]['questionid']."\" value=\"1\">";
    $formstring.="</TD></TR>\n";
  }
}
$printstring.="</TABLE></P><hr>";
$formstring.="</TABLE></P>\n";
if ($elements > 0) {
  $formstring.="<LABEL for=\"classcomment\">Other comments on this class:</LABEL>\n<br>\n";
  $formstring.="  <TEXTAREA name=\"classcomment\" rows=6 cols=72></TEXTAREA>\n<br>\n";
  $formstring.="<LABEL for=\"progcomment\">Comments on the FFF in general: (not shared with the presenter)</LABEL>\n<br>\n";
  $formstring.="  <TEXTAREA name=\"progcomment\" rows=6 cols=72></TEXTAREA>\n<br>\n";
} else {
  $formstring.="<INPUT type=\"hidden\" name=\"selsess\" value=\"437\">\n";
  $formstring.="<LABEL for=\"classcomment\">Other Comments: (The more you give us, the better we can meet your desires.)</LABEL>\n<br>\n";
  $formstring.="  <TEXTAREA name=\"classcomment\" rows=6 cols=72></TEXTAREA>\n<br>\n";
}
$formstring.="<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Send Feedback</BUTTON>\n";
$formstring.="</FORM>\n";
$printstring.="<P>Other comments/ideas/questions/feedback about the class or the flea (feel free to use the back):";

if ($print_p =="") {
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo $formstring;
  correct_footer();
 } else {
  $pdf->AddPage();
  $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $printstring, $border=0, $ln=true, $fill=false, $reseth=true, $align='', $autopadding=true);
  $pdf->writeHTMLCell($w=0, $h=0, $x=PDF_MARGIN_LEFT, $y=137, $printstring, $border=0, $ln=true, $fill=false, $reseth=true, $align='', $autopadding=true);
  $pdf->Output($dayname.'Feedback.pdf', 'I');
 }
?>
