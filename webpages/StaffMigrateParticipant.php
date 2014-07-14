<?php
require_once ('StaffCommonCode.php');

$title="Migrate Participant";
$description="<P>Locate someone who already exists, and migrate them to ".$_SESSION['conname']." so they can be appropriately utilized.</P>\n";

topofpagereport($title,$description,$additionalinfo);

//Choose the individual from the database
// lastname, firstname (badgename/pubsname) <emailaddr> - partid
$query0=<<<EOD
SELECT
    DISTINCT badgeid,
    concat(lastname,', ',firstname,' (',badgename,'/',pubsname,') ',email,' - ',badgeid) AS pname
  FROM 
      Participants 
    JOIN CongoDump USING (badgeid)
  ORDER BY
    lastname
EOD;

// firstname lastname (badgename/pubsname) <emailaddr> - partid
$query1=<<<EOD
SELECT
    DISTINCT badgeid,
    concat(firstname,' ',lastname,' (',badgename,'/',pubsname,') ',email,' - ',badgeid) AS pname
  FROM
      Participants
    JOIN CongoDump USING (badgeid)
  ORDER BY
    firstname
EOD;

// pubsname/badgename (lastname, firstname) <emailaddr> - partid
$query2=<<<EOD
SELECT
    DISTINCT badgeid,
    concat(pubsname,'/',badgename,' (',lastname,', ',firstname,') ',email,' - ',badgeid) AS pname
  FROM
      Participants
    JOIN CongoDump USING (badgeid)
  ORDER BY
    pubsname
EOD;

// emailaddr: pubsname/badgename (lastname, firstname) - partid
$query3=<<<EOD
SELECT
    DISTINCT badgeid,
    concat(email,": ",pubsname,'/',badgename,' (',lastname,', ',firstname,') - ',badgeid) AS pname
  FROM
      Participants
    JOIN CongoDump USING (badgeid)
  ORDER BY
    email
EOD;

// Now give the choices
echo "<FORM name=\"selpartform\" method=POST action=\"StaffEditCreateParticipant.php\">\n";
echo "<INPUT type=\"hidden\" name=\"action\" value=\"edit\">\n";
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
echo "<DIV><LABEL for=\"partide\">Select Participant (Email Address) </LABEL>\n";
echo "<SELECT name=\"partide\">\n";
populate_select_from_query($query3, $selpartid, "Select Participant (Email Address)", true);
echo "</SELECT></DIV>\n";
echo "<P>&nbsp;\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Submit</BUTTON></DIV>\n";
echo "</FORM>\n";

correct_footer();
?>