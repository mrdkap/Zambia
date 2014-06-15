<?php
require_once('CommonCode.php');

// Render Error reporting
function RenderError($title,$message) {
  $HeaderTemplateFile="../Local/HeaderTemplate.html";
  $FooterTemplateFile="../Local/FooterTemplate.html";

  if ($_SESSION['role'] == "Brainstorm") {
    brainstorm_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
  } elseif ($_SESSION['role'] == "Vendor") {
    vendor_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
  } elseif ($_SESSION['role'] == "Participant") {
    participant_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
  } elseif ($_SESSION['role'] == "Staff") {
    global $debug;
    staff_header($title);
    if (isset($debug)) echo $debug."<BR>\n";
    echo "<P id=\"errmsg\">".$message."</P>\n";
  } elseif ($_SESSION['role'] == "Posting") {
    posting_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
  } else {
    // do something generic here (though this might be way too generic)
    // better to output some error message reliably than none at all
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml">
  <HEAD>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">
<?php
    echo "    <TITLE>$title</TITLE>\n";
    if (file_exists($HeaderTemplateFile)) {
      readfile($HeaderTemplateFile);
    } else {
?>
    <link rel="stylesheet" href="Common.css" type="text/css">
  </HEAD>
  <BODY>
<?php
    }
    echo "    <H1>Zambia &ndash; ".$_SESSION['conname']." </H1>\n";
    echo "    <H2>$title</H2>\n";
    echo "    <hr>\n";
    echo "    <P> An error occurred: </P>\n    ";
    echo $message;
    if (file_exists($FooterTemplateFile)) {
      readfile($FooterTemplateFile);
    }
    echo "  </BODY>";
    echo "</HTML>";
  }
  correct_footer();
}

?>
