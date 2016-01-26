<?php
require_once('PartCommonCode.php');
global $link;
$conid=$_GET['conid'];

// LOCALISMS
$title="Photo Lounge Picture Database";
$description="<P>All the information from the photo-lounge DB dumped for your viewing pleasure.</P>\n";
$additionalinfo="<P>Please click on the Artists name (not their email) to be able to vote for the pictures.</P>\n";

// Check to see if page can be displayed
if (!may_I("PhotoRev")) {
  $message_error ="Alas, you do not have the proper permissions to view this page.";
  $message_error.=" If you think this is in error, please, get in touch with an administrator.";
  RenderError($title,$message_error);
  exit();
}
 
// Test for conid being passed in
if ((empty($conid)) or (!is_numeric($conid))) {
  $conid=$_SESSION['conid'];
}

$query = <<<EOD
SELECT
    pictureid,
    concat (pubsname, ": ", picturevote) as vote
  FROM
      VotesOnPicture
    JOIN Participants USING (badgeid)
EOD;

//Retrieve query
list($voterows,$vote_header_array,$vote_array)=queryreport($query,$link,$title,$description,0);

// Build the mapping
for ($i=1; $i<=$voterows; $i++) {
  $picture_array[$vote_array[$i]['pictureid']].=$vote_array[$i]['vote'];
}

// Connect to Vendor Database
if (vendor_prepare_db()===false) {
  $message_error="Unable to connect to database.<BR>No further execution possible.";
  RenderError($title,$message_error);
  exit();
}

//Check to see if the table exists
$tablename="default_fff_".$conid."_photo_lounge";
$picurl="https://nelaonline.org";
$pTableExist = mysql_query("show tables like '".$tablename."'");
if ($rTableExist = mysql_fetch_array($pTableExist)) {

  $badlist="'22c90b2144a8d85','230fc849168b5e3','c41500755f89b61','73fe3cb361f676a','9517fd0bf8faa65'";

  $query = <<<EOD
SELECT
    concat("<A HREF=\"PhotoLoungeVote.php?artist=",photo_artist_name,"\">",photo_artist_name,"</A> -- <A HREF=\"mailto:",photo_artist_email,"\">",photo_artist_email,"</A>") AS "Artist",
    if(photo_artist_bio is not NULL,photo_artist_bio,"No Bio Here") AS "Bio",
    concat(photo_artist_consent_web,"/",photo_artist_consent_dvd) AS "Web/DVD",
    concat("\n<!-- 1 -->\n  <TABLE>\n    <TR>\n      <TD>",
	   if(photo_title is not NULL,concat("Title: ",photo_title,"<BR/>"),"No Title<BR/>"),
	   if(photo_model is not NULL,concat("Model: ",photo_model,"<BR/>"),"No Model<BR/>"),
	   if(photo_location is not NULL,concat("Photo Location: ",photo_location,"<BR/>"),"No Location<BR/>"),
	   "</TD>\n      <TD>",
	   if(photo_image != "dummy", if(photo_image is not NULL, if(photo_image not in ($badlist), concat("<A HREF=\"",REPLACE(PMA.path,"{{ url:site }}","$picurl/"),"\"><img src=\"$picurl/files/thumb/",photo_image,"\"></A>"),"No Image"),"No Image"),"Dummy Image"),
	   "</TD></TR>",
	   if(photo_image != "dummy", if(photo_image is not NULL, if(photo_image not in ($badlist), concat("    <TR>\n      <TD>Vote: ",photo_image,"</TD></TR>"),""),""),""),
	   "</TABLE>\n<!-- /1 -->\n") AS "1:",
    concat("\n<!-- 2 -->\n  <TABLE>\n    <TR>\n      <TD>",
	   if(title1 is not NULL,concat("Title: ",title1,"<BR/>"),"No Title<BR/>"),
	   if(model1 is not NULL,concat("Model: ",model1,"<BR/>"),"No Model<BR/>"),
	   if(location1 is not NULL,concat("Photo Location: ",location1,"<BR/>"),"No Location<BR/>"),
	   "</TD>\n      <TD>",
	   if(photo1 != "dummy", if(photo1 is not NULL, if(photo1 not in ($badlist), concat("<A HREF=\"",REPLACE(PMB.path,"{{ url:site }}","$picurl/"),"\"><img src=\"$picurl/files/thumb/",photo1,"\"></A>"),"No Image"),"No Image"),"Dummy Image"),
	   "</TD></TR>",
	   if(photo1 != "dummy", if(photo1 is not NULL, if(photo1 not in ($badlist), concat("    <TR>\n      <TD>Vote: ",photo1,"</TD></TR>"),""),""),""),
	   "</TABLE>\n<!-- /2 -->\n") AS "2:",
    concat("\n<!-- 3 -->\n  <TABLE>\n    <TR>\n      <TD>",
	   if(title2 is not NULL,concat("Title: ",title2,"<BR/>"),"No Title<BR/>"),
	   if(model2 is not NULL,concat("Model: ",model2,"<BR/>"),"No Model<BR/>"),
	   if(location2 is not NULL,concat("Photo Location: ",location2,"<BR/>"),"No Location<BR/>"),
	   "</TD>\n      <TD>",
	   if(photo2 != "dummy", if(photo2 is not NULL, if(photo2 not in ($badlist), concat("<A HREF=\"",REPLACE(PMC.path,"{{ url:site }}","$picurl/"),"\"><img src=\"$picurl/files/thumb/",photo2,"\"></A>"),"No Image"),"No Image"),"Dummy Image"),
	   "</TD></TR>",
	   if(photo2 != "dummy", if(photo2 is not NULL, if(photo2 not in ($badlist), concat("    <TR>\n      <TD>Vote: ",photo2,"</TD></TR>"),""),""),""),
	   "</TABLE>\n<!-- /3 -->\n") AS "3:",
    concat("\n<!-- 4 -->\n  <TABLE>\n    <TR>\n      <TD>",
	   if(title3 is not NULL,concat("Title: ",title3,"<BR/>"),"No Title<BR/>"),
	   if(model3 is not NULL,concat("Model: ",model3,"<BR/>"),"No Model<BR/>"),
	   if(location3 is not NULL,concat("Photo Location: ",location3,"<BR/>"),"No Location<BR/>"),
	   "</TD>\n      <TD>",
	   if(photo3 != "dummy", if(photo3 is not NULL, if(photo3 not in ($badlist), concat("<A HREF=\"",REPLACE(PMD.path,"{{ url:site }}","$picurl/"),"\"><img src=\"$picurl/files/thumb/",photo3,"\"></A>"),"No Image"),"No Image"),"Dummy Image"),
	   "</TD></TR>",
	   if(photo3 != "dummy", if(photo3 is not NULL, if(photo3 not in ($badlist), concat("    <TR>\n      <TD>Vote: ",photo3,"</TD></TR>"),""),""),""),
	   "</TABLE>\n<!-- /4 -->\n") AS "4:",
    concat("\n<!-- 5 -->\n  <TABLE>\n    <TR>\n      <TD>",
	   if(title4 is not NULL,concat("Title: ",title4,"<BR/>"),"No Title<BR/>"),
 	   if(model4 is not NULL,concat("Model: ",model4,"<BR/>"),"No Model<BR/>"),
	   "</TD>\n      <TD>",
	   if(location4 is not NULL,concat("Photo Location: ",location4,"<BR/>"),"No Location<BR/>"),
	   if(photo4 != "dummy", if(photo4 is not NULL, if(photo4 not in ($badlist), concat("<A HREF=\"",REPLACE(PME.path,"{{ url:site }}","$picurl/"),"\"><img src=\"$picurl/files/thumb/",photo4,"\"></A>"),"No Image"),"No Image"),"Dummy Image"),
	   "</TD></TR>",
	   if(photo4 != "dummy", if(photo4 is not NULL, if(photo4 not in ($badlist), concat("    <TR>\n      <TD>Vote: ",photo4,"</TD></TR>"),""),""),""),
	   "</TABLE>\n<!-- /5 -->\n") AS "5:"
  FROM
      $tablename PL
    LEFT JOIN default_files PMA ON (PL.photo_image=PMA.id)
    LEFT JOIN default_files PMB ON (PL.photo1=PMB.id)
    LEFT JOIN default_files PMC ON (PL.photo2=PMC.id)
    LEFT JOIN default_files PMD ON (PL.photo3=PMD.id)
    LEFT JOIN default_files PME ON (PL.photo4=PME.id)
  WHERE
    photo_artist_email not in ("webmaster@nelaonline.org", "sweet99iya@gmail.com")

EOD;

  //Retrieve query
  list($elements,$header_array,$element_array)=queryreport($query,$vlink,$title,$description,0);

  // Produce vote tally
  for ($i=1; $i<=$elements; $i++) {
    for ($j=1; $j<=5; $j++) {
      $fullstring=$element_array[$i][$j.":"];
      $endstring=strstr($fullstring,"Vote: ");
      $workstring=strstr($endstring,"<",true);
      $checkstring=trim(strstr($workstring," ")," ");
      if (!empty($picture_array[$checkstring])) {
	$fixstring="Vote: ".$picture_array[$checkstring];
      } else {
	$fixstring="Vote: None";
      }
      $element_array[$i][$j.":"]=str_replace($workstring,$fixstring,$fullstring);
    }
 }

  // Produce page
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
  echo renderhtmlreport(1,$elements,$header_array,$element_array);
  correct_footer();

} else {
  
  // Error out due to lack of $tablename
  $message.="<P>Cannot find table: $tablename.</P>\n";
  RenderError($title,$message);
  exit();
}