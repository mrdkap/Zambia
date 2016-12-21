<?php
require_once('PartCommonCode.php');
global $link;
$title="Photo Lounge Voting";

// Test for conid being passed in
$conid=$_GET['conid'];
if ((empty($conid)) or (!is_numeric($conid))) {
  $conid=$_POST['conid'];
}
if ((empty($conid)) or (!is_numeric($conid))) {
  $conid=$_SESSION['conid'];
}

// Set target_dir
$target_dir = "../Local/$conid/Photo_Lounge_Submissions";

// Check to see if page can be displayed
if (!may_I("PhotoRev")) {
  $message_error ="Alas, you do not have the proper permissions to view this page.";
  $message_error.=" If you think this is in error, please, get in touch with an administrator.";
  RenderError($title,$message_error);
  exit();
}
 
// Error out due to lack of submitter
$artistid=$_GET['artistid'];
if (empty($artistid)) {$artistid=$_POST['artistid'];}
if (empty($artistid)) {
  $message.="<P>There needs to be a Photo Submitter named.  Please <A HREF=\"PhotoLoungePictures.php\">Return</A> to the thumbnail display.</P>\n";
  RenderError($title,$message);
  exit();
}

// Sets up the Select for the artist names, and the map for the specific artist's name.
$photogquery = <<<EOD
SELECT
    DISTINCT badgeid,
    pubsname
  FROM
      PhotoLoungePix
    JOIN Participants USING (badgeid)
  ORDER BY
    pubsname
EOD;

$selectstring.="<SELECT name=\"artistid\">\n";
$selectstring.=populate_select_from_query_inline($photogquery,$artistid,"Select Artist Name",false);
$selectstring.="</SELECT></DIV>\n";

// Fixes the mapping of the id to the name, if the select is used.
list($artistrows,$artist_header_array,$artist_array)=queryreport($photogquery,$link,$title,$description,0);

// Build the artist/artistid map
for ($i=1;$i<=$artistrows;$i++) {
  $artistmap[$artist_array[$i]["badgeid"]]=$artist_array[$i]["pubsname"];
}

// Establish the artist's name.
$artistname=$_GET['artist'];
if (empty($artistname)) {$artistname=$_POST['artist'];}
if (empty($artistname)) {$artistname=$artistmap[$artistid];}


// LOCALISMS
$title="Photo Lounge Voting on $artistname";
$description="<P>Please vote for up to 5 pictures from $artistname.</P>\n";
$description.="<P><A HREF=\"PhotoLoungePictures.php\">Return</A> to the thumbnail display.</P>\n";
$additionalinfo.="<P>Please rank these pictures with 1 being your first choice, and 5 being your\n";
$additionalinfo.="fifth choice.  You can vote on up to 5, but are not required to vote on that\n";
$additionalinfo.="many (or any, if none strike your fancy).  To see the state of the voting go\n";
$additionalinfo.="<A HREF=\"PhotoLoungePictures.php\">back</A> to the thumbnail display.</P>\n";

// Some local variables to be able to do the munging below.

// Make the passed radio buttons a little more readable.
$ordinal[1]="first";
$ordinal[2]="second";
$ordinal[3]="third";
$ordinal[4]="forth";
$ordinal[5]="fifth";
$sordinal[1]="1st";
$sordinal[2]="2nd";
$sordinal[3]="3rd";
$sordinal[4]="4th";
$sordinal[5]="5th";

/* This sets up the tests for the existance and permissibility of the
   pictures.  The $target_dir variable is the root of this tree.
 */

// Add new notes
for ($i=1; $i<=5; $i++) {
  if (!empty($_POST[$ordinal[$i]])) {
    $element_array = array('pictureid', 'conid', 'badgeid', 'picturevote');
    $value_array = array($_POST[$ordinal[$i]],
			 $_SESSION['conid'],
			 $_SESSION['badgeid'],
			 $i);
    submit_table_element($link, $title, "VotesOnPicture", $element_array, $value_array);
  }
}

if ($conid <= 46) { // Exised in this version, see PhotoLoungeVote-old.php for this.
  $message_error="Using the newer format, older voting has been disabled.";
  RenderError($title,$message_error);
  exit();
}

// Sets up the query for the table.
$query=<<<EOD
SELECT
    concat("  <TABLE>\n    <TR>\n      <TD>",
	   if(phototitle is not NULL,concat("Title: ",phototitle,"<BR/>"),"No Title<BR/>"),
	   if(photomodel is not NULL,concat("Model: ",photomodel,"<BR/>"),"No Model<BR/>"),
	   if(photoloc is not NULL,concat("Photo Location: ",photoloc,"<BR/>"),"No Location<BR/>"),
	   "</TD>\n      <TD>\n",
           "<A HREF=\"$target_dir/",photofile,"\"><img width=300 src=\"$target_dir/",photofile,"\"></A>\n",
	   "\n</TD></TR>\n    <TR><TD colspan=2>\n",
	   "<INPUT type=\"radio\" name=\"first\" id=\"first\" value=\"",photofile,"\">1st&nbsp;&nbsp;",
	   "<INPUT type=\"radio\" name=\"second\" id=\"second\" value=\"",photofile,"\">2nd&nbsp;&nbsp;",
	   "<INPUT type=\"radio\" name=\"third\" id=\"third\" value=\"",photofile,"\">3rd&nbsp;&nbsp;",
	   "<INPUT type=\"radio\" name=\"forth\" id=\"forth\" value=\"",photofile,"\">4th&nbsp;&nbsp;",
	   "<INPUT type=\"radio\" name=\"fifth\" id=\"fifth\" value=\"",photofile,"\">5th&nbsp;&nbsp;",
           "</TD></TR></TABLE>\n") AS "Picture"
  FROM
      PhotoLoungePix
  WHERE
    badgeid=$artistid
EOD;

//Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);
 
// Produce page
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo "<FORM name=\"artistform\" method=POST action=\"PhotoLoungeVote.php\">\n";
echo "<DIV><LABEL for=\"artist\">Select Photographer</LABEL>\n";
echo "<INPUT type=\"hidden\" name=\"conid\" value=\"$conid\">\n";
echo "$selectstring\n";
echo "<P>&nbsp;\n";
if (isset($_SESSION['return_to_page'])) {
  echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>";
}
echo "<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Select Submitter</BUTTON></DIV>\n";
echo "</FORM>\n";

if (empty($artistid)) {
  correct_footer();
  exit();
}

echo "<FORM name=\"voteform\" method=POST action=\"PhotoLoungeVote.php\">\n";
echo "<HR/><INPUT type=\"submit\" name=\"submit\" value=\"VOTE\">\n";
echo "<INPUT type=\"hidden\" name=\"artist\" value=\"$artistname\">\n";
echo "<INPUT type=\"hidden\" name=\"artistid\" value=\"$artistid\">\n";
echo "<INPUT type=\"hidden\" name=\"conid\" value=\"$conid\">\n";
echo renderhtmlreport(1,$elements,$header_array,$element_array);
echo "<INPUT type=\"submit\" name=\"submit\" value=\"VOTE\">\n";
echo "</FORM>\n";
correct_footer();
