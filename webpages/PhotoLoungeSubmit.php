<?php
require_once('PartCommonCode.php'); // initialize db; check login;
$conid=$_SESSION['conid'];  // make it a variable so it can be substituted
$badgeid=$_SESSION['badgeid'];  // make it a variable so it can be substituted

$title="Photo Lounge Submission";
$description="<P>Upload what photos you want to submit, below.</P>";
$additionalinfo="<P>By uploading your files you are agreeing to: &lt;PUT SOMETHING HERE&gt;</P>";
$minwidth=1200;
$minheight=1200;
$maxwidth=30000;
$maxheight=30000;
$maxfilesize=5000000;

// Limit this somehow ...
/* if (!may_I('photo_submission')) {
  $message_error ="Either you do not currently have permission to view this page,";
  $message_error.=" or the photo lounge submissions are not open.<BR>\n";
  RenderError($title,$message_error);
  exit();
} */

$target_dir = "../Local/$conid/Photo_Lounge_Submissions/$badgeid-";
if(isset($_POST["submit"])) {

  // Photo information (starting with default values)
  $lounge="Yes";
  $dvd="Yes";
  $pictitle="No Title Listed";
  $photog="No Photographer Listed";
  $model="No Model Listed";
  $location="No Location Listed";
  $notes="NULL";

  // Build the photo information from what was passed in
  if ((!isset($_POST["lounge"])) or ($_POST["lounge"]!="checked")) {$lounge="No";}
  if ((!isset($_POST["dvd"])) or ($_POST["dvd"]!="checked")) {$dvd="No";}
  if (empty($_POST["title"])) {
    $pictitle=htmlspecialchars_decode($_FILES["fileToUpload"]["name"]);
  } else {
    $pictitle=htmlspecialchars_decode($_POST["title"]);
  }
  if (!empty($_POST["photog"])) {$photog=htmlspecialchars_decode($_POST["photog"]);}
  if (!empty($_POST["model"])) {$model=htmlspecialchars_decode($_POST["model"]);}
  if (!empty($_POST["location"])) {htmlspecialchars_decode($_POST["location"]);}
  if (!empty($_POST["notes"])) {htmlspecialchars_decode($_POST["notes"]);}

  // File information
  $target_file = $target_dir . basename($_FILES["fileToUpload"]["tmp_name"]);
  $upload_filename = basename($_FILES["fileToUpload"]["name"]);
  $uploadOk = 1;
  $imageFileType = pathinfo($upload_filename,PATHINFO_EXTENSION);
  if (empty($_FILES["fileToUpload"]["tmp_name"])) {
    $uploadOk = 0 ;
    $message_error.="Sorry, no file provided.<BR>";
  } else {
    $target_file = $target_dir . hash_file('sha256', $_FILES["fileToUpload"]["tmp_name"]) . "." . $imageFileType;
  }
  $target_file_basename = basename($target_file);

  // Check if image file is a actual image or fake image
  
  if (empty($_FILES["fileToUpload"]["tmp_name"])) {
    $uploadOk = 0 ;
    $check = false;
  } else {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  }
  if($check !== false) {
    $width=$check[0];
    $height=$check[1];
    $message.="File is an image - " . $check["mime"] . " sized $width by $height.";
    $uploadOk = 1;
  } else {
    $uploadOk = 0;
    $message_error.="Sorry, file is not an image.<BR>";
  }

  // Check if file already exists
  if (file_exists($target_file)) {
    $uploadOk = 0;
    $message_error.="Sorry, file already exists.<BR>";
  }

  // Check file size
  if ($_FILES["fileToUpload"]["size"] > $maxfilesize) {
    $uploadOk = 0;
    $message_error.="Sorry, your file is too large.<BR>";
  }

  // Check image size
  if (($width < $minwidth) and ($height < $minheight)) {
    $uploadOk = 0;
    $message_error.="Sorry, your image is too small.<BR>";
  }

  // Allow certain file formats
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
     && $imageFileType != "gif" ) {
    $uploadOk = 0;
    $message_error.="Sorry, only JPG, JPEG, PNG & GIF files are allowed.<BR>";
  }

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    $message_error.="Sorry, your file was not uploaded, please try again.<BR>";
    // if everything is ok, try to upload file and track information
  } else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
      $message.="<BR>\nThe file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";

      // Put the information in the database
      $element_array = array('conid', 'badgeid', 'genconsent', 'dvdconsent', 'photofile', 'phototitle', 'photoartist', 'photomodel', 'photoloc', 'photonotes');
      $value_array=array($conid, $badgeid, $lounge, $dvd, $target_file_basename, $pictitle, $photog, $model, $location, $notes);
      submit_table_element($link, $title, "PhotoLoungePix", $element_array, $value_array);
    } else {
      $message_error.="<br>Sorry, there was an error uploading your file, please try again.";
    }
  }
}

// Begin the presentation of the information
topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo "<FORM action=\"PhotoLoungeSubmit.php\" method=\"post\" enctype=\"multipart/form-data\">\n";
echo "Select images to upload:<BR>\n";
echo "<TABLE>\n";
echo "  <TR><TD align=right><LABEL for=\"fileToUpload\">Image: </LABEL></TD>";
echo "    <TD><INPUT type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\"></TD></TR>\n";
echo "  <TR><TD colspan=2 align=center><INPUT type=\"checkbox\" name=\"lounge\" id=\"lounge\" value=\"checked\" checked>\n";
echo "    <LABEL for=\"lounge\"> Lounge permission</LABEL> :: \n";
echo "    <INPUT type=\"checkbox\" name=\"dvd\" id=\"dvd\" value=\"checked\" checked>\n";
echo "    <LABEL for=\"dvd\"> DVD permission</LABEL></TD></TR>\n";
echo "  <TR><TD align=right><LABEL for=\"title\">Title: </LABEL></TD>";
echo "    <TD><INPUT type=\"text\" name=\"title\" id=\"title\"></TD></TR>\n";
echo "  <TR><TD align=right><LABEL for=\"photog\">Photographer(s): </LABEL></TD>";
echo "    <TD><INPUT type=\"text\" name=\"photog\" id=\"photog\"></TD></TR>\n";
echo "  <TR><TD align=right><LABEL for=\"model\">Model(s): </LABEL></TD>";
echo "    <TD><INPUT type=\"text\" name=\"model\" id=\"model\"></TD></TR>\n";
echo "  <TR><TD align=right><LABEL for=\"location\">Location: </LABEL></TD>";
echo "    <TD><INPUT type=\"text\" name=\"location\" id=\"location\"></TD></TR>\n";
echo "</TABLE>\n";
echo "<LABEL for \"notes\">NOTES: </LABEL><TEXTAREA name=\"notes\" rows=6 cols=50></TEXTAREA>\n";
echo "<INPUT type=\"submit\" value=\"Upload Image\" name=\"submit\"><BR>\n";
echo "</FORM>\n";

$picsubmitted=0;
foreach (glob("$target_dir" . "*") as $displayfile) {
  if ($picsubmitted==0) {
    echo "<HR\nHere are the Pictures already submitted by you:</BR>\n";
    $picsubmitted++;
  }
  echo sprintf("<img width=300 src=\"%s\">\n",$displayfile);
}
correct_footer();
?>
