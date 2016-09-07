<?php
require_once('PartCommonCode.php'); // initialize db; check login;
$conid=$_SESSION['conid'];  // make it a variable so it can be substituted
$badgeid=$_SESSION['badgeid'];  // make it a variable so it can be substituted

$title="Photo Lounge Submission";
$description="<P>Upload what photos you want to submit, below.</P>";
$additionalinfo="<P>By uploading your files you are agreeing to: &lt;PUT SOMETHING HERE&gt;</P>";
$piccount=1;

// Limit this somehow ...
/* if (!may_I('photo_submission')) {
  $message_error ="Either you do not currently have permission to view this page,";
  $message_error.=" or the photo lounge submissions are not open.<BR>\n";
  RenderError($title,$message_error);
  exit();
} */

$target_dir = "../Local/$conid/Photo_Lounge_Submissions/$badgeid-";
if(isset($_POST["submit"])) {
  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
  $uploadOk = 1;
  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

  // Check if image file is a actual image or fake image
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if($check !== false) {
    $message.="File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  } else {
    $uploadOk = 0;
    $message_error.="File is not an image.<BR>";
  }

  // Check if file already exists
  if (file_exists($target_file)) {
    $uploadOk = 0;
    $message_error.="Sorry, file already exists.<BR>";
  }

  // Check file size
  if ($_FILES["fileToUpload"]["size"] > 5000000) {
    $uploadOk = 0;
    $message_error.="Sorry, your file is too large.<BR>";
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
    // if everything is ok, try to upload file
  } else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
      $message.=" The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
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
echo "  <TR><TD colspan=2 align=center><INPUT type=\"checkbox\" name=\"web\" id=\"web\"><LABEL for=\"web\"> Web permission</LABEL> :: \n";
echo "     <INPUT type=\"checkbox\" name=\"dvd\" id=\"dvd\"><LABEL for=\"dvd\"> DVD permission</LABEL></TD></TR>\n";
echo "  <TR><TD align=right><LABEL for=\"title\">Title: </LABEL></TD>";
echo "    <TD><INPUT type=\"text\" name=\"title\" id=\"title\"></TD></TR>\n";
echo "  <TR><TD align=right><LABEL for=\"photog\">Photographer(s): </LABEL></TD>";
echo "    <TD><INPUT type=\"text\" name=\"photog\" id=\"photog\"></TD></TR>\n";
echo "  <TR><TD align=right><LABEL for=\"model\">Model(s): </LABEL></TD>";
echo "    <TD><INPUT type=\"text\" name=\"model\" id=\"model\"></TD></TR>\n";
echo "  <TR><TD align=right><LABEL for=\"location\">Location: </LABEL></TD>";
echo "    <TD><INPUT type=\"text\" name=\"location\" id=\"location\"></TD></TR>\n";
echo "</TABLE>\n";
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
