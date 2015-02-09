<?php
require_once('Constants.php');
require_once('data_functions.php');
require_once('db_functions.php');
require_once('validation_functions.php');
require_once('php_functions.php');
require_once('error_functions.php');

global $link;
$ConKey=CON_KEY; // make it a variable so it can be substituted

//set_session_timeout();
session_start();
if (prepare_db()===false) {
  $message_error="Unable to connect to database.<BR>No further execution possible.";
  RenderError($title,$message_error);
  exit();
 };

// Establish the con info
$query= <<<EOF
SELECT
    conid,
    conname,
    constartdate,
    connumdays,
    conurl,
    conlogo,
    condefaultduration,
    condurationminutes,
    congridspacer,
    conallowkids,
    contotalsess,
    condailysess,
    conavailabilityrows,
    vendoremail,
    programemail
  FROM
      ConInfo
   LEFT JOIN (
      SELECT
          conid,
          group_concat(email) AS vendoremail
        FROM
            ConRoles
          JOIN UserHasConRole USING (conroleid)
          JOIN CongoDump USING (badgeid)
        WHERE
          conrolename like '%Vending%' AND
          conid=$ConKey) AS X USING (conid)
    LEFT JOIN (
      SELECT
          conid,
          group_concat(email) AS programemail
        FROM
            ConRoles
          JOIN UserHasConRole USING (conroleid)
          JOIN CongoDump USING (badgeid)
        WHERE
          conrolename like '%BrainstormCoord%' AND
          conid=$ConKey) AS Y USING (conid)
  WHERE
      conid=$ConKey
EOF;

// Retrieve query fail if database can't be found, and if there isn't just one result
if (($result=mysql_query($query,$link))===false) {
  $message_error="Error retrieving data from database<BR>\n";
  $message_error.=$query;
  RenderError($title,$message_error);
  exit();
}
if (0==($rows=mysql_num_rows($result))) {
  $message_error.="Database query did not return any rows.<BR>\n";
  $message_error.=$query;
  RenderError($title,$message_error);
  exit();
}
if ($rows > 1) {
  $message_error.="Too many results found.<BR>\n";
  $message_error.=$query;
  RenderError($title,$message_error);
  exit();
}

$ConInfo_array=mysql_fetch_assoc($result);
$ConInfo_array_keys=array_keys($ConInfo_array);
foreach ($ConInfo_array_keys as $element) {
  if ($_SESSION["$element"]=="") {$_SESSION["$element"]=$ConInfo_array["$element"];}
}

// Somewhere DOUBLE_SCHEDULE dissapeared.  Find it, and fix it.
// ADD newroomslots to coninfo
define("newroomslots",5); // number of rows at bottom of page for new schedule entries
$_SESSION['newroomslots']=5;

global $daymap;
for ($i=0;$i<$_SESSION['connumdays'];$i++) {
  $today=strtotime($_SESSION['constartdate'])+(86400 * $i); // 86400 seconds in a day
  $daymap['long'][$i+1]=strftime("%A",$today);
  $daymap['short'][$i+1]=strftime("%a",$today);
 }

if (isLoggedIn()==false and !isset($logging_in)) {
  $message="Session expired. Please log in again.";
  require ('login.php');
  exit();
 };

// function to generate a clickable tab.
// 'text' contains the text that should appear in the tab.
// 'usable' indicates whether the tab is usable.
//
// if the tab is usable, its background and foreground color will
// be determined by the 'usabletab' class.  when the mouse is over the tab
// the background and foreground colors of the tab will be determined
// by the 'mousedovertab' class.
//
// if the tab is not usable, the tab will use class 'unusabletab'

function maketab ($text,$usable,$url) {
  if ($usable) {
    echo '<SPAN class="usabletab" onmouseover="mouseovertab(this)" onmouseout="mouseouttab(this)">';
    echo '<IMG class="tabborder" SRC="images/leftCorner.gif" alt="&nbsp;">';
    echo '<A HREF="' . $url . '">' ;// XXX link needs to be quoted
    echo $text;                     // XXX needs to be quoted
    echo '<IMG class="tabborder" SRC="images/rightCorner.gif" alt="&nbsp;">';
    echo '</SPAN>';
  }
  else {
    echo '<SPAN class="unusabletab">';
    echo '<IMG class="tabborder" SRC="images/leftCorner.gif" alt="&nbsp;">';
    echo $text;                     // XXX needs to be quoted
    echo '<IMG class="tabborder" SRC="images/rightCorner.gif" alt="&nbsp;">';
    echo '</SPAN>';
  }
}

/* functions to put the headers in place.  Probably should be generalized more,
 than specifically pre-scripting it, the way we do. */

function posting_header ($title) {
  $ConName=$_SESSION['conname']; // make it a variable so it can be substituted
  $ConUrl=$_SESSION['conurl']; // make it a variable so it can be substituted
  $HeaderTemplateFile="../Local/HeaderTemplate.html";

  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/strict.dtd\">\n";
  echo "<html xmlns=\"http://www.w3.org/TR/xhtml1/transitional\">\n";
  echo "<head>\n";
  echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">\n";
  echo "  <title>Zambia -- $ConName -- $title</title>\n";
  echo "  <link rel=\"stylesheet\" href=\"Common.css\" type=\"text/css\">\n";
  if (file_exists($HeaderTemplateFile)) {
    readfile($HeaderTemplateFile);
    echo "<H2 class=\"head\" align=\"center\"><A HREF=\"http://$ConUrl\">Return</A> to the programming website</H2>\n";
    echo "<HR>\n";
    echo "<H1 class=\"head\" align=\"center\">$title</H1>\n";
  } else {
    echo "  <link rel=\"stylesheet\" href=\"Common.css\" type=\"text/css\">\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "<H2 class=\"head\"><A HREF=\"http://$ConUrl\">Return</A> to the programming website</H2>\n";
    echo "<hr>\n\n";
    echo "<H1 class=\"head\">$title</H1>\n";
  }
}

function staff_header ($title) {
  require_once ("javascript_functions.php");
  $ConName=$_SESSION['conname']; // make it a variable so it can be substituted
  $ConUrl=$_SESSION['conurl']; // make it a variable so it can be substituted

  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/strict.dtd\">\n";
  echo "<html xmlns=\"http://www.w3.org/TR/xhtml1/transitional\">\n";
  echo "<head>\n";
  echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">\n";
  echo "  <title>Zambia -- $ConName -- $title</title>\n";
  echo "  <link rel=\"stylesheet\" href=\"StaffSection.css\" type=\"text/css\">\n";
  javascript_for_edit_session();
  mousescripts();
  echo "</head>\n";
  echo "<body>\n";
  echo "<H1 class=\"head\">Zambia&ndash;The $ConName Scheduling Tool</H1>\n";
  echo "<H1 class=\"head\">Return to the <A HREF=\"http://$ConUrl\">$ConName</A> website</H1>\n";
  echo "<hr>\n\n";
  if (isset($_SESSION['badgeid'])) {
    echo "  <table class=\"tabhead\">\n    <tr class=\"tabrow\">\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Staff Overview",1,"StaffPage.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Available Reports",1,"genindex.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Manage Sessions",1,"StaffManageSessions.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Manage Participants &amp; Schedule",1,"StaffManageParticipants.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Printing",1,"PreconPrinting.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("TimeCards",1,"VolunteerCheckIn.php");
    echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
    maketab("Participant View",1,"welcome.php");
    if (may_I('Vendor')) {
      echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
      maketab("Vendor View",1,"VendorWelcome.php");
      }
    if (may_I('public_login')) {
      echo "</td>\n      <td class=\"tabblocks border0020\">\n          ";
      maketab("Brainstorm View",1,"BrainstormWelcome.php");
    }
    echo "</td>\n    </tr>\n  </table>\n";
    echo "<table class=\"header\">\n  <tr>\n    <td style=\"height:5px\">\n      </td>\n    </tr>\n";
    echo "  <tr>\n    <td>\n      <table width=\"100%\">\n";
    echo "        <tr>\n          <td width=\"425\">&nbsp;\n            </td>\n";
    echo "          <td class=\"Welcome\">Welcome ";
    echo $_SESSION['badgename'];
    echo "            </td>\n";
    echo "          <td><A class=\"logout\" HREF=\"logout.php\">&nbsp;Logout&nbsp;</A>\n            </td>\n";
    echo "          <td width=\"25\">&nbsp;\n            </td>\n          </tr>\n        </table>\n";
    echo "      </td>\n    </tr>\n";
  }
  echo "  </table>\n\n<H2 class=\"head\">$title</H2>\n";
  //  echo "Permissions: ".print_r($_SESSION['permission_set'])."\n";
}

function participant_header ($title) {
  require_once ("javascript_functions.php");
  global $badgeid;
  $ConName=$_SESSION['conname']; // make it a variable so it can be substituted
  $ConUrl=$_SESSION['conurl']; // make it a variable so it can be substituted

  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/strict.dtd\">\n";
  echo "<html xmlns=\"http://www.w3.org/TR/xhtml1/transitional\">\n";
  echo "<head>\n";
  echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">\n";
  echo "  <title>Zambia -- $ConName -- $title</title>\n";
  echo "  <link rel=\"stylesheet\" href=\"ParticipantSection.css\" type=\"text/css\">\n";
  mousescripts();
  echo "</head>\n";
  echo "<body>\n";
  echo "<H1 class=\"head\">Zambia&ndash;The $ConName Scheduling Tool</H1>\n";
  echo "<H1 class=\"head\">Return to the <A HREF=\"http://$ConUrl\">$ConName</A> website</H1>\n";
  echo "<hr>\n\n";
  if (isset($_SESSION['badgeid'])) {
    echo "<table class=\"tabhead\">\n";
    echo "  <col width=10%><col width=10%><col width=10%><col width=10%><col width=10%>\n";
    echo "  <col width=10%><col width=10%><col width=10%><col width=10%><col width=10%>\n";
    echo "  <tr class=\"tabrow\">\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Welcome", 1, "welcome.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("My Availability",may_I('my_availability'),"my_sched_constr.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("My Panel Interests",may_I('my_panel_interests'),"PartPanelInterests.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("My General Interests",may_I('my_gen_int_write'),"my_interests.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    if (may_I('Staff')) {
      maketab("Staff View",may_I('Staff'),"StaffPage.php");
    }
    echo "</td>\n  </tr>\n  <tr class=\"tabrows\">\n    <td class=\"tabblocks border0020 smallspacer\">&nbsp;";
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    echo "<!-- XXX this should have a may_I -->\n       ";
    maketab("My Profile",1,"my_contact.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Search Panels",may_I('search_panels'),"my_sessions1.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("My Schedule",may_I('my_schedule'),"MySchedule.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Submit a Proposal",may_I('my_suggestions_write'),"MyProposals.php");
    echo "</td>\n    <td class=\"tabblocks border0020 smallspacer\">&nbsp;";
    echo "</td>\n  </tr>\n</table>\n";
    echo "<table class=\"header\">\n  <tr>\n    <td style=\"height:5px\"></td>\n  </tr>\n";
    echo "  <tr>\n    <td>\n      <table width=\"100%\">\n";
    echo "        <tr>\n          <td width=\"425\">&nbsp;</td>\n";
    echo "          <td class=\"Welcome\">Welcome ";
    echo $_SESSION['badgename'];
    echo "            </td>\n";
    echo "          <td><A class=\"logout\" HREF=\"logout.php\">&nbsp;Logout&nbsp;</A></td>\n";
    echo "          <td width=\"25\">&nbsp;</td>\n        </tr>\n      </table>\n";
    echo "    </td>\n  </tr>\n";
  }
  echo "</table>\n\n<H2 class=\"head\">$title</H2>\n";
}

function brainstorm_header ($title) {
  require_once ("javascript_functions.php");
  $ConName=$_SESSION['conname']; // make it a variable so it can be substituted
  $ConUrl=$_SESSION['conurl']; // make it a variable so it can be substituted

  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/strict.dtd\">\n";
  echo "<html xmlns=\"http://www.w3.org/TR/xhtml1/transitional\">\n";
  echo "<head>\n";
  echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">\n";
  echo "  <title>Zambia -- $ConName -- $title</title>\n";
  echo "  <link rel=\"stylesheet\" href=\"BrainstormSection.css\" type=\"text/css\">\n";
  echo "  <meta name=\"keywords\" content=\"Questionnaire\">\n";
  javascript_for_edit_session();
  javascript_pretty_buttons();
  mousescripts();
  echo "</head>\n";
  echo "<body leftmargin=\"0\" topmargin=\"0\" marginheight=\"0\" marginwidth=\"0\">\n";
  echo "<H1 class=\"head\">Zambia&ndash;The $ConName Scheduling Tool</H1>\n";
  echo "<H1 class=\"head\">Return to the <A HREF=\"http://$ConUrl\">$ConName</A> website</H1>\n";
  echo "<hr>\n\n";
  if (isset($_SESSION['badgeid'])) {
    echo "<table class=\"tabhead\">\n";
    echo "  <col width=8%><col width=8%><col width=8%><col width=8%><col width=8%>\n";
    echo "  <col width=8%><col width=10%><col width=10%><col width=8%><col width=8%>\n";
    echo "  <col width=8%><col width=8%>\n";
    echo "  <tr class=\"tabrow\">\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Welcome",1,"BrainstormWelcome.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Suggest a Session",may_I('BrainstormSubmit'),"BrainstormCreateSession.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Search Sessions",1,"BrainstormReport.php?status=search");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Suggest a Presenter",may_I('BrainstormSubmit'),"BrainstormSuggestPresenter.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    if(may_I('Participant')) {
      maketab("Participants View",may_I('Participant'),"welcome.php");
    }
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    if(may_I('Staff')) {
      maketab("Staff View",may_I('Staff'),"StaffPage.php");
    }
    echo"  </tr>\n  <tr class=\"tabrows\">\n    <td class=\"tabblocks border0020\" colspan=12>\n         View sessions proposed to date:</td>\n  </tr>";
    echo "</td>\n  </tr>\n  <tr class=\"tabrows\">\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("All Proposals",1,"BrainstormReport.php?status=all");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("New (Unseen)",1,"BrainstormReport.php?status=unseen");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Reviewed",1,"BrainstormReport.php?status=reviewed");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Likely to Occur",1,"BrainstormReport.php?status=likely");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=4>\n       ";
    maketab("Scheduled",1,"BrainstormReport.php?status=scheduled");
    echo "</td>\n  </tr>\n</table>\n";
    echo "<table class=\"header\">\n  <tr>\n    <td style=\"height:5px\"></td>\n  </tr>\n";
    echo "  <tr>\n    <td>\n      <table width=\"100%\">\n";
    echo "        <tr>\n          <td width=\"425\">&nbsp;</td>\n";
    echo "          <td class=\"Welcome\">Welcome ";
    echo $_SESSION['badgename'];
    echo "            </td>\n";
    echo "          <td><A class=\"logout\" HREF=\"logout.php\">&nbsp;Logout&nbsp;</A></td>\n";
    echo "          <td width=\"25\">&nbsp;</td>\n        </tr>\n      </table>\n";
    echo "    </td>\n  </tr>\n";
  }
  echo "</table>\n\n<H2 class=\"head\">$title</H2>\n";
}

function vendor_header ($title) {
  require_once ("javascript_functions.php");
  $ConName=$_SESSION['conname']; // make it a variable so it can be substituted
  $ConUrl=$_SESSION['conurl']; // make it a variable so it can be substituted

  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/strict.dtd\">\n";
  echo "<html xmlns=\"http://www.w3.org/TR/xhtml1/transitional\">\n";
  echo "<head>\n";
  echo "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">\n";
  echo "  <title>Zambia -- $ConName -- $title</title>\n";
  echo "  <link rel=\"stylesheet\" href=\"BrainstormSection.css\" type=\"text/css\">\n";
  echo "  <meta name=\"keywords\" content=\"Questionnaire\">\n";
  javascript_for_edit_session();
  javascript_pretty_buttons();
  mousescripts();
  echo "</head>\n";
  echo "<body leftmargin=\"0\" topmargin=\"0\" marginheight=\"0\" marginwidth=\"0\">\n";
  echo "<H1 class=\"head\">Zambia&ndash;The $ConName Scheduling Tool</H1>\n";
  echo "<H1 class=\"head\">Return to the <A HREF=\"http://$ConUrl\">$ConName</A> website</H1>\n";
  echo "<hr>\n\n";
  if (isset($_SESSION['badgeid'])) {
    echo "<table class=\"tabhead\">\n";
    echo "  <col width=8%><col width=8%><col width=8%><col width=8%><col width=8%>\n";
    echo "  <col width=8%><col width=10%><col width=10%><col width=8%><col width=8%>\n";
    echo "  <col width=8%><col width=8%>\n";
    echo "  <tr class=\"tabrow\">\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Welcome",1,"VendorWelcome.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("List",1,"VendorSearch.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    if (may_I('Vendor')) {
      maketab("Update",may_I('Vendor'),"VendorSubmitVendor.php");
    } else {
      maketab("New Vendor",may_I('BrainstormSubmit'),"VendorSubmitVendor.php");
    }
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    maketab("Apply",may_I('vendor_apply'),"VendorApply.php");
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    echo "</td>\n    <td class=\"tabblocks border0020\" colspan=2>\n       ";
    if (may_I('Staff')) {
      maketab("Staff View",may_I('Staff'),"StaffPage.php");
    }
    echo "</td>\n  </tr>\n</table>\n";
    echo "<table class=\"header\">\n  <tr>\n    <td style=\"height:5px\"></td>\n  </tr>\n";
    echo "  <tr>\n    <td>\n      <table width=\"100%\">\n";
    echo "        <tr>\n          <td width=\"425\">&nbsp;</td>\n";
    echo "          <td class=\"Welcome\">Welcome ";
    echo $_SESSION['badgename'];
    echo "            </td>\n";
    echo "          <td><A class=\"logout\" HREF=\"logout.php\">&nbsp;Logout&nbsp;</A></td>\n";
    echo "          <td width=\"25\">&nbsp;</td>\n        </tr>\n      </table>\n";
    echo "    </td>\n  </tr>\n";
  }
  echo "</table>\n\n<H2 class=\"head\">$title</H2>\n";
}

/* Top of page reporting, simplified by the foo_header functions
 for HTML pages.  It takes the title, description and any
 additional information, and puts it all in the right place
 depending on the SESSION variable.*/
function topofpagereport ($title,$description,$info) {
  if ($_SESSION['role'] == "Brainstorm") {
    brainstorm_header($title);
  }
  elseif ($_SESSION['role'] == "Vendor") {
    vendor_header($title);
  }
  elseif ($_SESSION['role'] == "Participant") {
    participant_header($title);
  }
  elseif ($_SESSION['role'] == "Staff") {
    staff_header($title);
  }
  elseif ($_SESSION['role'] == "Posting") {
    posting_header($title);
  }
  date_default_timezone_set('US/Eastern');
  echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
  echo $description;
  echo $info;
  if ($message_error!="") {
    echo "<P class=\"errmsg\">$message_error</P>\n";
  }
  if ($message!="") {
    echo "<P class=\"regmsg\">$message</P>\n";
  }
}

/* Top of page reporting, for CSV pages.  It takes only the filename
 as an input, and spits out the CSV headers. */
function topofpagecsv ($filename) {
  header("Expires: 0");
  header("Cache-control: private");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Description: File Transfer");
  header("Content-Type: text/csv");
  header("Content-disposition: attachment; filename=$filename");
}

/* Footer choice, for html pages.  Select the correct footer,
 dependant on role.  This could probably just have the above footer
 functions, rolled into this, for simplicity sake. */
function correct_footer () {
  if ($_SESSION['role'] == "Brainstorm") {
    echo "<hr>\n<P>If you would like assistance using this tool, or ";
    echo "if you would like to communicate an idea that you cannot fit into this form, please contact ";
    echo "<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A>.</P>";
    include('google_analytics.php');
    echo "\n\n</body>\n</html>\n";
  } elseif ($_SESSION['role'] == "Vendor") {
    echo "<hr>\n<P>If you would like assistance using this tool, please contact ";
    echo "<A HREF=\"mailto:".$_SESSION['vendoremail']."\">".$_SESSION['vendoremail']."</A>.  ";
    include('google_analytics.php');
    echo "\n\n</body>\n</html>\n";
  } elseif ($_SESSION['role'] == "Participant") {
    echo "<hr>\n<P>If you need help or to tell us something that doesn't fit here, please email ";
    echo "<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A>.\n</P>";
    include('google_analytics.php');
    echo "\n\n</body>\n</html>\n";
  } elseif ($_SESSION['role'] == "Staff") {
    echo "<hr>\n<P>If you would like assistance using this tool or you would like to communicate an";
    echo " idea that you cannot fit into this form, please contact ";
    echo "<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A>.\n</P>";
    include ('google_analytics.php');
    echo "\n\n</body>\n</html>\n";
  } elseif ($_SESSION['role'] == "Posting") {
    $FooterTemplateFile="../Local/FooterTemplate.html";

    if (file_exists($FooterTemplateFile)) {
      readfile($FooterTemplateFile);
    } else {
      echo "<hr>\n<P>If you have questions or wish to communicate an idea, please contact ";
      echo "<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A>.\n</P>";
    }
    include ('google_analytics.php');
    echo "\n\n</body>\n</html>\n";
  }
}

/* Produce the HTML body version of the information gathered in tables.
 It takes 4 inputs, the number of rows, the header array, the elements
 that go into the table, and if this table is the last thing on a page.
 the switch for the close on how it is called doesn't quite work yet,
 and might want to be simplified out, depending on the calling page to
 do the right thing, dropping it to 3 variables. */
function renderhtmlreport ($startrows,$endrows,$header_array,$element_array) {
  $headers="";
  foreach ($header_array as $header_name) {
    $headers.="<TH>";
    $headers.=$header_name;
    $headers.="</TH>\n";
  }
  $htmlstring ="<TABLE BORDER=1>";
  $htmlstring.="<TR>" . $headers . "</TR>";
  for ($i=$startrows; $i<=$endrows; $i++) {
    $htmlstring.="<TR>";
    foreach ($header_array as $header_name) {
      $htmlstring.="<TD>";
      $htmlstring.=$element_array[$i][$header_name];
      $htmlstring.="</TD>\n";
    }
    $htmlstring.="</TR>\n";
  }
  $htmlstring.="</TABLE>";
  return($htmlstring);
}

/* Produce the CSV body version of the information gathered in tables.
 It takes in four variables, the start row, the number of rows, the
 header array, and the elements that go in the table.  It then strips
 out all of the unwanted characters (html tags, extraneous returns,
 and other bits) and outputs the comma seperated information.*/
function rendercsvreport ($startrows,$endrows,$header_array,$element_array) {
  $headers="";
  $spacestr=array('\\n','\n','\\r','\r','&nbsp;');
  $newstr=array(' ',' ',' ',' ',' ');
  foreach ($header_array as $header_name) {
    $headers.="\"";
    $headers.=strip_tags(trim(str_replace($spacestr,$newstr,$header_name)));
    $headers.="\",";
  }
  $headers = substr($headers, 0, -1);
  $csvstring ="$headers\n";
  for ($i=$startrows; $i<=$endrows; $i++) {
    $rowinfo="";
    foreach ($header_array as $header_name) {
      $rowinfo.="\"";
      $rowinfo.=strip_tags(trim(str_replace($spacestr,$newstr,$element_array[$i][$header_name])));
      $rowinfo.="\",";
    }
    $rowinfo=substr($rowinfo, 0, -1);
    $csvstring.="$rowinfo\n";
  }
  return($csvstring);
}

/* This function presumes multiple calls on the same array informaition.
 It takes in 4 elements, the start and end row for a table, of the series
 of tables, the headers which go in every table, and the full array, from
 which the subset is used.  It then prints them nicely. */
function rendergridreport ($startrows,$endrows,$header_array,$element_array) {
  $headers="";
  foreach ($header_array as $header_name) {
    $headers.="<TH class=\"border2222\">";
    $headers.=$header_name;
    $headers.="</TH>\n";
  }
  $gridstring="<P><TABLE cellspacing=0 border=1 class=\"border1111\">";
  $gridstring.="<TR>" . $headers . "</TR>";
  for ($i=$startrows; $i<=$endrows; $i++) {
    $gridstring.="<TR>";
    foreach ($header_array as $header_name) {
      $gridstring.=$element_array[$i][$header_name];
    }
    $gridstring.="</TR>\n";
  }
  $gridstring.="</TABLE></P>";
  return($gridstring);
}

/* Produces the Precis style report of the information passed in.
   It takes in four variables, the start row, the number of rows,
   the header array (not yet used, but should be) and the array
   of elements that are presented.
   The array of elements expects (but does not require):
   sessionid, conid, trackname, typename, title, duration,
   estatten, desc_good_web, desc_good_book, persppartinfo, and
   proposer
   This goes hand in hand with the query produced in the function:
   retrieve_select_from_db
   This should be expanded to work with the MySchedule page as well. */
function renderprecisreport ($startrows,$endrows,$header_array,$element_array) {
  $printstring ="<hr>\n";
  $printstring.="<TABLE>\n";
  $printstring.="   <COL><COL><COL><COL><COL>\n";
  for ($i=$startrows; $i<=$endrows; $i++) {
    $rowspan=1;
    if (!empty($element_array[$i]['desc_good_web'])) {$rowspan++;}
    if (!empty($element_array[$i]['desc_good_book'])) {$rowspan++;}
    if (!empty($element_array[$i]['persppartinfo'])) {$rowspan++;}
    $printstring.="<TR>\n  <TD rowspan=$rowspan class=\"border0000\" id=\"sessidtcell\"><b>";
    if ((may_I('Staff')) and (!empty($element_array[$i]['sessionid']))) {
      $printstring.="<A HREF=\"StaffAssignParticipants.php?selsess=".$element_array[$i]['sessionid']."\">".$element_array[$i]['sessionid']."</A>";
    }
    $printstring.="&nbsp;&nbsp;</TD>\n";
    if (may_I('Staff')) {
      $printstring.="  <TD class=\"border0000\"><b>";
      if (!empty($element_array[$i]['proposer'])) {
	$printstring.=$element_array[$i]['proposer'];
      }
      if (!empty($element_array[$i]['estatten'])) {
	$printstring.="</b> (Count: ".$element_array[$i]['estatten'].") <b>";
      }
      if ($element_array[$i]['conid'] != $_SESSION['conid']) {
	$printstring.=" <A HREF=\"MyMigrations.php?sessionid=".$element_array[$i]['sessionid']."&conid=".$element_array[$i]['conid']."\">Migrate</A></b></TD>\n";
      } else {
	$printstring.="</b></TD>\n";
      }
      $printstring.="  <TD class=\"border0000\"><b>";
    } else {
      $printstring.="  <TD colspan=2 class=\"border0000\"><b>";
    }
    if (!empty($element_array[$i]['trackname'])) {
      $printstring.=$element_array[$i]['trackname'];
    }
    $printstring.="</TD>\n  <TD class=\"border0000\"><b>";
    if (!empty($element_array[$i]['typename'])) {
      $printstring.=$element_array[$i]['typename'];
    }
    $printstring.="</TD>\n  <TD class=\"border0000\"><b>";
    if (!empty($element_array[$i]['title'])) {
      if ((may_I('Staff')) and (!empty($element_array[$i]['sessionid']))){
	$printstring.="<A HREF=\"EditSession.php?id=".$element_array[$i]['sessionid']."\">".htmlspecialchars($element_array[$i]['title'],ENT_NOQUOTES)."</A>";
      } else {
	$printstring.=htmlspecialchars($element_array[$i]['title'],ENT_NOQUOTES);
      }
    }
    $printstring.="&nbsp;&nbsp;</TD>\n  <TD class=\"border0000\"><b>";
    if (!empty($element_array[$i]['duration'])) {
      $printstring.=$element_array[$i]['duration'];
    }
    $printstring.="</TD>\n</TR>\n";
    if (!empty($element_array[$i]['desc_good_web'])) {
      $printstring.="<TR><TD colspan=6 class=\"border0010\">Web: ".htmlspecialchars($element_array[$i]['desc_good_web'],ENT_NOQUOTES)."</TD></TR>\n";
    }
    if (!empty($element_array[$i]['desc_good_book'])) {
      $printstring.="<TR><TD colspan=6 class=\"border0010\">Book: ".htmlspecialchars($element_array[$i]['desc_good_book'],ENT_NOQUOTES)."</TD></TR>\n";
    }
    if (!empty($element_array[$i]['persppartinfo'])) {
      $printstring.="<TR><TD colspan=6 class=\"border0010\">".htmlspecialchars($element_array[$i]['persppartinfo'],ENT_NOQUOTES)."</TD></TR>\n";
    }
    $printstring.="<TR><TD colspan=6 class=\"border0020\">&nbsp;</TD></TR>\n";
    $printstring.="<TR><TD colspan=6 class=\"border0000\">&nbsp;</TD></TR>\n";
  }
  $printstring.="</TABLE>\n";

  return($printstring);
}

/* This function renders the Schedule, Description, Tracks, Rooms, and Bios
   in their various forms, for their various audiences.
   It takes 5 elements:
   format - One of desc, bios, sched, tracks, trtime, rooms
   header_break - If there is sectional breaks, what it breaks on, otherwise empty
   single_line_p [T,F] - If the sched line is a single line, or is more full
   elements - the count of the elements to loop over
   element_array - the array of elements to loop over

   element_array should at least contain Title, and whatever is set in header_break,
   if header_break is not empty.  It may also include: Bio, Participants, Track,
   Start Time, Duration, Room, iCal, Feedback, and Description.  Bio only needs to
   be included if the format is set to bios.
 */
function renderschedreport ($format,$header_break,$single_line_p,$elements,$element_array) {
  $sched="<DL>\n";

  $header="";
  for ($i=1; $i<=$elements; $i++) {
    if (($header_break != "") and ($element_array[$i][$header_break] != $header)) {
      $header=$element_array[$i][$header_break];
      if ($format != "bios") {
	$sched.=sprintf("</DL><P>&nbsp;</P>\n<HR><H3>%s</H3>\n<DL>\n",$header);
      } else {
	$sched.=$element_array[$i]['Bio'];
      }
    }
    if ($single_line_p != "T") { $sched.="<P>"; }
    $sched.=sprintf("<DT><B>%s</B>",$element_array[$i]['Title']);
    if (($single_line_p == "T") and
	($format != "bios") and
	(!empty($element_array[$i]['Participants'])) and
	($element_array[$i]['Participants'] != " ")) {
      $sched.=sprintf("&mdash;%s",$element_array[$i]['Participants']);
    }
    if (($format != "tracks") and ($format != "trtime") and (!empty($element_array[$i]['Track']))) {
      $sched.=sprintf("&mdash;%s",$element_array[$i]['Track']);
    }
    if (($format != "sched") and (!empty($element_array[$i]['Start Time']))) {
      $sched.=sprintf("&mdash;%s",$element_array[$i]['Start Time']);
    }
    if (!empty($element_array[$i]['Duration'])) {
      $sched.=sprintf("&mdash;%s",$element_array[$i]['Duration']);
    }
    if (($format != "rooms") and (!empty($element_array[$i]['Room']))) {
      $sched.=sprintf("&mdash;%s",$element_array[$i]['Room']);
    }
    if ((isset($element_array[$i]['iCal'])) and (!empty($element_array[$i]['iCal']))) {
      $sched.=sprintf("&mdash;%s",$element_array[$i]['iCal']);
    }
    if ((isset($element_array[$i]['Estatten'])) and (!empty($element_array[$i]['Estatten']))) {
      $sched.=sprintf("&mdash;(Count %s)",$element_array[$i]['Estatten']);
    }
    if ((isset($element_array[$i]['Feedback'])) and (!empty($element_array[$i]['Feedback']))) {
      $sched.=sprintf("&mdash;%s",$element_array[$i]['Feedback']);
    }
    if ($single_line_p != "T") {
      if (!empty($element_array[$i]['Description'])) {
	$sched.=sprintf("</DT>\n<DD><P>%s",$element_array[$i]['Description']);
      }
      if ((!empty($element_array[$i]['Participants'])) and
	  ($element_array[$i]['Participants'] != " ")) {
	$sched.=sprintf("</P></DD>\n<DD><i>%s</i></DD>\n",$element_array[$i]['Participants']);
      } else {
	$sched.="</P></DD>\n";
      }
    } else {
      $sched.="</DT>\n";
    }
    if (($element_array[$i][$header_break] != $element_array[$i + 1][$header_break]) and
	($format == "bios")) {
      $sched.="</DL>\n";
      if ((isset($element_array[$i]['istable'])) and ($element_array[$i]['istable'] > 0)) {
	$sched.="    </TD>\n  </TR>\n</TABLE>\n";
      }
    }
  }
  if ($format != "bios") {
    $sched.="</DL>\n";
  }
  return($sched);
}

/* Pull the information from the database for a report.  This should be
 checked with, and possibly unified with other functions in db_functions
 file.  It takes the query and link to do the pull, title and description
 in case there is an error, or just no information, and a reportid, so
 the report can be edited if there is a query error in the report. */
function queryreport ($query,$link,$title,$description,$reportid) {
  mysql_query("SET group_concat_max_len = 9216;",$link);
  if (($result=mysql_query($query,$link))===false) {
    $message="<P>Error retrieving data from database.</P>\n<P>";
    if ($reportid !=0) {
      $message.="Edit Report <A HREF=EditReport.php?selreport=$reportid>$reportid</A></P>\n<P>";
    }
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }
  if (0==($rows=mysql_num_rows($result))) {
    $header_array[0]="This report retrieved no results matching the criteria.";
    $element_array[$header_array[0]]='';
    $rows=0;
    return array ($rows,$header_array,$element_array);
    /* $message="$description\n<P>This report retrieved no results matching the criteria.</P>\n";
    RenderError($title,$message);
    exit(); */
  }
  for ($i=1; $i<=$rows; $i++) {
    $element_array[$i]=mysql_fetch_assoc($result);
  }
  $header_array=array_keys($element_array[1]);
  return array ($rows,$header_array,$element_array);
}

/* Show a list of participants to select from, generated from all participants.
 Each list is ordered by the sorting key, for html-based and visual-based
 searching. */
function select_participant ($selpartid, $limit, $returnto) {
  $conid=$_SESSION['conid'];
  global $link;

/* Get all the Permission Roles */
  $query = <<<EOD
SELECT
    permrolename,
    notes
  FROM
      PermissionRoles
  WHERE
    permroleid > 1
EOD;

  list($permrole_rows,$permrole_header_array,$permrole_array)=queryreport($query,$link,"Broken Query - select_participant - PermissionRoles",$query,0);

  // Empty Title Switch to begin with.
  $TitleSwitch="";

  /* Attempt to establish default graph based on permissions */
  for ($i=1; $i<=$permrole_rows; $i++) {
    if (may_I($permrole_array[$i]['permrolename'])) {
      $permrolecheck_array[]="'".$permrole_array[$i]['permrolename']."'";
    }
  }

  $additional_permission_array=array('SuperProgramming', 'SuperLiaison', 'Liaison');

  foreach ($additional_permission_array as $perm) {
    if (may_I($perm)) {
      $permrolecheck_array[]="'Participant'";
    }
  }
  $permrolecheck_string=implode(",",$permrolecheck_array);

  if ($limit=="ALL") {
    $limittables='';
    $limitwhere='';
    $emailaddr=" ',email,'";
  } elseif ($limit!='') {
    $query=<<<EOD
SELECT
    interestedtypeid
  FROM
      InterestedTypes
  WHERE
    interestedtypename=$limit
EOD;

    list($interested_rows,$interested_header_array,$interested_array)=queryreport($query,$link,"Broken Query - select_participant - InterestedTypes",$query,0);

    // should only return one thing
    $interestedtypeid=$interested_array[1]['interestedtypeid'];
    if ($interestedtypeid=='') {
      $message.=$query." Returned an empty array";
      RenderError("Broken Query - select_participant - InterestedTypes", $message);
      exit;
    }
    $limittables ="    JOIN UserHasPermissionRole USING (badgeid)\n";
    $limittables.="    JOIN PermissionRoles USING (permroleid)\n";
    $limittables.="    JOIN Interested I USING (badgeid,conid)\n";
    $limitwhere ="  WHERE\n";
    $limitwhere.="    interestedtypeid=$interestedtypeid AND\n";
    $limitwhere.="    conid=$conid AND\n";
    $limitwhere.="    permrolename in ($permrolecheck_string)\n";
    $emailaddr="";
  } else {
    $limittables ="    JOIN UserHasPermissionRole USING (badgeid)\n";
    $limittables.="    JOIN PermissionRoles USING (permroleid)\n";
    $limitwhere ="  WHERE\n";
    $limitwhere.="    conid=$conid AND\n";
    $limitwhere.="    permrolename in ($permrolecheck_string)\n";
    $emailaddr="";
  }

  // lastname, firstname (badgename/pubsname) - partid
  $query0=<<<EOD
SELECT
    DISTINCT badgeid,
    concat(lastname,', ',firstname,' (',badgename,'/',pubsname,')$emailaddr - ',badgeid) AS pname
  FROM
      Participants
    JOIN CongoDump USING (badgeid)
    $limittables
    $limitwhere
  ORDER BY
    lastname
EOD;

  // firstname lastname (badgename/pubsname) - partid
  $query1=<<<EOD
SELECT
    DISTINCT badgeid,
    concat(firstname,' ',lastname,' (',badgename,'/',pubsname,')$emailaddr - ',badgeid) AS pname
  FROM
      Participants
    JOIN CongoDump USING (badgeid)
    $limittables
    $limitwhere
  ORDER BY
    firstname
EOD;

  // pubsname/badgename (lastname, firstname) - partid
  $query2=<<<EOD
SELECT
    DISTINCT badgeid,
    concat(pubsname,'/',badgename,' (',lastname,', ',firstname,')$emailaddr - ',badgeid) AS pname
  FROM
      Participants
    JOIN CongoDump USING (badgeid)
    $limittables
    $limitwhere
  ORDER BY
    pubsname
EOD;

  // pubsname/badgename (lastname, firstname) - partid
  $query3=<<<EOD
SELECT
    DISTINCT badgeid,
    concat(email,": ",pubsname,'/',badgename,' (',lastname,', ',firstname,') - ',badgeid) AS pname
  FROM
      Participants
    JOIN CongoDump USING (badgeid)
    $limittables
    $limitwhere
  ORDER BY
    email
EOD;

  // Now give the choices
  echo "<FORM name=\"selpartform\" method=POST action=\"".$returnto."\">\n";
  echo "<DIV><LABEL for=\"partidl\">Select Participant (Lastname)</LABEL>\n";
  echo "<SELECT name=\"partidl\">\n";
  populate_select_from_query($query0, $selpartid, "Select Participant (Lastname)", true);
  echo "</SELECT></DIV>\n";
  echo "<DIV><LABEL for=\"partidf\">Select Participant (Firstname)</LABEL>\n";
  echo "<SELECT name=\"partidf\">\n";
  populate_select_from_query($query1, $selpartid, "Select Participant (Firstname)", true);
  echo "</SELECT></DIV>\n";
  echo "<DIV><LABEL for=\"partidp\">Select Participant (Pubsname) </LABEL>\n";
  echo "<SELECT name=\"partidp\">\n";
  populate_select_from_query($query2, $selpartid, "Select Participant (Pubsname)", true);
  echo "</SELECT></DIV>\n";
  if ($limit=='ALL') {
    echo "<DIV><LABEL for=\"partide\">Select Participant (Email Address) </LABEL>\n";
    echo "<SELECT name=\"partide\">\n";
    populate_select_from_query($query3, $selpartid, "Select Participant (Email Address)", true);
    echo "</SELECT></DIV>\n";
  }
  echo "<P>&nbsp;\n";
  echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Submit</BUTTON></DIV>\n";
  echo "</FORM>\n";
}

/* This takes the trackidlist, statusidlist, typeidlist, and
   sessionidlist, and returns a query for renderprecisreport
   by producing:
   sessionid, trackname, typename, title, duration, estatten,
   desc_good_web, desc_good_book, persppartinfo, and proposer
   descriptionlang: Only using "en-us" for now. */
function retrieve_select_from_db ($trackidlist,$statusidlist,$typeidlist,$sessionidlist,$prevcon) {
  require_once('validation_functions.php');
  $conid=$_SESSION['conid'];

  if (validate_conid($prevcon)) {$conid=$prevcon;}

  $query=<<<EOD
SELECT
    sessionid,
    conid,
    trackname,
    typename,
    concat(title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web))) AS title,
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS duration,
    estatten,
    desc_good_web,
    desc_good_book,
    persppartinfo,
    pubsname as proposer
  FROM
      Sessions
    JOIN Tracks T USING (trackid)
    JOIN Types USING (typeid)
    JOIN SessionStatuses USING (statusid)
    JOIN Participants ON (suggestor=badgeid)
    JOIN (SELECT
        sessionid,
	descriptiontext as title_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='title' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') TGW USING (sessionid)
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as subtitle_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='subtitle' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') SGW USING (sessionid)
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as desc_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='description' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') DGW USING (sessionid)
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as desc_good_book
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='description' AND
	  biostatename='good' AND
	  biodestname='book' AND
	  descriptionlang='en-us') DGB USING (sessionid)
  WHERE
    conid=$conid
EOD;

// The following three lines are for debugging only
//    error_log("zambia - retrieve: trackidlist: $tracklist");
//    error_log("retrieve: statusid: $status");
//    error_log("retrieve: typeid: $type");

  if (($trackidlist!=0) and ($trackidlist!="")) {$query.=" AND trackid in ($trackidlist)";}
  if (($statusidlist!=0) and ($statusidlist!='')) {$query.=" AND statusid in ($statusidlist)";}
  if (($typeidlist!=0) and ($typeidlist!='')) {$query.=" AND typeid in ($typeidlist)";}
  if (($sessionidlist!=0) and ($sessionidlist!='')) {$query.=" AND sessionid in ($sessionidlist)";}
  if ($_SESSION['badgeid']==100) {$query.=" ORDER BY T.display_order,title";}

  return($query);
}

/* This function will output the page with the form to search for a session
   Variables:
   track: a number signifying the trackid in the Tracks table, or 0 - implying all
   status: a number signifying the statusid in the SessonStatuses table, or 0 - implying all
   type: a number signifying the typeid in the Types table, or 0 - implying all
   sessionid: a sessionid from the Sessions table, limited to this con-instance.
   The output varies depending on the permissions of the caller.
   Still missing:
   Title search
   Suggestor search */
function RenderSearchSession ($track,$status,$type,$sessionid) {

  // If staff, switch on the queries.  Else defaults to Brainstorm
  if (may_I('Staff')) {
    $query_track="SELECT trackid, trackname FROM Tracks ORDER BY display_order";
    $query_status="SELECT statusid, statusname FROM SessionStatuses ORDER BY display_order";
    $query_type="SELECT typeid, typename FROM Types ORDER BY display_order";
    $colspan=9;
  } elseif (may_I('Programming')) {
    $query_track="SELECT trackid, trackname FROM Tracks ORDER BY display_order";
    // Status should be fixed to "Scheduled"
    $statuschoice['scheduled']="Scheduled";
    // Types are limited to Panels and Classes, we don't have introducers for anything else
    $query_type="SELECT typeid,typename FROM Types WHERE typename in ('Panel','Class')";
    $colspan=9;
  } else {
    $query_track="SELECT trackid, trackname FROM Tracks WHERE selfselect=1 ORDER BY display_order";
    // Search left out of choices to avoid tail-biting.
    $statuschoice['all']="ANY";
    $statuschoice['unseen']="New (Unseen)";
    $statuschoice['reviewed']="Reviewed";
    $statuschoice['likely']="Likely to Occur";
    $statuschoice['scheduled']="Scheduled";
    $sessionid="";
    $colspan=4;
  }

  $returnstring.="    <INPUT type=\"hidden\" name=\"issearch\" value=\"1\">\n";
  $returnstring.="    <TABLE>\n";
  $returnstring.="      <TR>\n";

  //Track Info
  $returnstring.="        <TD>Track: </TD>\n";
  $returnstring.="        <TD><SELECT class=\"tcell\" name=\"track\">\n";
  $returnstring.=populate_select_from_query_inline($query_track, $track, "ANY", true);
  $returnstring.="            </SELECT></TD>\n";

  //Type Info, for Staff and Program volunteers
  if ((may_I('Staff')) OR (may_I('Programming'))) {
    $returnstring.="        <TD>Type:</TD>\n";
    $returnstring.="        <TD><SELECT name=\"type\">\n";
    $returnstring.=populate_select_from_query_inline($query_type, $type, "ANY", true);
    $returnstring.="            </SELECT></TD>\n";
  }

  //Status Info
  $returnstring.="        <TD>Status:</TD>\n";
  $returnstring.="        <TD><SELECT name=\"status\">\n";
  //different for Staff and Brainstorm
  if (may_I('Staff')) {
    $returnstring.=populate_select_from_query_inline($query_status, $status, "ANY", true);
  } else {
    foreach ($statuschoice as $key => $value) {
      $returnstring.="<OPTION value=\"$key\" ";
      if ($key==$_POST['status']) {
	$returnstring.="selected";
      }
      $returnstring.=">$value</OPTION>\n";
    }
  }
  $returnstring.="            </SELECT></TD>\n";

  //Sessionid, only for Staff
  if ((may_I('Staff')) OR (may_I('Programming'))) {
    $returnstring.="        <TD>Session ID:</TD>\n";
    $returnstring.="        <TD><INPUT type=\"text\" name=\"sessionid\" size=\"10\"";
    if ((isset($sessionid)) and (is_numeric($sessionid)) and ($sessionid > 0)) {
      $returnstring.=" value=\"$sessionid\"";
    }
    $returnstring.=">\n</TD>";
    $returnstring.="        <TD>(Leave blank for any)</TD>\n";
  }
  $returnstring.="      </TR>\n";

  //Submit button
  $returnstring.="      <TR><TD colspan=$colspan align=right><BUTTON type=submit value=\"search\">Search</BUTTON></TD></TR>\n";
  $returnstring.="    </TABLE>\n";

  return($returnstring);
}

/* Generic insert takes five variables: link, title, Table, array of elements, array of values. */
function submit_table_element ($link, $title, $table, $element_array, $value_array) {
  foreach ($element_array as $element) {$element_string.=mysql_real_escape_string(stripslashes($element)).",";}
  foreach ($value_array as $value) {$value_string.="'".mysql_real_escape_string(stripslashes($value))."',";}
  $element_string=substr($element_string,0,-1);
  $value_string=substr($value_string,0,-1);
  $query= "INSERT INTO $table ($element_string) VALUES ($value_string)";
  if (!mysql_query($query,$link)) {
    if (mysql_errno($link)==1062) {
      $message_error=$query."<BR>Error updating $table.  Record already exists.";
    } else {
      $message_error=$query."<BR>Error updating $table.  Database not updated.";
    }
    RenderError($title,$message_error);
    exit;
  }
  $message="Table $table updated successfully.<BR>";
  return($message);
}

/* Generic update takes six variables: link, title, Table, paired array of updates,
 which field to match on, and value of the match. */
function update_table_element ($link, $title, $table, $pairedvalue_array, $match_field, $match_value) {
  foreach ($pairedvalue_array as $pairedvalue) {$pairedvalue_string.=$pairedvalue.",";}
  $pairedvalue_string=substr($pairedvalue_string,0,-1);
  $query="UPDATE $table set $pairedvalue_string where $match_field = '$match_value'";
  if (!mysql_query($query,$link)) {
    $message_error=$query."<BR>Error updating $table.  Database not updated.";
    RenderError($title,$message_error);
    exit;
  }
  $message="Table $table updated successfully.<BR>";
  return($message);
}

/* Generic update takes five variables: link, title, Table, paired array of updates,
 and the where string. */
function update_table_element_extended_match ($link, $title, $table, $pairedvalue_array, $match_string) {
  foreach ($pairedvalue_array as $pairedvalue) {$pairedvalue_string.=$pairedvalue.",";}
  $pairedvalue_string=substr($pairedvalue_string,0,-1);
  $query="UPDATE $table set $pairedvalue_string where $match_string";
  if (!mysql_query($query,$link)) {
    $message_error=$query."<BR>Error updating $table.  Database not updated.";
    RenderError($title,$message_error);
    exit;
  }
  $message="Table $table updated successfully.<BR>";
  return($message);
}

/* Generic delete takes four variables: link, title, Table, and the where string.
  WARNING: Very Dangerous.  Your where string could wipe out the whole table. */
function delete_table_element ($link, $title, $table, $match_string) {
  $query="DELETE FROM $table where $match_string";
  if (!mysql_query($query,$link)) {
    $message_error=$query."<BR>Error updating $table.  Database not updated.";
    RenderError($title,$message_error);
    exit;
  }
  $message="Table $table updated successfully.<BR>";
  return($message);
}

/* unfrom/refrom fix so that queries can be set as values in the various database entries */
function unfrom ($transstring) {
   $badfrom = array("FROM", "From", "from");
   $goodfrom = array("UMFRAY", "Umfray", "umfray");
   return str_replace ($badfrom, $goodfrom, $transstring);
   }

function refrom ($transstring) {
   $badfrom = array("FROM", "From", "from");
   $goodfrom = array("UMFRAY", "Umfray", "umfray");
   return str_replace ($goodfrom, $badfrom, $transstring);
   }

/* Used to add a note on a participant as part of flow, and allowing for participant change. */
function submit_participant_note ($note, $partid) {
  global $link;

  $query = "INSERT INTO NotesOnParticipants (badgeid,rbadgeid,note,conid) VALUES ('";
  $query.=$partid."','";
  $query.=$_SESSION['badgeid']."','";
  $query.=mysql_real_escape_string($note)."','";
  $query.=$_SESSION['conid']."')";
  if (!mysql_query($query,$link)) {
    $message=$query."<BR>Error updating database with note.  Database not updated.";
    echo "<P class=\"errmsg\">".$message."\n";
    return;
  }
  $message="Database updated successfully with note.<BR>";
  echo "<P class=\"regmsg\">".$message."\n";
}

/* Pull the notes for a participant, in reverse order. */
// I'm no longer sure why the below is here ...
//"SELECT PR.pubsname, PB.pubsname, N.timestamp, N.note FROM NotesOnParticipants N, $ReportDB.Participants PR, $ReportDB.Participants PB WHERE N.rbadgeid=PR.badgeid AND N.badgeid=PB.badgeid;
function show_participant_notes ($partid) {
  global $link;

  $query = <<<EOD
SELECT
    timestamp as 'When',
    pubsname as 'Who',
    note as 'What Was Done',
    conid as "Con"
  FROM
      NotesOnParticipants N
    JOIN Participants P ON N.rbadgeid=P.badgeid
  WHERE
    N.badgeid=$partid
  ORDER BY
    timestamp DESC
EOD;
  list($rows,$header_array,$notes_array)=queryreport($query,$link,"Notes on Participant","","");
  echo renderhtmlreport(1,$rows,$header_array,$notes_array);
  correct_footer();
}

/* create_participant and edit_participant functions.  Need more doc. */
function create_participant ($participant_arr) {
  global $link;

  // Get the various length limits
  $limit_array=getLimitArray();

  // Get a set of bioinfo, not for the info, but for the arrays.
  $bioinfo=getBioData($_SESSION['badgeid']);

  // Test constraints.

  // Bios test moved into the add bios loop.

  // Too short/long name.
  $error_status=false;
  if (isset($limit_array['min']['web']['name'])) {
    $namemin=$limit_array['min']['web']['name'];
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) < $namemin) OR
	(strlen($participant_arr['badgename']) < $namemin) OR
	(strlen($participant_arr['pubsname']) < $namemin)) {
      $message_error="All name fields are required and minimum length is $namemin characters.  <BR>\n";
      return array ($message,$message_error);
    }
  }
  if (isset($limit_array['max']['web']['name'])) {
    $namemax=$limit_array['max']['web']['name'];
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) > $namemax) OR
	(strlen($participant_arr['badgename']) > $namemax) OR
	(strlen($participant_arr['pubsname']) > $namemax)) {
      $message_error="All name fields are required and maximum length is $namemax characters.  <BR>\n";
      return array ($message,$message_error);
    }
  }

  // Invalid email address.
  if (!is_email($participant_arr['email'])) {
    $message_error="Email address: ".$participant_arr['email']." is not valid.  <BR>\n";
    return array ($message,$message_error);
  }

  // Get next possible badgeid.
  // WAS: "SELECT MAX(badgeid) FROM Participants WHERE badgeid>='1'";
  $query = "SELECT badgeid FROM Participants ORDER BY ABS(badgeid) DESC LIMIT 1";
  $result=mysql_query($query,$link);
  if (!$result) {
    $message_error="Unrecoverable error updating database.  Database not updated.<BR>\n";
    $message_error.=$query;
    RenderError($title,$message_error);
    exit();
  }
  if (mysql_num_rows($result)!=1) {
    $message_error="Database query returned unexpected number of rows(1 expected).  Database not updated.<BR>\n";
    $message_error.=$query;
    RenderError($title,$message_error);
    exit();
  }
  $maxbadgeid=mysql_result($result,0);
  //error_log("Zambia: SubmitEditCreateParticipant.php: maxbadgeid: $maxbadgeid");
  sscanf($maxbadgeid,"%d",$x);
  $newbadgeid=sprintf("%d",$x+1); // convert to num; add 1; convert back to string

  // Create Participants entry.
  $element_array = array('badgeid', 'password', 'bestway', 'altcontact', 'prognotes', 'pubsname');
  $value_array=array($newbadgeid,
                     $participant_arr['password'],
                     $participant_arr['bestway'],
                     htmlspecialchars_decode($participant_arr['altcontact']),
                     htmlspecialchars_decode($participant_arr['prognotes']),
		     htmlspecialchars_decode($participant_arr['pubsname']));
  $message.=submit_table_element($link, $title, "Participants", $element_array, $value_array);

  // Add "Interested" if exists
  if (isset($participant_arr['interested']) AND ($participant_arr['interested']!='')) {
    $query ="UPDATE Interested SET ";
    $query.="interestedtypeid=".$participant_arr['interested']." ";
    $query.="WHERE badgeid=\"".$newbadgeid."\" AND conid=".$_SESSION['conid'];
    if (!mysql_query($query,$link)) {
      $message.=$query."<BR>Error updating Interested table.  Database not update.";
      echo "<P class=\"errmsg\">".$message."</P>\n";
      return;
    }
    ereg("Rows matched: ([0-9]*)", mysql_info($link), $r_matched);
    if ($r_matched[1]==0) {
      $element_array=array('conid','badgeid','interestedtypeid');
      $value_array=array($_SESSION['conid'], $newbadgeid, mysql_real_escape_string(stripslashes($participant_arr['interested'])));
      $message.=submit_table_element($link,$title,"Interested", $element_array, $value_array);
    } elseif ($r_matched[1]>1) {
      $message.="There might be something wrong with the table, there are multiple interested elements for this year.";
    }
  }

  // Add Bios.
  /* We are only updating the raw bios here, so only a 3-depth
   search happens on biolang, biotypename, and biodest. */
  $biostate='raw'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
  for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
    for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
      for ($l=0; $l<count($bioinfo['biodest_array']); $l++) {

	// Setup for keyname, to collapse all three variables into one passed name.
	$biotype=$bioinfo['biotype_array'][$i];
	$biolang=$bioinfo['biolang_array'][$j];
	// $biostate=$bioinfo['biostate_array'][$k];
	$biodest=$bioinfo['biodest_array'][$l];
	$keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";

	// Length-check the values.
	$biotext=stripslashes(htmlspecialchars_decode($participant_arr[$keyname]));
	if ((isset($limit_array['max'][$biodest][$biotype])) and (strlen($biotext)>$limit_array['max'][$biodest][$biotype])) {
	  $message.=ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	  $message.=" too long (".strlen($biotext)." characters), the limit is ".$limit_array['max'][$biodest][$biotype]." characters.";
	} elseif ((isset($limit_array['min'][$biodest][$biotype])) and (strlen($biotext)<$limit_array['min'][$biodest][$biotype])) {
	  $message.=ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	  $messaage.=" too short (".strlen($biotext)." characters), the limit is ".$limit_array['min'][$biodest][$biotype]." characters.";
	} else {
	  $message.=update_bio_element($link,$title,$biotext,$newbadgeid,$biotype,$biolang,$biostate,$biodest);
	}
      }
    }
  }

  // Create CongoDump entry.
  $element_array = array('badgeid', 'firstname', 'lastname', 'badgename', 'phone', 'email', 'postaddress1', 'postaddress2', 'postcity', 'poststate', 'postzip', 'regtype');
  $value_array=array($newbadgeid,
		     htmlspecialchars_decode($participant_arr['firstname']),
		     htmlspecialchars_decode($participant_arr['lastname']),
		     htmlspecialchars_decode($participant_arr['badgename']),
		     htmlspecialchars_decode($participant_arr['phone']),
		     htmlspecialchars_decode($participant_arr['email']),
		     htmlspecialchars_decode($participant_arr['postaddress1']),
		     htmlspecialchars_decode($participant_arr['postaddress2']),
		     htmlspecialchars_decode($participant_arr['postcity']),
		     htmlspecialchars_decode($participant_arr['poststate']),
		     htmlspecialchars_decode($participant_arr['postzip']),
		     htmlspecialchars_decode($participant_arr['regtype']));
  $message.=submit_table_element($link, $title, "CongoDump", $element_array, $value_array);

  // Assign permissions.
  if (empty($participant_arr['permroleid'])) {
    $message.="No permission role set.";
  } else {
    $query = "INSERT INTO UserHasPermissionRole (badgeid, permroleid, conid) VALUES ";
    foreach ($participant_arr['permroleid'] as $key => $value) {
      $query.="('".$newbadgeid."','".$key."','".$_SESSION['conid']."'),";
    }

    $query=rtrim($query,',');
    if (!mysql_query($query,$link)) {
      $message_error=$query."<BR>Error updating UserHasPermissionRole database.  Database not updated.";
      RenderError($title,$message_error);
      exit();
    }
  }

  // Assign con roles
  if (empty($participant_arr['conroleid'])) {
    $message.="No con role set.";
  } else {
    $query = "INSERT INTO UserHasConRole (badgeid, conroleid, conid) VALUES ";
    foreach ($participant_arr['conroleid'] as $key => $value) {
      $query.="('".$newbadgeid."','".$key."','".$_SESSION['conid']."'),";
    }

    $query=rtrim($query,',');
    if (!mysql_query($query,$link)) {
      $message_error=$query."<BR>Error updating UserHasConRole database.  Database not updated.";
      RenderError($title,$message_error);
      exit();
    }
  }

  // Submit a note about what was done.
  $element_array = array('badgeid', 'rbadgeid', 'note','conid');
  $value_array=array($newbadgeid,
                     $_SESSION['badgeid'],
                     mysql_real_escape_string(htmlspecialchars_decode($participant_arr['note'])),
		     $_SESSION['conid']);
  $message.=submit_table_element($link, $title, "NotesOnParticipants", $element_array, $value_array);

  // Make $message additive (.=) to get all the information
  $message="Database updated successfully with ".$participant_arr["badgename"].".<BR>";
  return array ($message,$message_error);
}

function edit_participant ($participant_arr) {
  global $link;

  // Get the various length limits
  $limit_array=getLimitArray();

  // Get a set of bioinfo, and compare below.
  $bioinfo=getBioData($participant_arr['partid']);

  // Test constraints.

  // Too short/long name.
  if (isset($limit_array['min']['web']['name'])) {
    $namemin=$limit_array['min']['web']['name'];
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) < $namemin) OR
	(strlen($participant_arr['badgename']) < $namemin) OR
	(strlen($participant_arr['pubsname']) < $namemin)) {
      $message_error="All name fields are required and minimum length is $namemin characters.  <BR>\n";
      echo "<P class=\"errmsg\">".$message_error."\n";
      return;
    }
  }
  if (isset($limit_array['max']['web']['name'])) {
    $namemax=$limit_array['max']['web']['name'];
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) > $namemax) OR
	(strlen($participant_arr['badgename']) > $namemax) OR
	(strlen($participant_arr['pubsname']) > $namemax)) {
      $message_error="All name fields are required and maximum length is $namemax characters.  <BR>\n";
      echo "<P class=\"errmsg\">".$message_error."\n";
      return;
    }
  }

  // Invalid email.
  if (!is_email($participant_arr['email'])) {
    $message_error="Email address: ".$participant_arr['email']." is not valid.  <BR>\n";
    echo "<P class=\"errmsg\">".$message_error."\n";
    return;
  }

  // Update Participants entry.
  $pairedvalue_array=array("bestway='".mysql_real_escape_string($participant_arr['bestway'])."'",
			   "altcontact='".mysql_real_escape_string($participant_arr['altcontact'])."'",
			   "prognotes='".mysql_real_escape_string(stripslashes($participant_arr['prognotes']))."'",
			   "pubsname='".mysql_real_escape_string(stripslashes($participant_arr['pubsname']))."'");
  $message.=update_table_element($link, $title, "Participants", $pairedvalue_array, "badgeid", $participant_arr['partid']);

  // Update CongoDump entry.
  $pairedvalue_array=array("firstname='".mysql_real_escape_string(stripslashes($participant_arr['firstname']))."'",
			   "lastname='".mysql_real_escape_string(stripslashes($participant_arr['lastname']))."'",
			   "badgename='".mysql_real_escape_string(stripslashes($participant_arr['badgename']))."'",
			   "phone='".mysql_real_escape_string($participant_arr['phone'])."'",
			   "email='".mysql_real_escape_string($participant_arr['email'])."'",
			   "postaddress1='".mysql_real_escape_string(stripslashes($participant_arr['postaddress1']))."'",
			   "postaddress2='".mysql_real_escape_string(stripslashes($participant_arr['postaddress2']))."'",
			   "postcity='".mysql_real_escape_string(stripslashes($participant_arr['postcity']))."'",
			   "poststate='".mysql_real_escape_string($participant_arr['poststate'])."'",
			   "postzip='".mysql_real_escape_string($participant_arr['postzip'])."'",
			   "regtype='".mysql_real_escape_string(stripslashes($participant_arr['regtype']))."'");
  $message.=update_table_element($link, $title, "CongoDump", $pairedvalue_array, "badgeid", $participant_arr['partid']);

  // Update Interested entry.
  if (isset($participant_arr['interested']) AND ($participant_arr['interested']!='') AND ($participant_arr['interested']!=0)) {
    $query ="UPDATE Interested SET ";
    $query.="interestedtypeid=".$participant_arr['interested']." ";
    $query.="WHERE badgeid=\"".$participant_arr['partid']."\" AND conid=".$_SESSION['conid'];
    if (!mysql_query($query,$link)) {
      $message.=$query."<BR>Error updating Interested table.  Database not update.";
    }
    ereg("Rows matched: ([0-9]*)", mysql_info($link), $r_matched);
    if ($r_matched[1]==0) {
      $element_array=array('conid','badgeid','interestedtypeid');
      $value_array=array($_SESSION['conid'], $participant_arr['partid'], mysql_real_escape_string(stripslashes($participant_arr['interested'])));
      $message.=submit_table_element($link,"Admin Participants","Interested", $element_array, $value_array);
    } elseif ($r_matched[1]>1) {
      $message.="There might be something wrong with the table, there are multiple interested elements for this year.";
    }
  }

  // Update/add Bios.
  /* We are only updating the raw bios here, so only a 3-depth
   search happens on biolang, biotypename and biodest. */
  $biostate='raw'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
  for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
    for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
      for ($l=0; $l<count($bioinfo['biodest_array']); $l++) {

	// Setup for keyname, to collapse all three variables into one passed name.
	$biotype=$bioinfo['biotype_array'][$i];
	$biolang=$bioinfo['biolang_array'][$j];
	// $biostate=$bioinfo['biostate_array'][$k];
	$biodest=$bioinfo['biodest_array'][$l];
	$keyname=$biotype."_".$biolang."_".$biostate."_".$biodest."_bio";

	// Clean up the posted string
	$teststring=stripslashes(htmlspecialchars_decode($participant_arr[$keyname]));
	$biostring=stripslashes(htmlspecialchars_decode($bioinfo[$keyname]));

	if ($teststring != $biostring) {
	  if ((isset($limit_array['max'][$biodest][$biotype])) and (strlen($teststring)>$limit_array['max'][$biodest][$biotype])) {
	    $message.=ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	    $message.=" too long (".strlen($teststring)." characters), the limit is ".$limit_array['max'][$biodest][$biotype]." characters.";
	  } elseif ((isset($limit_array['min'][$biodest][$biotype])) and (strlen($teststring)<$limit_array['min'][$biodest][$biotype])) {
	    $message.=ucfirst($biostate)." ".ucfirst($biotype)." ".ucfirst($biodest)." (".$biolang.") Biography";
	    $message.=" too short (".strlen($teststring)." characters), the limit is ".$limit_array['min'][$biodest][$biotype]." characters.";
	  } else {
	    $message.=update_bio_element($link,$title,$teststring,$participant_arr['partid'],$biotype,$biolang,$biostate,$biodest);
	  }
	}
      }
    }
  }

  // Submit a note about what was done.
  $element_array = array('badgeid', 'rbadgeid', 'note','conid');
  $value_array=array($participant_arr['partid'],
                     $_SESSION['badgeid'],
                     mysql_real_escape_string(htmlspecialchars_decode($participant_arr['note'])),
		     $_SESSION['conid']);
  $message.=submit_table_element($link, $title, "NotesOnParticipants", $element_array, $value_array);

  // Update permissions
  foreach ($participant_arr['waspermroleid'] as $key => $value) {
    if (($participant_arr['waspermroleid'][$key]=="not") and
	($participant_arr['permroleid'][$key]=="checked")) {
      $element_array = array('badgeid', 'permroleid', 'conid');
      $value_array = array($participant_arr['partid'], $key, $_SESSION['conid']);
      $message.=submit_table_element($link, $title, "UserHasPermissionRole", $element_array, $value_array);
    }
    if (($participant_arr['waspermroleid'][$key]=="indeed") and
	($participant_arr['permroleid'][$key]!="checked")) {
      $match_string="badgeid=".$participant_arr['partid']." AND permroleid=".$key." AND conid=".$_SESSION['conid'];
      $message.=delete_table_element($link, $title, "UserHasPermissionRole",$match_string);
    }
  }

  // Update con roles
  if (isset($participant_arr['wasconroleid'])) {
    foreach ($participant_arr['wasconroleid'] as $key => $value) {
      if (($participant_arr['wasconroleid'][$key]=="not") and
	  ($participant_arr['conroleid'][$key]=="checked")) {
	$element_array = array('badgeid', 'conroleid', 'conid');
	$value_array = array($participant_arr['partid'], $key, $_SESSION['conid']);
	$message.=submit_table_element($link, $title, "UserHasConRole", $element_array, $value_array);
      }
      if (($participant_arr['wasconroleid'][$key]=="indeed") and
	  ($participant_arr['conroleid'][$key]!="checked")) {
	$match_string="badgeid=".$participant_arr['partid']." AND conroleid=".$key." AND conid=".$_SESSION['conid'];
	$message.=delete_table_element($link, $title, "UserHasConRole",$match_string);
      }
    }
  }

  // Make $message additive (.=) to get all the information
  $message="Database updated successfully.<BR>";
  echo "<P class=\"regmsg\">".$message."\n";
}

function get_emailto_from_permrole($permrolename,$link,$title,$description) {
  /* Takes the permrolename and link, (and title and description, in
     case of failure) and returns a valid email address */

  // Get the email-to from the permrole
  $query=<<<EOD
SELECT
    emailtoquery
  FROM
    EmailTo
  WHERE
    emailtodescription='$permrolename'
EOD;

  // presume there is only one match and return that, with the error report, if necessary
  list($rows,$header_array,$emailtoquery_array)=queryreport($query,$link,$title,$description,0);
  list($rows,$header_array,$emailto_array)=queryreport($emailtoquery_array[1]['emailtoquery'],$link,$title,$description,0);
  return($emailto_array[1]['email']);
}

function send_fixed_email_info($emailto,$subject,$body,$link,$title,$description) {
  /* Takes the emailto (which might be a permrolename), subject, body,
     link (and title, and description in case of failure), resolve the
     emailto, if it is a permrolename, use the default from, and no
     cc, and add an entry to the email queue. */
  global $link;
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BioDB=="BIODB") {unset($BIODB);}

  $conid=$_SESSION['conid'];

  $query = <<<EOD
SELECT
    email
  FROM
      $ReportDB.ConRoles
    JOIN $ReportDB.UserHasConRole USING (conroleid)
    JOIN $ReportDB.CongoDump USING (badgeid)
  WHERE
    conrolename like '%ZambiaCoord%' AND
    conid=$conid
EOD;

  list($rows,$header_array,$emailfrom_array)=queryreport($query,$link,$title,$description,0);
  for ($i=1; $i<=$rows; $i++) {
    $emailfromlong.=$emailfrom_array[$i]['email'].",";
  }
  $emailfrom=rtrim($emailfromlong,",");

  // Check to see if it is just an email address, or the permrole to be expanded
  if (!strpos($emailto,"@")) {
    $newemailaddress=get_emailto_from_permrole($emailto,$link,$title,$description);
    $newemailto="$emailto <$newemailaddress>";
    $emailto=$newemailto;
  }

  // Insert into queue, the Zambia Coordinator is the from address, no cc address, status 1 to send
  $element_array=array('emailto','emailfrom','emailcc','emailsubject','body','status');
  $value_array=array($emailto, $emailfrom, '',
		     htmlspecialchars_decode($subject),
		     htmlspecialchars_decode($body),
		     1);
  $message.=submit_table_element($link, $title, "EmailQueue", $element_array, $value_array);
}

/* Three flow report functions.  They are remove, add, and delta rank.
 Need more header doc */
function remove_flow_report ($flowid,$table,$title,$description) {
  global $link;

  // Establish the table name
  $tablename=$table."Flow";

  // Establish the table element or fail
  if ($table=="Group") {
    $tableelement="gflowid";
  } elseif ($table=="Personal") {
    $tableelement="pflowid";
  } else {
    $message="<P>Error finding table $tablename.  Database not updated.</P>\n<P>";
    RenderError($title,$message);
    exit ();
  }

  // Set up the query
  $query="DELETE FROM $tablename where $tableelement=$flowid";

  // Execute the query and test the results
  if (($result=mysql_query($query,$link))===false) {
    $message="<P>Error updating $tablename table.  Database not updated.</P>\n<P>";
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }
}

function add_flow_report ($addreport,$addphase,$table,$group,$title,$description) {
  global $link;

  $mybadgeid=$_SESSION['badgeid'];

  // Get phasetypeid list
  $query="SELECT phasetypeid FROM PhaseTypes ORDER BY phasetypeid";

  // Retrieve query
  list($phasecount,$unneeded_array_a,$phase_array)=queryreport($query,$link,$title,$description,0);

  // Build the limits
  $firstphase=$phase_array[1]['phasetypeid'];
  $lastphase=$phase_array[$phasecount]['phasetypeid'];

  // Set the phase, if it is within the phasetypeid list
  $phasecheck="";
  if (($addphase<=$lastphase) AND ($addphase>=$firstphase)) {
    $phasecheck="phasetypeid='$addphase'";
  } else {
    $phasecheck="phasetypeid is NULL";
  }

  // Establish the table name
  $tablename=$table."Flow";

  // Establish the table element or fail
  if ($table=="Group") {
    $torder="gfloworder";
    $tname="gflowname";
    $cname=$group;
    $tid="gflowid";
  } elseif ($table=="Personal") {
    $torder="pfloworder";
    $tname="badgeid";
    $cname=$mybadgeid;
    $tid="pflowid";
  } else {
    $message="<P>Error finding table $tablename.  Database not updated.</P>\n<P>";
    RenderError($title,$message);
    exit ();
  }

  // Get the last element number, to increment
  $query="SELECT $torder AS floworder FROM $tablename where $tname='$cname' AND $phasecheck ORDER BY $torder DESC LIMIT 0,1";

  // Execute the query, test the results and assign the array values
  if (($result=mysql_query($query,$link))===false) {
    $message="<P>Error retrieving data from database.</P>\n<P>";
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }
  $floworder_array[1]=mysql_fetch_assoc($result);

  // Increment so we don't have redundant keys
  $nextfloworder=$floworder_array[1]['floworder']+1;

  // Insert query
  if ($phasecheck!="phasetypeid is NULL") {
    $query="INSERT INTO $tablename (reportid,$tname,$torder,phasetypeid) VALUES ($addreport,'$cname',$nextfloworder,$addphase)";
  } else {
    $query="INSERT INTO $tablename (reportid,$tname,$torder) VALUES ($addreport,'$cname',$nextfloworder)";
  }

  // Execute query
  if (!mysql_query($query,$link)) {
    $message=$query."<BR>Error updating $tablename database.  Database not updated.";
    RenderError($title,$message);
    exit ();
  }
}

function deltarank_flow_report ($flowid,$table,$direction,$title,$description) {
  global $link;

  // Establish the table name;
  $tablename=$table."Flow";

  // Estabilsh the table elements, or fail;
  if ($table=="Group") {
    $torder="gfloworder";
    $tname="gflowname";
    $tid="gflowid";
  } elseif ($table=="Personal") {
    $torder="pfloworder";
    $tname="badgeid";
    $tid="pflowid";
  } else {
    $message="<P>Error finding table $tablename.  Database not updated.</P>\n<P>";
    RenderError($title,$message);
    exit ();
  }

  // Get element from table;
  $query="SELECT $torder,$tname,phasetypeid FROM $tablename WHERE $tid=$flowid";
  list($phaserows,$phaseheader_array,$phasereport_array)=queryreport($query,$link,$title,$description,0);

  // Set the current flow order number;
  $corder=$phasereport_array[1][$torder];
  $cname=$phasereport_array[1][$tname];

  // Determine the next flow order number, depending on $direction;
  if ($direction=="Up") {
    $norder=$corder-1;
    if ($norder<1) {
      $message="<P>You cannot have an order number less than 1.</P>\n";
      RenderError($title,$message);
      exit ();
    }
  } elseif ($direction="Down") {
    $norder=$corder+1;
  } else {
    $message="<P>You have chosen an inappropriate direction: $direction.</P>\n";
    RenderError($title,$message);
    exit ();
  }

  // Determine if there is a phasetypeid attached to this particular flow element;
  if (isset($phasereport_array[1]['phasetypeid'])) {
    $phase=$phasereport_array[1]['phasetypeid'];
    $phasecheck="phasetypeid='$phase'";
  } else {
    $phasecheck="phasetypeid is NULL";
  }

  // Get element to be swapped with from table, based on current element floworder and $norder
  $query="SELECT $tid FROM $tablename WHERE $torder=$norder AND $tname='$cname' AND $phasecheck LIMIT 0,1";
  if (($result=mysql_query($query,$link))===false) {
    $message="<P>Error retrieving data from database.</P>\n<P>";
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }

  // Swap the elements, checking for errors each time
  $query1="UPDATE $tablename set $torder=$norder where $tid=$flowid";

  // Execute the query and test the results
  if (($result1=mysql_query($query1,$link))===false) {
    $message="<P>Error updating $tablename table.  Database not updated.</P>\n<P>";
    $message.=$query;
    RenderError($title,$message);
    exit ();
  }

  // If there is nothing to swap with, simply stop here.
  if (1==($row=mysql_num_rows($result))) {
    $replace_array[1]=mysql_fetch_assoc($result);
    $rtid=$replace_array[1][$tid];
    $query="UPDATE $tablename set $torder=$corder where $tid=$rtid";

    // Execute the query and test the results;
    if (($result=mysql_query($query,$link))===false) {
      $message="<P>Error updating $tablename table.  Database not updated.</P>\n<P>";
      $message.=$query;
      RenderError($title,$message);
      exit ();
    }
  }
}

/* These functions deal with the outside bios tables */

/* Function getBioData($badgeid)
 Reads Bios tables from db to populate returned array $bioinfo with the
 key of biotypename_biolang_biostatename_bio and the value of biotext eg:
 $bioinfo['uri_en-us_raw_web_bio']='This bio is short and meaningless.'
 Returns bioinfo; */
function getBioData($badgeid) {
  global $message_error,$message2,$link;
  $LanguageList=LANGUAGE_LIST; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($LanguageList=="LANGUAGE_LIST") {unset($LanguageList);}

  $query= <<<EOD
SELECT
    concat(biotypename,"_",biolang,"_",biostatename,"_",biodestname,"_bio") AS biokey,
    biotext
  FROM
      Bios
    JOIN BioTypes USING (biotypeid)
    JOIN BioStates USING (biostateid)
    JOIN BioDests USING (biodestid)
  WHERE
    badgeid="$badgeid"
EOD;
  $result=mysql_query($query,$link);
  if (!$result) {
    $message_error.=mysql_error($link)."\n<BR>Database Error.<BR>No further execution possible.";
    RenderError($title,$message_error);
    exit;
  };
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $bioinfo[$row['biokey']]=$row['biotext'];
  }

  // Get all current possible biolang
  $query="SELECT DISTINCT(biolang) FROM Bios";
  if (isset($LanguageList)) {$query.=" WHERE biolang in $LanguageList";}
  if (($result=mysql_query($query,$link))===false) {
    $message_error.=$query."<BR>\nError retrieving biolang data from database.\n";
    RenderError($title,$message_error);
    exit();
  }
  while ($row=mysql_fetch_assoc($result)) {
    $biolang_array[]=$row['biolang'];
  }
  $bioinfo['biolang_array']=$biolang_array;

  // Get all current possible biotypenames
  $query="SELECT DISTINCT(biotypename) FROM BioTypes WHERE biotypename not in ('web','book')";
  if (($result=mysql_query($query,$link))===false) {
    $message_error.=$query."<BR>\nError retrieving biotypename data from database.\n";
    RenderError($title,$message_error);
    exit();
  }
  while ($row=mysql_fetch_assoc($result)) {
    $biotype_array[]=$row['biotypename'];
  }
  $bioinfo['biotype_array']=$biotype_array;

  // Get all current possible biostatenames
  $query="SELECT DISTINCT(biostatename) FROM BioStates";
  if (($result=mysql_query($query,$link))===false) {
    $message_error.=$query."<BR>\nError retrieving biotypename data from database.\n";
    RenderError($title,$message_error);
    exit();
  }
  while ($row=mysql_fetch_assoc($result)) {
    $biostate_array[]=$row['biostatename'];
  }
  $bioinfo['biostate_array']=$biostate_array;

  // Get all current possible biodestnames
  $query="SELECT DISTINCT(biodestname) FROM BioDests";
  if (($result=mysql_query($query,$link))===false) {
    $message_error.=$query."<BR>\nError retrieving biotypename data from database.\n";
    RenderError($title,$message_error);
    exit();
  }
  while ($row=mysql_fetch_assoc($result)) {
    $biodest_array[]=$row['biodestname'];
  }
  $bioinfo['biodest_array']=$biodest_array;

  return($bioinfo);
}

/* Specific bio update takes eight variables: link, title, biotext,
   badgeid, biotypename, biolang, biostatename, and biodestname, and returns
   the success message */
function update_bio_element ($link, $title, $newbio, $badgeid, $biotypename, $biolang, $biostatename, $biodestname) {

  // make sure it's clean
  $biotext=mysql_real_escape_string($newbio,$link);

  $query=<<<EOD
UPDATE
      Bios
    INNER JOIN BioTypes USING (biotypeid)
    INNER JOIN BioStates USING (biostateid)
    INNER JOIN BioDests USING (biodestid)
  SET
    biotext='$biotext'
  WHERE
    badgeid='$badgeid' AND
    biotypename in ('$biotypename') AND
    biolang in ('$biolang') AND
    biostatename in ('$biostatename') AND
    biodestname in ('$biodestname')
EOD;

  if (!mysql_query($query,$link)) {
    $message_error.=$query."<BR>Error updating the $biotypename $biolang $biostatename $biodestname bio for $badgeid.  Database not updated.";
    RenderError($title,$message_error);
    exit;
  }

  if ((mysql_affected_rows($link) == 0) and ($biotext!="")) {
$query=<<<EOD
INSERT INTO
    Bios (badgeid, biotypeid, biostateid, biodestid, biolang, biotext)
  VALUES
    ('$badgeid',
     (SELECT biotypeid FROM BioTypes WHERE biotypename IN ('$biotypename')),
     (SELECT biostateid FROM BioStates WHERE biostatename IN ('$biostatename')),
     (SELECT biodestid FROM BioDests WHERE biodestname IN ('$biodestname')),
     '$biolang',
     '$biotext');
EOD;

    if (!mysql_query($query,$link)) {
      $message_error.=$query."<BR>Error inserting the $biotypename $biolang $biostatename $biodestname bio for $badgeid.  Database not updated.";
      RenderError($title,$message_error);
      exit;
    }
  }

  $message.="Database updated successfully with bio.<BR>";
  return ($message);
}

function generateSvgString($sessionid,$conid) {
  /* Global Variables */
  global $message_error,$message,$link;
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BioDB=="BIODB") {unset($BIODB);}

  /* Local Variables */
  // Number of values offered
  $possible_value_count=5;

  // Number of offset and blank lines for the key
  //  1 for "Out of", 1 for "Q#", 1 for the space above the title
  //  1 for the title, 1 for the space below the title,
  //  1 for the space between the two sets of information
  //  1 for the bottom space
  $key_blank_lines=7;

  //should match $possible_values
  $value_title[1]="Totally Disagree";
  $value_title[2]="Somewhat Disagree";
  $value_title[3]="Neutral";
  $value_title[4]="Somewhat Agree";
  $value_title[5]="Totally Agree";

  $value_color[1]="red";
  $value_color[2]="orange";
  $value_color[3]="yellow";
  $value_color[4]="green";
  $value_color[5]="blue";

  //Some fixed and calculated values
  $fontsize=9;
  $textyoffset=$fontsize;
  $left_offset=$fontsize*6;
  $top_offset=$fontsize;
  $spacer=$fontsize;
  $bar_width=($fontsize*9);
  $short_bar_width=($bar_width/$possible_value_count);
  $grid_percent=20;
  $grid_count=100/$grid_percent;
  $height=($bar_width*$grid_count);

  // Get the count of each questionid mapped to questionvalue
  $query=<<<EOD
SELECT
    concat(questionid,":",questionvalue) AS QQ,
    count(*) AS tot,
    questionid,
    questionvalue
  FROM
      $ReportDB.Feedback
  WHERE
    sessionid=$sessionid AND
    conid=$conid
  GROUP BY
    1
EOD;

  // Retrieve query
  list($questidvalnos,$questidvalheader_array,$questidval_array)=queryreport($query,$link,$title,$description,0);

  /* Create the graph_return array indexed by questionid and questionvalue.
     This array holds the count of each of the values of the question answers per question.*/
  for ($i=1; $i<=$questidvalnos; $i++) {
    $graph_return[$questidval_array[$i]['questionid']][$questidval_array[$i]['questionvalue']]=$questidval_array[$i]['tot'];
  }

  // Get the total count of each questionid
  $query=<<<EOD
SELECT
    questionid,
    count(*) AS tot,
    questiontext
  FROM
      $ReportDB.Feedback
    JOIN $ReportDB.QuestionsForSurvey USING (questionid)
  WHERE
    sessionid=$sessionid AND
    conid=$conid
  GROUP BY
    questionid
EOD;

  // Retrieve query
  list($questvalnos,$questvalheader_array,$questval_array)=queryreport($query,$link,$title,$description,0);

  /* Create the questid_array of the list of questions,
     the graph_count array which is the total number answers for each question
     and the qdesc for the key which is the actual question, mapped to the number.*/
  for ($i=1; $i<=$questvalnos; $i++) {
    $questid_array[]=$questval_array[$i]['questionid'];
    $graph_count[$questval_array[$i]['questionid']]=$questval_array[$i]['tot'];
    $qdesc[$questval_array[$i]['questionid']]=$questval_array[$i]['questiontext'];
  }

  $number_of_questions=count($questid_array);
  $width=($number_of_questions*($bar_width+$spacer))+($left_offset*2);
  $fullheight=($top_offset+$height+(($textyoffset+$fontsize)*($key_blank_lines+$possible_value_count+$number_of_questions)));

  $svgstring="<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" x=\"0px\" y=\"0px\" width=\"".$width."px\" height=\"".$fullheight."px\" version=\"1.1\">\n";

  // Get the precis name as graph_title
  $query=<<<EOD
SELECT
    concat(title, if(secondtitle,concat(": ",secondtitle),"")) as Title
  FROM
      $ReportDB.Sessions
  WHERE
    sessionid=$sessionid AND
    conid=$conid
EOD;

  // Retrieve query
  list($titlenos,$titleheader_array,$title_array)=queryreport($query,$link,$title,$description,0);

  $graph_title=htmlspecialchars($title_array[1]['Title']);

  // Begin grouping
  $svgstring.='<g font-size="'.$fontsize.'px" font-family="helvetica" fill="#000">'."\n";

  // Left side percentage counts
  for ($i=0; $i<=$grid_count; $i++) {
    $svgstring.='<text x="'.($left_offset-2).'" ';
    $svgstring.='y="'.($top_offset+$textyoffset+$height-((($i*$grid_percent)/100)*$height)).'" ';
    $svgstring.='text-anchor="end">'.$i*$grid_percent.'%-</text>'."\n";
  }

  // The bars
  $k=0;
  foreach ($questid_array as $i) {
    for ($j=$possible_value_count; $j>=1; $j--) {
      if ((isset($graph_return[$i][$j])) and ($graph_return[$i][$j] > 0)) {
	$graph_percent=(($graph_return[$i][$j]/$graph_count[$i])*100);
	$svgstring.='<rect height="'.(($graph_percent/$grid_percent)*$bar_width).'" ';
	$svgstring.='x="'.((($bar_width+$spacer)*$k)+$left_offset).'" ';
	$svgstring.='y="'.($top_offset+$height-(($graph_percent/$grid_percent)*$bar_width)).'" ';
	$svgstring.='width="'.($short_bar_width*$j).'" ';
	$svgstring.='style="stroke:#000;stroke-width:1ps;fill:'.$value_color[$j].';"/>'."\n";
      }
    }
    $svgstring.='<text x="'.((($bar_width+$spacer)*$k)+$left_offset+($bar_width/2)).'" ';
    $svgstring.='y="'.($top_offset+$height+$textyoffset+$fontsize).'" ';
    $svgstring.='text-anchor="middle">Q'.$i.'</text>'."\n";
    $svgstring.='<text x="'.((($bar_width+$spacer)*$k)+$left_offset+($bar_width/2)).'" ';
    $svgstring.='y="'.($top_offset+$height+(($textyoffset+$fontsize)*2)).'" ';
    $svgstring.='text-anchor="middle">Out of '.$graph_count[$i].'</text>'."\n";
    $k++;
  }

  // Right-side percentage counts
  for ($i=0; $i<=$grid_count; $i++) {
    $svgstring.='<text x="'.((($bar_width+$spacer)*$k)+$left_offset+2).'" ';
    $svgstring.='y="'.($top_offset+$textyoffset+$height-((($i*$grid_percent)/100)*$height)).'" ';
    $svgstring.='text-anchor="start">-'.$i*$grid_percent.'%</text>'."\n";
  }

  // Key
  $l=4;
  $svgstring.='<text x="'.(((($bar_width+$spacer)*$k)+($left_offset*2)+2)/2).'" ';
  $svgstring.='y="'.($top_offset+$height+(($textyoffset+$fontsize)*$l)).'" ';
  $svgstring.='text-anchor="middle">Feedback results for '.$graph_title.'</text>'."\n";
  $l++;
  for ($i=$possible_value_count; $i>=1; $i--) {
    $l++;
    $svgstring.='<text x="'.$left_offset.'" ';
    $svgstring.='y="'.($top_offset+$height+(($textyoffset+$fontsize)*$l)).'" ';
    $svgstring.='fill="'.$value_color[$i].'" ';
    $svgstring.='text-anchor="start">'.$value_title[$i].'='.$value_color[$i].'</text>'."\n";
  }
  $l++;
  foreach ($questid_array as $i) {
    $l++;
    $svgstring.='<text x="'.$left_offset.'" ';
    $svgstring.='y="'.($top_offset+$height+(($textyoffset+$fontsize)*$l)).'" ';
    $svgstring.='text-anchor="start">Q '.$i.': '.$qdesc[$i].'</text>'."\n";
  }

  // Close the group, and the SVG
  $svgstring.="</g>\n";
  $svgstring.="</svg>\n";

  return($svgstring);
}

/* These three selects build session_array, list of comments associated with each class into
 session_array['sessionid'] if there should be a graph for that sessionid into
 session_array['graph']['sessionid'] and the key in session_array['key']
 Returns session_array*/
function getFeedbackData($badgeid) {
  global $message_error,$message2,$link;

  $query = <<<EOD
SELECT
    concat(sessionid,"-",conid) AS "Sess-Con",
    comment
  FROM
      CommentsOnSessions
EOD;

  if ($badgeid!="") {
    $query.=<<<EOD
  WHERE
      (sessionid,conid) in (SELECT
			    sessionid,
			    conid
                    FROM
                        ParticipantOnSession
                    WHERE badgeid='$badgeid')
EOD;
  }

  if (!$result=mysql_query($query,$link)) {
    $message.=$query."<BR>Error querying database.<BR>";
    RenderError($title,$message);
    exit();
  }

  while ($row=mysql_fetch_assoc($result)) {
    $session_array[$row['Sess-Con']].="    <br>\n    --\n    <br>\n    <PRE>".fix_slashes($row['comment'])."</PRE>";
  }

  // Check the existance of feedback in Feedback, and mark it in session_array['graph']['sessionid']
  $query = <<<EOD
SELECT
    concat(sessionid,"-",conid) AS "Sess-Con"
  FROM
      Feedback
EOD;

  if ($badgeid!="") {
    $query.=<<<EOD
  WHERE
      (sessionid,conid) in (SELECT
			    sessionid,
			    conid
                    FROM
                        ParticipantOnSession
                    WHERE badgeid='$badgeid')
EOD;
  }

  if (!$result=mysql_query($query,$link)) {
    $message.=$query."<BR>Error querying database.<BR>";
    RenderError($title,$message);
    exit();
  }

  while ($row=mysql_fetch_assoc($result)) {
    $session_array['graph'][$row['Sess-Con']]++;
  }

  return($session_array);
}

/* This function populates the various limits from the PublicationLimits table.
   Tentatively the table should be something like:
     publimid int(11), // key
     conid int(11), // which con
     publimtype enum('min','max'), // top or bottom limit
     publimdest enum('web','book'), // what medium - future 'mobile'
     publimname varchar(15), // bio, description, name, title, picture, uri, title, subtitle - future?
     publimval int(11), // what the limit is
     publimnote text // What it replaced, probably should become more useful, or go away.
   Language is not a consideration, whatever language it is, the size limit will still matter.
   State is not a consideration, be it raw, edited, or good, the size limit will still matter.
 */
function getLimitArray() {
  global $message_error,$message2,$link;
  $conid=$_SESSION['conid'];

  // Get the limit
  $query = <<<EOD
SELECT
    publimtype,
    publimdest,
    publimname,
    publimval
  FROM
      PublicationLimits
  WHERE
      conid=$conid
EOD;

  if (!$result=mysql_query($query,$link)) {
    $message.=$query."<BR>Error querying database.<BR>";
    RenderError($title,$message);
    exit();
  }

  while ($row=mysql_fetch_assoc($result)) {
    if (($row['publimval']!="") and ($row['publimval'] > 0)) {
      $limit_array[$row['publimtype']][$row['publimdest']][$row['publimname']]=$row['publimval'];
    }
  }
  return($limit_array);
}
?>
