<?php
require_once('StaffCommonCode.php');
require_once('../../tcpdf/config/lang/eng.php');
require_once('../../tcpdf/tcpdf.php');
global $link;
$conid=$_SESSION['conid'];

// LOCALIZATIONS
$_SESSION['return_to_page']="PhotoLoungeContactSheet.php";
$title="Photo Lounge Contact Sheet";
$description="<P>These are the curated Photo Lounge pictures, with the information we have for them.</P>\n";
$additionalinfo="<P><A HREF=\"PhotoLoungeContactSheet.php?print_p=Y\">Print</A> this.</P>\n";
$additionalinfo.="<P><A HREF=\"PhotoLoungeCollectVotes.php\">Change</A> what is included in this.</P>\n<HR>\n";

// For printing
if ($_GET['print_p']=="Y") {
  $print_p=true;
}

// Set the directory that we keep the files in
$target_dir="../Local/$conid/Photo_Lounge_Submissions/";

// Printed document information
class MYPDF extends TCPDF {
  public function Footer() {
    $this->SetY(-15);
    $this->SetFont("helvetica", 'I', 8);
    $this->Cell(0, 10, "Copyright ".date('Y')." New England Leather Alliance, a Coalition Partner of NCSF and a subscribing organization of CARAS", 'T', 1, 'C');
  }
}

$pdf = new MYPDF('l', 'mm', 'letter', true, 'UTF-8', false);
$pdf->SetCreator('Zambia');
$pdf->SetAuthor('Programming Team');
$pdf->SetTitle('Photo Lounge Contact Sheet for '.$_SESSION['conname']);
$pdf->SetSubject('Photo Lounge Contact Sheet for '.$_SESSION['conname']);
$pdf->SetKeywords('Zambia, Photo, Contact');
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
$pdf->SetFont('helvetica', '', 10, '', true);

// Pull the picture information and thumbnail for each picture.
$query=<<<EOD
SELECT
    concat("<TABLE width=100%>\n  <TR>\n    <TD>",
	   GROUP_CONCAT("<img src=\"../Local/$conid/Photo_Lounge_Submissions/.thmb/",photofile,"\"><br>",
			"Title: ",phototitle,"<br>",
			if((photoartist="No Photographer Listed"),"",concat("Artist: ",photoartist,"<br>")),
			if((photomodel="No Model Listed"),"",concat("Model: ",photomodel,"<br>")),
			if((photoloc="No Location Listed"),"",concat("Location: ",photoloc,"<br>")),
			if((photonotes="NULL"),"",concat("Notes: ",photonotes,"<br>"))
			SEPARATOR "</TD>\n    <TD>"),
	   "</TD>\n  </TR>\n</TABLE>") AS "Pictures",
    GROUP_CONCAT("<A HREF=StaffEditPhotoLoungeInfo.php?photoid=",photoid,">edit pic</A>" SEPARATOR " :: ") AS "Update",
    badgeid
  FROM
      PhotoLoungePix
  WHERE
    conid=$conid AND
    includestatus="a"
  GROUP BY
    badgeid
EOD;

// Retrieve query
list($rows,$element_header,$element_array)=queryreport($query,$link,$title,$description,0);

// setup for viewing instead of printing
if ($print_p =="") {
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
}

// Add the Table of Pictures, and then the bio information, with an HR in between
for ($i=1; $i<=$rows; $i++) {
  // Start with an empty biostring, and printstring
  $printstring="";
  $biostring="";
  $bioinfo=getBioData($element_array[$i]['badgeid']);
  if (!empty($bioinfo['name_en-us_good_book_bio'])) {
    $biostring.=$bioinfo['name_en-us_good_book_bio'];
  } elseif (!empty($bioinfo['name_en-us_good_web_bio'])) {
    $biostring.=$bioinfo['name_en-us_good_web_bio'];
  } elseif (!empty($bioinfo['name_en-us_edited_book_bio'])) {
    $biostring.=$bioinfo['name_en-us_edited_book_bio'];
  } elseif (!empty($bioinfo['name_en-us_edited_web_bio'])) {
    $biostring.=$bioinfo['name_en-us_edited_web_bio'];
  } elseif (!empty($bioinfo['name_en-us_raw_book_bio'])) {
    $biostring.=$bioinfo['name_en-us_raw_book_bio'];
  } elseif (!empty($bioinfo['name_en-us_raw_web_bio'])) {
    $biostring.=$bioinfo['name_en-us_raw_web_bio'];
  }
  if (!empty($bioinfo['bio_en-us_good_book_bio'])) {
    $biostring.=$bioinfo['bio_en-us_good_book_bio'];
  } elseif (!empty($bioinfo['bio_en-us_good_web_bio'])) {
    $biostring.=$bioinfo['bio_en-us_good_web_bio'];
  }
  if (!empty($bioinfo['uri_en-us_good_book_bio'])) {
    $biostring.=" URL: ".$bioinfo['uri_en-us_good_book_bio'];
  } elseif (!empty($bioinfo['uri_en-us_good_web_bio'])) {
    $biostring.=" URL: ".$bioinfo['uri_en-us_good_web_bio'];
  }
  if (!empty($bioinfo['pronoun_en-us_good_book_bio'])) {
    $biostring.=" Preferred Pronoun: ".$bioinfo['pronoun_en-us_good_book_bio'];
  } elseif (!empty($bioinfo['pronoun_en-us_good_web_bio'])) {
    $biostring.=" Preferred Pronoun: ".$bioinfo['pronoun_en-us_good_web_bio'];
  }


  $printstring.=$element_array[$i]["Pictures"];
  $printstring.="\n<P>$biostring</P>\n";
  // Present the body of the page, or put to a pdf file.
  if ($print_p == "") {
    echo "$printstring";
    if (may_I("SuperPhotoRev")) {
      echo "<P>Updates: " . $element_array[$i]['Update'] . " :: ";
      echo "<A HREF=\"StaffEditBios.php?qno=1&badgeid=".$element_array[$i]['badgeid']."&badgeids=".$element_array[$i]['badgeid']."\">";
      echo "edit bio</A></P>\n";
    }
    echo "<HR>\n";
  } else {
    $pdf->AddPage();
    $pdf->writeHTML($printstring, true, false, true, false, '');
  }
}

// Present the footer, or write it to a file.
if ($print_p == "") {
  correct_footer();
} else {
  $pdf->Output('PhotoLoungeContactSheet-'.$_SESSION['conname'].'.pdf', 'I');
}

