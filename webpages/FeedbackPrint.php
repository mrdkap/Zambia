<?php
require_once('StaffCommonCode.php');
require_once('../../tcpdf/config/lang/eng.php');
require_once('../../tcpdf/tcpdf.php');
// require_once('ChartSVGFeedback.php');

/* Global Variables */
global $link;

/* Localizations */
set_time_limit(0);
$_SESSION['return_to_page']="FeedbackPrint.php";
$title="Feedback Printing";
$print_p=$_GET['print_p'];
$conid=$_GET['conid'];
$badgeid=$_GET['badgeid'];

/* Adjust conid */
if ($conid=="") {$conid=$_SESSION['conid'];}

/* Adjust badgeid */
if ($badgeid=="") {
  $conid_or_badgeid="conid=$conid";
  $feedback_string="con $conid";
  $add_badgeid="";
  $pdf_title=$conid;
} else {
  $conid_or_badgeid="badgeid=$badgeid";
  $feedback_string="participant $badgeid";
  $add_badgeid="&badgeid=$badgeid";
  $pdf_title=$badgeid;
}

$description="<P>A way to <A HREF=\"FeedbackPrint.php?print_p=T$add_badgeid\">print</A> the feedback for $feedback_string.</P>\n<hr>\n";

/* Populate feedback array */
$feedback_array=getFeedbackData("");

/* Get class data */
$query=<<<EOD
SELECT
  if ((pubsname is NULL), '', GROUP_CONCAT(DISTINCT concat(pubsname,if((moderator in ('1','Yes')),'(m)','')) SEPARATOR ', ')) AS 'Participants',
    GROUP_CONCAT(DISTINCT DATE_FORMAT(ADDTIME(constartdate,starttime),'%a %l:%i %p') SEPARATOR ', ') AS 'Start Time',
    GROUP_CONCAT(DISTINCT trackname SEPARATOR ', ') as 'Track',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    GROUP_CONCAT(DISTINCT roomname SEPARATOR ', ') AS Roomname,
    estatten AS Attended,
    Sessionid,
    Conid,
    Conname,
    concat(sessionid,"-",conid) AS "Sess-Con",
    if((questiontypeid IS NULL),"",questiontypeid) AS questiontypeid,
    if((title_good_web IS NULL),title,title_good_web) AS Title,
    if((subtitle_good_web IS NULL), secondtitle,subtitle_good_web) AS Subtitle,
    concat(desc_good_web,'</P>') AS 'Web Description',
    concat(desc_good_book,'</P>') AS 'Book Description'
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms USING (roomid)
    JOIN Tracks USING (trackid)
    LEFT JOIN ParticipantOnSession USING (sessionid,conid)
    LEFT JOIN Participants USING (badgeid)
    LEFT JOIN TypeHasQuestionType USING (typeid,conid)
    JOIN PubStatuses USING (pubstatusid)
    JOIN ConInfo USING (conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as title_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('title') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  descriptionlang='en-us') TGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as subtitle_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('subtitle') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  descriptionlang='en-us') SGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as desc_good_web
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('description') AND
	  biostatename in ('good') AND
	  biodestname in ('web') AND
	  descriptionlang='en-us') DGW USING (sessionid,conid)
    LEFT JOIN (SELECT
        sessionid,
	conid,
	descriptiontext as desc_good_book
      FROM
          Descriptions
	  JOIN DescriptionTypes USING (descriptiontypeid)
          JOIN BioStates USING (biostateid)
          JOIN BioDests USING (biodestid)
      WHERE
	  descriptiontypename in ('description') AND
	  biostatename in ('good') AND
	  biodestname in ('book') AND
	  descriptionlang='en-us') DGB USING (sessionid,conid)
  WHERE
    $conid_or_badgeid AND
    pubstatusname in ('Public') AND
    (volunteer IS NULL OR volunteer not in ('1','Yes')) AND
    (introducer IS NULL OR introducer not in ('1','Yes')) AND
    (aidedecamp IS NULL OR aidedecamp not in ('1','Yes'))
  GROUP BY
    conid,
    sessionid
  ORDER BY
    conid,
    title
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

// Return the whole con feedback
// If asked for by a staff person, and it's not just a person
if (may_I("Staff") and ($badgeid=="")) {
   $elements++;
   $element_array[$elements]['Participants']=" ";
   $element_array[$elements]['Start Time']=" ";
   $element_array[$elements]['Track']=" ";
   $element_array[$elements]['Duration']=" ";
   $element_array[$elements]['Roomname']=" ";
   $element_array[$elements]['Attended']=" ";
   $element_array[$elements]['Sessionid']="-1";
   $element_array[$elements]['Conid']="$conid";
   $element_array[$elements]['Conname']="$conname";
   $element_array[$elements]['Sess-Con']="-1-".$conid;
   $element_array[$elements]['questiontypeid']="2";
   $element_array[$elements]['Title']="Whole Con Feedback";
   $element_array[$elements]['Subtitle']=" ";
   $element_array[$elements]['Web Description']=" ";
   $element_array[$elements]['Book Description']=" ";
}

// Document information
class MYPDF extends TCPDF {
  public function Footer() {
    $this->SetY(-15);
    $this->SetFont("helvetica", 'I', 8);
    $this->Cell(0, 10, "Copyright ".date('Y')." New England Leather Alliance, a Coalition Partner of NCSF and a subscribing organization of CARAS", 'T', 1, 'C');
  }
}

$pdf = new MYPDF('p', 'mm', 'letter', true, 'UTF-8', false);
$pdf->SetCreator('Zambia');
$pdf->SetAuthor('Programming Team');
$pdf->SetTitle('Feedback' . $_SESSION['conname']);
$pdf->SetSubject('Feedback for the Classes and Panels');
$pdf->SetKeywords('Zambia, Presenters, Feedback');
$pdf->SetHeaderData($_SESSION['conlogo'], 70, $_SESSION['conname'], $_SESSION['conurl']);
$pdf->setHeaderFont(Array("helvetica", '', 10));
$pdf->setFooterFont(Array("helvetica", '', 8));
$pdf->SetDefaultMonospacedFont("courier");
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->setFontSubsetting(true);
$pdf->SetFont('helvetica', '', 8, '', true);
$pdf->AddPage();

$printstring1 ="<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\">\n";
$printstring1.="  <circle cx=\"100\" cy=\"50\" r=\"40\" stroke=\"black\"";
$printstring1.="  stroke-width=\"2\" fill=\"red\"/>\n";
$printstring1.="</svg>\n";

$printstring2=<<<EOD
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="738px" height="756px" version="1.1">
<g font-size="9px" font-family="helvetica" fill="#000">
<text x="52" y="423" text-anchor="end">0%-</text>
<text x="52" y="342" text-anchor="end">20%-</text>
<text x="52" y="261" text-anchor="end">40%-</text>
<text x="52" y="180" text-anchor="end">60%-</text>
<text x="52" y="99" text-anchor="end">80%-</text>
<text x="52" y="18" text-anchor="end">100%-</text>
<rect height="363.103448276" x="54" y="50.8965517241" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="27.9310344828" x="54" y="386.068965517" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<rect height="13.9655172414" x="54" y="400.034482759" width="48.6" style="stroke:#000;stroke-width:1ps;fill:yellow;"/>
<text x="94.5" y="432" text-anchor="middle">Q1</text>
<text x="94.5" y="450" text-anchor="middle">Out of 29</text>
<rect height="378" x="144" y="36" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="27" x="144" y="387" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<text x="184.5" y="432" text-anchor="middle">Q2</text>
<text x="184.5" y="450" text-anchor="middle">Out of 30</text>
<rect height="391.034482759" x="234" y="22.9655172414" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="13.9655172414" x="234" y="400.034482759" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<text x="274.5" y="432" text-anchor="middle">Q3</text>
<text x="274.5" y="450" text-anchor="middle">Out of 29</text>
<rect height="364.5" x="324" y="49.5" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="27" x="324" y="387" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<rect height="13.5" x="324" y="400.5" width="48.6" style="stroke:#000;stroke-width:1ps;fill:yellow;"/>
<text x="364.5" y="432" text-anchor="middle">Q4</text>
<text x="364.5" y="450" text-anchor="middle">Out of 30</text>
<rect height="391.5" x="414" y="22.5" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="13.5" x="414" y="400.5" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<text x="454.5" y="432" text-anchor="middle">Q5</text>
<text x="454.5" y="450" text-anchor="middle">Out of 30</text>
<rect height="307.24137931" x="504" y="106.75862069" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="83.7931034483" x="504" y="330.206896552" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<rect height="13.9655172414" x="504" y="400.034482759" width="48.6" style="stroke:#000;stroke-width:1ps;fill:yellow;"/>
<text x="544.5" y="432" text-anchor="middle">Q6</text>
<text x="544.5" y="450" text-anchor="middle">Out of 29</text>
<rect height="378" x="594" y="36" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="13.5" x="594" y="400.5" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<rect height="13.5" x="594" y="400.5" width="48.6" style="stroke:#000;stroke-width:1ps;fill:yellow;"/>
<text x="634.5" y="432" text-anchor="middle">Q7</text>
<text x="634.5" y="450" text-anchor="middle">Out of 30</text>
<text x="686" y="423" text-anchor="start">-0%</text>
<text x="686" y="342" text-anchor="start">-20%</text>
<text x="686" y="261" text-anchor="start">-40%</text>
<text x="686" y="180" text-anchor="start">-60%</text>
<text x="686" y="99" text-anchor="start">-80%</text>
<text x="686" y="18" text-anchor="start">-100%</text>
<text x="370" y="486" text-anchor="middle">Feedback results for A Fist Full of Fun</text>
<text x="54" y="522" fill="blue" text-anchor="start">Totally Agree=blue</text>
<text x="54" y="540" fill="green" text-anchor="start">Somewhat Agree=green</text>
<text x="54" y="558" fill="yellow" text-anchor="start">Neutral=yellow</text>
<text x="54" y="576" fill="orange" text-anchor="start">Somewhat Disagree=orange</text>
<text x="54" y="594" fill="red" text-anchor="start">Totally Disagree=red</text>
<text x="54" y="630" text-anchor="start">Q 1: This class/panel matched the Web or Program Book description</text>
<text x="54" y="648" text-anchor="start">Q 2: I had fun AND learned in this class/panel</text>
<text x="54" y="666" text-anchor="start">Q 3: I'd recommend the class/panel to a friend</text>
<text x="54" y="684" text-anchor="start">Q 4: This class/panel has inspired me to try something new</text>
<text x="54" y="702" text-anchor="start">Q 5: The presenter(s) really knew their stuff</text>
<text x="54" y="720" text-anchor="start">Q 6: My interests and curiosities were represented in this year's programming</text>
<text x="54" y="738" text-anchor="start">Q 7: Bring this presenter back next year</text>
</g>
</svg>
EOD;

$workstring="<DL>\n";
for ($i=1; $i<=$elements; $i++) {
  $workstring.=sprintf("<P><DT>%s <B>%s</B>",$element_array[$i]['Conname'],$element_array[$i]['Title']);
  if ($element_array[$i]['Subtitle'] !='') {
    $workstring.=sprintf(": %s",$element_array[$i]['Subtitle']);
  }
  if ($element_array[$i]['Participants']) {
    $workstring.=sprintf(" by <B>%s</B> ",$element_array[$i]['Participants']);
  }
  if ($element_array[$i]['Track']) {
    $workstring.=sprintf("&mdash; <i>%s</i>",$element_array[$i]['Track']);
  }
  if ($element_array[$i]['Start Time']) {
    $workstring.=sprintf("&mdash; <i>%s</i>",$element_array[$i]['Start Time']);
  }
  if ($element_array[$i]['Duration']) {
    $workstring.=sprintf("&mdash; <i>%s</i>",$element_array[$i]['Duration']);
  }
  if ($element_array[$i]['Roomname']) {
    $workstring.=sprintf("&mdash; <i>%s</i>",$element_array[$i]['Roomname']);
  }
  if ($element_array[$i]['Attended']) {
    $workstring.=sprintf("&mdash; About %s Attended",$element_array[$i]['Attended']);
  }
  if ($element_array[$i]['Web Description']) {
    $workstring.=sprintf("  </DT>\n  <DD><P>Web: %s</P>\n",$element_array[$i]['Web Description']);
  }
  if ($element_array[$i]['Book Description']) {
    $workstring.=sprintf("  </DD>\n  <DD><P>Book: %s</P>\n",$element_array[$i]['Book Description']);
  }
  if ($feedback_array[$element_array[$i]['Sess-Con']]) {
    $workstring.="  </DD>\n    <DD>Written feedback from surveys:\n<br>\n";
    $workstring.=sprintf("%s<br>\n",$feedback_array[$element_array[$i]['Sess-Con']]);
  }
  // Gather up the info before the graph
  $printstring.=$workstring;
  $pdf->writeHTML($workstring, true, false, true, false, "");
  $workstring="";
  $feedback_file=sprintf("../Local/Feedback/%s.jpg",$element_array[$i]["Sessionid"]);
  if (file_exists($feedback_file)) {
    $printstring.="  </DD>\n  <DD>Feedback graph from surveys:\n<br>\n";
    $printstring.=sprintf ("<img src=\"%s\">\n<br>\n",$feedback_file);
  }
  if (isset($feedback_array['graph'][$element_array[$i]['Sess-Con']])) {
    $workstring="  </DD>\n  <DD>Feedback graph from surveys:\n<br>\n";
    $printstring.=$workstring;
    $pdf->writeHTML($workstring, true, false, true, false, "");
    $workstring="";
    $graphstring=generateSvgString($element_array[$i]['Sessionid'],$element_array[$i]['Conid']);
    $printstring.=$graphstring;
    $pdf->ImageSVG("@".$graphstring,'','','','','','N','',1,true);
  }
  $workstring.="</DD></P>\n";
}
$workstring.="</DL>\n";
$printstring.=$workstring;
$pdf->writeHTML($workstring, true, false, true, false, "");

if ($print_p =="") {
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo "$printstring\n</hr>\n";
  correct_footer();
  } elseif ($print_p =="Template") {
  echo $printstring;
  } else {
  $pdf->Output('Feedback-'.$pdf_title.'.pdf', 'I');
 }
