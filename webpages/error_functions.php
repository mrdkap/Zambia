<?php
require_once('CommonCode.php');

// Render Error reporting
function RenderError($title,$message_error) {
  $HeaderTemplateFile="../Local/HeaderTemplate.html";
  $FooterTemplateFile="../Local/FooterTemplate.html";

  if ($_SESSION['role'] == "Staff") {
    global $debug;
    topofpagereport($title,"","","",$message_error);
    if (isset($debug)) echo $debug."<BR>\n";
  } elseif (($_SESSION['role'] == "Brainstorm") or
	    ($_SESSION['role'] == "Vendor") or
	    ($_SESSION['role'] == "Participant") or
	    ($_SESSION['role'] == "Posting")) {
    topofpagereport($title,"","","",$message_error);
  } else {
    // do something generic here (though this might be way too generic)
    // better to output some error message reliably than none at all
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
    echo "<HTML xmlns=\"http://www.w3.org/1999/xhtml\">\n";
    echo "  <HEAD>\n";
    echo "    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">\n";
    echo "    <TITLE>$title</TITLE>\n";
    if (file_exists($HeaderTemplateFile)) {
      readfile($HeaderTemplateFile);
    } else {
      echo "    <link rel=\"stylesheet\" href=\"Common.css\" type=\"text/css\">\n";
      echo "  </HEAD>\n";
      echo "  <BODY>\n";
    }
    echo "    <H1>Zambia &ndash; ".$_SESSION['conname']." </H1>\n";
    echo "    <H2>$title</H2>\n";
    echo "    <hr>\n";
    echo "    <P> An error occurred: </P>\n    ";
    echo $message_error;
    if (file_exists($FooterTemplateFile)) {
      readfile($FooterTemplateFile);
    }
    echo "  </BODY>";
    echo "</HTML>";
  }
  correct_footer();
}

?>
