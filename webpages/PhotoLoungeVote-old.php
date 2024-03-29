<?php
require_once('PartCommonCode.php');
global $link;
$title="Photo Lounge Voting";

// For historical purposes, since voting will be new-style going forward.

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
 
// Error out due to lack of Artist
$artistid=$_GET['artistid'];
if (empty($artistid)) {$artistid=$_POST['artistid'];}
if (empty($artistid)) {
  $message.="<P>There needs to be a Photo Submitter named.  Please <A HREF=\"PhotoLoungePictures.php\">Return</A> to the thumbnail display.</P>\n";
  RenderError($title,$message);
  exit();
}

$artistname=$_GET['artist'];
if (empty($artistname)) {$artistname=$_POST['artist'];}
if (empty($artistname)) {$artistname=$artistid;}

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

if ($conid==46) {
  // Actual picture names as per the table
  $picname[1]="photo_image";
  $picname[2]="photo1";
  $picname[3]="photo2";
  $picname[4]="photo3";
  $picname[5]="photo4";

  // Placement for the pictures (somewhat easier)
  $picplace[1]="PMA";
  $picplace[2]="PMB";
  $picplace[3]="PMC";
  $picplace[4]="PMD";
  $picplace[5]="PME";
} else {
  // Actual picture names as per the table
  $picname[1]="photo_image";
  $picname[2]="photo_image";
  $picname[3]="photo_image";
  $picname[4]="photo_image";
  $picname[5]="photo_image";

  // Placement for the pictures (somewhat easier)
  $picplace[1]="PM";
  $picplace[2]="PM";
  $picplace[3]="PM";
  $picplace[4]="PM";
  $picplace[5]="PM";
}

// Where this whole bloddy mess lives
$picurl="https://".VENDORHOSTNAME;

// The bad pictures, since removing them from the database would be tricky.
$badlist="'22c90b2144a8d85','230fc849168b5e3','c41500755f89b61','73fe3cb361f676a','9517fd0bf8faa65'";


/* This sets up the tests for the existance and permissibility of the
   pictures.  The "badlist" above weeds out any pictures that the
   permission has been withdrawn for us to have.  The picurl is the
   root of this tree, this should probably be made a variable, rather
   than hard-coded like it is.  It can be hoped that, carrying
   forward, the photo lounge will continue to use this particular
   table, set, but ... it might change, so ... being able to change
   such by variablizing it, is a good thing.

   The checks are for null, called "dummy" or are in the badlist (as
   explained above) on both the small version of the image (a width of
   200, seemed acceptable, as opposed to drawing on the thumbnails
   again) and on the existence of the radio buttons.

   The radio button loop needed to be included in it's own concat,
   because of it's own test, since it's in a different table row from
   the information.  If that changes, it might be able to be put back
   in the same table row and column as the picture, and not need it's
   own test and concat.

   Because the $query statement is as it is, it stumbles over
   indecies, so I had to come up with the ${"foo_$i"} nomenclature, so
   it would pass in without a problem.  I might want to restructure
   this, so the whole query line is generated in it's own loop, but
   that might be needlessly complicated.  These short-cuts are to make
   the code much more readable, rather than anything else.
 */
for ($i=1; $i<=5; $i++) {

  // Build the url test and display string
  ${"urlstring_$i"}="if(".$picname[$i]." != \"dummy\", if(".$picname[$i]." is not NULL, if(".$picname[$i]." not in ($badlist),";
  ${"urlstring_$i"}.=" concat(\"<A HREF=\\\"\",REPLACE(".$picplace[$i].".path,\"{{ url:site }}\",\"".$picurl."/\"),\"\\\">";
  ${"urlstring_$i"}.="<img width=200 src=\\\"\",REPLACE(".$picplace[$i].".path,\"{{ url:site }}\",\"".$picurl."/\"),\"\\\"></A>\"),";
  ${"urlstring_$i"}.="\"No Image\"),\"No Image\"),\"Dummy Image\")";

  // Build the radio test and display string

  // Radio button test and prelude
  ${"radiostring_$i"}="if(".$picname[$i]." != \"dummy\", if(".$picname[$i]." is not NULL, if(".$picname[$i]." not in ($badlist), concat(";

  // Loop across the 5 buttons
  $accum="";
  for ($j=1; $j<=5; $j++) {
    $accum.="\"<INPUT type=\\\"radio\\\" name=\\\"".$ordinal[$j]."\\\" id=\\\"".$ordinal[$j]."\\\" value=\\\"\",".$picname[$i].",\"\\\">".$sordinal[$j]."&nbsp;&nbsp;\",\n";
  }

  // Radio button end
  ${"radiostring_$i"}.=$accum." \" \"),\"No Image\"),\"No Image\"),\"Dummy Image\")";
}

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

/* Here's where it gets slightly complex.  All of the below is set up for 46 and earlier.
   Putting in a switch to actually pull the appropriate information from the local
   database afterward, to do the same sort-of setup so people can choose that way. */

if ($conid <= 46) { // Existed in another database.  Should be migrated over at some point.

  // Connect to Vendor Database
  if (vendor_prepare_db()===false) {
    $message_error="Unable to connect to database.<BR>No further execution possible.";
    RenderError($title,$message_error);
    exit();
  }

  //Check to see if the table exists
  $tablename="default_fff_".$conid."_photo_lounge";
  $pTableExist = mysqli_query($link,"show tables like '".$tablename."'");
  if ($rTableExist = mysqli_fetch_array($pTableExist)) {

    if ($conid==46) {
      $query = <<<EOD
SELECT
    concat("\n<!-- 1 -->\n  <TABLE>\n    <TR>\n      <TD>",
	   if(photo_title is not NULL,concat("Title: ",photo_title,"<BR/>"),"No Title<BR/>"),
	   if(photo_model is not NULL,concat("Model: ",photo_model,"<BR/>"),"No Model<BR/>"),
	   if(photo_location is not NULL,concat("Photo Location: ",photo_location,"<BR/>"),"No Location<BR/>"),
	   "</TD>\n      <TD>\n",
           $urlstring_1,
	   "\n</TD></TR>\n    <TR><TD colspan=2>\n",
           $radiostring_1,
           "</TD></TR></TABLE>\n<!-- /1 -->\n") AS "1:",
    concat("\n<!-- 2 -->\n  <TABLE>\n    <TR>\n      <TD>",
	   if(title1 is not NULL,concat("Title: ",title1,"<BR/>"),"No Title<BR/>"),
	   if(model1 is not NULL,concat("Model: ",model1,"<BR/>"),"No Model<BR/>"),
	   if(location1 is not NULL,concat("Photo Location: ",location1,"<BR/>"),"No Location<BR/>"),
	   "</TD>\n      <TD>",
	   $urlstring_2,
	   "</TD></TR>\n    <TR><TD colspan=2>\n",
           $radiostring_2,
           "</TD></TR></TABLE>\n<!-- /2 -->\n") AS "2:",
    concat("\n<!-- 3 -->\n  <TABLE>\n    <TR>\n      <TD>",
	   if(title2 is not NULL,concat("Title: ",title2,"<BR/>"),"No Title<BR/>"),
	   if(model2 is not NULL,concat("Model: ",model2,"<BR/>"),"No Model<BR/>"),
	   if(location2 is not NULL,concat("Photo Location: ",location2,"<BR/>"),"No Location<BR/>"),
	   "</TD>\n      <TD>",
	   $urlstring_3,
	   "</TD></TR>\n    <TR><TD colspan=2>\n",
           $radiostring_3,
	   "</TD></TR></TABLE>\n<!-- /3 -->\n") AS "3:",
    concat("\n<!-- 4 -->\n  <TABLE>\n    <TR>\n      <TD>",
	   if(title3 is not NULL,concat("Title: ",title3,"<BR/>"),"No Title<BR/>"),
	   if(model3 is not NULL,concat("Model: ",model3,"<BR/>"),"No Model<BR/>"),
	   if(location3 is not NULL,concat("Photo Location: ",location3,"<BR/>"),"No Location<BR/>"),
	   "</TD>\n      <TD>",
	   $urlstring_4,
	   "</TD></TR>\n    <TR><TD colspan=2>\n",
           $radiostring_4,
	   "</TD></TR></TABLE>\n<!-- /4 -->\n") AS "4:",
    concat("\n<!-- 5 -->\n  <TABLE>\n    <TR>\n      <TD>",
	   if(title4 is not NULL,concat("Title: ",title4,"<BR/>"),"No Title<BR/>"),
 	   if(model4 is not NULL,concat("Model: ",model4,"<BR/>"),"No Model<BR/>"),
	   "</TD>\n      <TD>",
	   if(location4 is not NULL,concat("Photo Location: ",location4,"<BR/>"),"No Location<BR/>"),
	   $urlstring_5,
	   "</TD></TR>\n    <TR><TD colspan=2>\n",
           $radiostring_5,
	   "</TD></TR></TABLE>\n<!-- /5 -->\n") AS "5:"
  FROM
      $tablename PL
    LEFT JOIN default_files PMA ON (PL.photo_image=PMA.id)
    LEFT JOIN default_files PMB ON (PL.photo1=PMB.id)
    LEFT JOIN default_files PMC ON (PL.photo2=PMC.id)
    LEFT JOIN default_files PMD ON (PL.photo3=PMD.id)
    LEFT JOIN default_files PME ON (PL.photo4=PME.id)
  WHERE
    photo_artist_name like "%$artistname%"
EOD;
    } else { // Is a con that had single-entries in the database, as opposed to 5 entries per.
      $query = <<<EOD
SELECT
    concat("  <TABLE>\n    <TR>\n      <TD>",
	   if(photo_title is not NULL,concat("Title: ",photo_title,"<BR/>"),"No Title<BR/>"),
	   if(photo_model_names is not NULL,concat("Model: ",photo_model_names,"<BR/>"),"No Model<BR/>"),
	   if(photo_artist_location is not NULL,concat("Photo Location: ",photo_artist_location,"<BR/>"),"No Location<BR/>"),
	   "</TD>\n      <TD>\n",
           $urlstring_1,
	   "\n</TD></TR>\n    <TR><TD colspan=2>\n",
           $radiostring_1,
           "</TD></TR></TABLE>\n") AS "Picture"
  FROM
      $tablename
    LEFT JOIN default_files PM ON (photo_image=PM.id)
  WHERE
    photo_artist_name like "%$artistname%"
EOD;
    }

    //Retrieve query
    list($elements,$header_array,$element_array)=queryreport($query,$vlink,$title,$description,0);

    $photogquery = <<<EOD
SELECT
    DISTINCT photo_artist_name
  FROM
      $tablename
  ORDER BY
    photo_artist_name
EOD;

    $result=mysqli_query($vlink,$photogquery);
    $selectstring.="<SELECT name=\"artist\">\n";
    while (list($select_artist_name)= mysqli_fetch_array($result, MYSQLI_NUM)) {
      $selectstring.="<OPTION value=\"".$select_artist_name."\" ";
      if ($select_artist_name==$artistname) {
	$selectstring.="selected";
      }
      $selectstring.=">".$select_artist_name."</OPTION>\n";
    }
    $selectstring.="</SELECT></DIV>\n";
  } else {
  
  // Error out due to lack of $tablename
  $message.="<P>Cannot find table: $tablename.</P>\n";
  RenderError($title,$message);
  exit();
  }
} else { // Is a con after 46

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

  // Sets up the Select for the artist names.
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
  for ($i=1;$i<=$artistrows;$i++) {
    $artistmap[$artist_array[$i]["badgeid"]]=$artist_array[$i]["pubsname"];
  }
  if ($artistname==$artistid) {$artistname=$artistmap[$artistid];}
  $title="Photo Lounge Voting on $artistname";
  $description="<P>Please vote for up to 5 pictures from $artistname.</P>\n";
}
 
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

if (empty($artistname)) {
  correct_footer();
  exit();
}

echo "<FORM name=\"voteform\" method=POST action=\"PhotoLoungeVote.php\">\n";
echo "<HR/><INPUT type=\"submit\" name=\"submit\" value=\"VOTE\">\n";
echo "<INPUT type=\"hidden\" name=\"artist\" value=\"$artistname\">\n";
echo "<INPUT type=\"hidden\" name=\"conid\" value=\"$conid\">\n";
echo renderhtmlreport(1,$elements,$header_array,$element_array);
echo "<INPUT type=\"submit\" name=\"submit\" value=\"VOTE\">\n";
echo "</FORM>\n";
correct_footer();

