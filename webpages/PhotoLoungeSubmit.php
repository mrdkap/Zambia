<?php
require_once('PhotoCommonCode.php'); // initialize db; check login;
$conid=$_SESSION['conid'];  // make it a variable so it can be substituted
$badgeid=$_SESSION['badgeid'];  // make it a variable so it can be substituted

// LOCALIZATIONS
$title="Photo Lounge Submission";
$description="<P>Upload what photos you want to submit, below.</P>\n";
$additionalinfo="<P>By uploading your files you are agreeing to <A HREF=\"PhotoLoungeSubmit.php?release=Yes\">the release</A>.</P>\n";

/* These should probably go in a sourced file under Verbiage or the like. */
// Some variables for this year.
//  - dvd_p is if there will be a dvd produced
//  - modelrelease_p if we require/request a model release
$dvd_p="N";
$modelrelease_p="Y";

// Some base information for the picture and pdf sizes.
$minwidth=1200;
$minheight=1200;
$maxwidth=30000;
$maxheight=30000;
$maximagefilesize=6000000;
$maxpdffilesize=800000000;

// Limit this to just the folks in PhotoSub permissions.
 if (!may_I('PhotoSub')) {
  $message_error ="Either you do not currently have permission to view this page,";
  $message_error.=" or the photo lounge submissions are not open.<br />\n";
  RenderError($title,$message_error);
  exit();
}

// See if the release was requested.
$release_p='';
if ((isset($_GET['release'])) and ($_GET['release']=="Yes")) {
  $release_p=true;
}

// Set the target_dir (with badgeid), thumbnail_dir, release_dir, accepted_dir
// and release_file, if picref was passed in.
$target_dir = "../Local/$conid/Photo_Lounge_Submissions/$badgeid-";
$thumbnail_dir = "../Local/$conid/Photo_Lounge_Submissions/.thmb";
$release_dir = "../Local/$conid/Photo_Lounge_Accepted";
$accepted_dir = "../Local/$conid/Photo_Lounge_Accepted/$badgeid-";
if (!empty($_POST['picref'])) {
  $ext = strrchr(basename($_POST['picref']), '.');
  $release_file = $release_dir . "/" . basename($_POST['picref'], $ext) . "_release.pdf";
}

// Make sure target and thumbnail dir exist
if (!file_exists($thumbnail_dir)) {
    mkdir($thumbnail_dir, 0777, true);
}

// Make sure release_dir exists
if (!file_exists($release_dir)) {
    mkdir($release_dir, 0777, true);
}

// Check to see if there is a photo submitted.
if((isset($_POST["submit"])) and ($_POST["submit"]=="Upload Image")) {

  // Photo information (starting with default values)
  $lounge="Yes";
  if ($dvd_p == "N") {$dvd="No";} else {$dvd="Yes";}
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
    $message_error.="Sorry, no file provided.<br />";
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
    $message_error.="Sorry, file is not an image.<br />";
  }

  // Check if file already exists
  if (file_exists($target_file)) {
    $uploadOk = 0;
    $message_error.="Sorry, file already exists.<br />";
  }

  // Check file size
  if ($_FILES["fileToUpload"]["size"] > $maximagefilesize) {
    $uploadOk = 0;
    $message_error.="Sorry, your file is too large.<br />";
  }

  // Check image size
  if (($width < $minwidth) and ($height < $minheight)) {
    $uploadOk = 0;
    $message_error.="Sorry, your image is too small.<br />";
  }

  // Allow certain file formats
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
     && $imageFileType != "gif" ) {
    $uploadOk = 0;
    $message_error.="Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br />";
  }

  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
    $message_error.="Sorry, your file was not uploaded, please try again.<br />";
    // if everything is ok, try to upload file and track information
  } else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
      $message.="<br />\nThe file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";

      // Put the information in the database
      $element_array = array('conid', 'badgeid', 'genconsent', 'dvdconsent', 'photofile', 'phototitle', 'photoartist', 'photomodel', 'photoloc', 'photonotes');
      $value_array=array($conid, $badgeid, $lounge, $dvd, $target_file_basename, $pictitle, $photog, $model, $location, $notes);
      submit_table_element($link, $title, "PhotoLoungePix", $element_array, $value_array);
    } else {
      $message_error.="<br />Sorry, there was an error uploading your file, please try again.";
    }
  }
}

// Check to see if there is a model release submitted.
if((isset($_POST["submit"])) and ($_POST["submit"]=="Upload release")) {
  $pdfFileType = pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION);
  if (empty($_FILES["fileToUpload"]["tmp_name"])) {
    echo "<P class=\"errmsg\">Sorry, no file provided.</P>";
  } elseif ($_FILES["fileToUpload"]["type"] != "application/pdf") {
    echo "<P class=\"errmsg\">Sorry, file type does not appear to be a PDF file.</P>";
  } elseif ($pdfFileType != "pdf" && $pdfFileType = "PDF") {
    echo "<P class=\"errmsg\">Sorry, file extension does not appear to be a PDF file.</P>";
  } elseif ($_FILES["fileToUpload"]["size"] > $maxpdffilesize) {
    echo "<P class=\"errmsg\">Sorry, your file is too large.</P>";
  } elseif (file_exists($release_file)) {
    echo "<P class=\"errmsg\">Sorry your file already exists.</P>";
  } else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $release_file)) {
      $message.="<br />The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
      $message_error.="<br />Sorry, there was an error uploading your file, please try again.";
    }
  }
}

// Begin the presentation of the information
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

// If they wanted to see the release (again):
if ($release_p) {
  $verbiage=get_verbiage("PhotoLoungeSubmit_0");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  }
}

// Form to submit the pictures
echo "<FORM action=\"PhotoLoungeSubmit.php\" method=\"post\" enctype=\"multipart/form-data\">\n";
echo "Select images to upload:<br />\n";
echo "<TABLE>\n";
echo "  <TR><TD align=right><LABEL for=\"fileToUpload\">Image: </LABEL></TD>";
echo "    <TD><INPUT type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\"></TD></TR>\n";
echo "  <TR><TD colspan=2 align=center><INPUT type=\"checkbox\" name=\"lounge\" id=\"lounge\" value=\"checked\">\n";
echo "    <LABEL for=\"lounge\"> Lounge permission</LABEL>";
// If the dvd is being created.
if ($dvd_p == "Y") {
  echo " :: \n";
  echo "    <INPUT type=\"checkbox\" name=\"dvd\" id=\"dvd\" value=\"checked\">\n";
  echo "    <LABEL for=\"dvd\"> DVD permission</LABEL>";
}
echo "</TD></TR>\n";
echo "  <TR><TD align=right><LABEL for=\"title\">Title: </LABEL></TD>";
echo "    <TD><INPUT type=\"text\" name=\"title\" id=\"title\"></TD></TR>\n";
echo "  <TR><TD align=right><LABEL for=\"photog\">Photographer(s): </LABEL></TD>";
echo "    <TD><INPUT type=\"text\" name=\"photog\" id=\"photog\"></TD></TR>\n";
echo "  <TR><TD align=right><LABEL for=\"model\">Model(s): </LABEL></TD>";
echo "    <TD><INPUT type=\"text\" name=\"model\" id=\"model\"></TD></TR>\n";
// If a model release is being requested.
if ($modelrelease_p == "Y") {
  echo "<TR><TD colspan=2 align=center><em>Attach pdf of Model Release(s) below each selected picture.</em></TD></TR>";
}
echo "  <TR><TD align=right><LABEL for=\"location\">Location: </LABEL></TD>";
echo "    <TD><INPUT type=\"text\" name=\"location\" id=\"location\"></TD></TR>\n";
echo "</TABLE>\n";
echo "<LABEL for \"notes\">NOTES: </LABEL><TEXTAREA name=\"notes\" rows=6 cols=50></TEXTAREA>\n";
echo "<INPUT type=\"submit\" value=\"Upload Image\" name=\"submit\"><br />\n";
echo "</FORM>\n";

// Show which pictures are already submitted
$picsubmitted=0;
foreach (glob("$target_dir" . "*") as $displayfile) {
  if ($picsubmitted==0) {
    echo "<HR\n>Here are the Pictures already submitted by you:<br />\n";
    $picsubmitted++;
  }
  echo sprintf("<img width=300 src=\"%s\">\n",$displayfile);
}

// Show which pictures are already accepted
$picacc=0;
foreach (glob("$accepted_dir" . "*") as $displayfile) {
  if ($picacc==0) {
    echo "<HR\n>Here are the Pictures accepted for our show:<br />\n";
    $picacc++;
  }
  if (strpos($displayfile, ".pdf")) { continue; }
  echo sprintf("<img width=300 src=\"%s\">\n",$displayfile);

  // If we are having a model release
  if ($modelrelease_p == "Y") {

    // Check to see if it's already uploaded  either return this message, or go onto the next phase.
    $ext = strrchr(basename($displayfile), '.');
    $test_release_file = $release_dir . "/" . basename($displayfile, $ext) . "_release.pdf";
    if (file_exists($test_release_file)) {
      echo "<P>Model Release Uploaded.</P>\n";
    } else {
      echo "  <FORM action=\"PhotoLoungeSubmit.php\" method=\"POST\" enctype=\"multipart/form-data\">\n";
      echo "  <P>Select pdf of model releases for this picture to upload:<br />\n";
      echo "  <input type=\"hidden\" name=\"picref\" id=\"picref\" value=\"$displayfile\">\n";
      echo "    <LABEL for=\"fileToUpload\">PDF: </LABEL>\n";
      echo "      <input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\" accept=\"application/pdf\">\n";
      echo "      <input type=\"submit\" value=\"Upload release\" name=\"submit\"><br />\n";
      echo "  </FORM>\n";
    }
  }
}
correct_footer();
?>
