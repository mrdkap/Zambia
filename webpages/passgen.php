<?php
$logging_in=true;

require_once('CommonCode.php');
require_once('email_functions.php');

if (($_POST['Update']='Y') AND (!empty($_POST['badgeid'])) AND (is_numeric($_POST['badgeid']))) {
  $badgeid=$_POST['badgeid'];

  // Generate the password
  $length=16;
  $lowercase = "qwertyuiopasdfghjklzxcvbnm";
  $uppercase = "ASDFGHJKLZXCVBNMQWERTYUIOP";
  $numbers = "1234567890";
  $specialcharacters = "{}[];:,./<>?_+~!@#";
  $randomCode = "";
  mt_srand(crc32(microtime()));
  $max = strlen($lowercase) - 1;
  for ($x = 0; $x < abs($length/3); $x++) {
    $randomCode .= $lowercase{mt_rand(0, $max)};
  }
  $max = strlen($uppercase) - 1;
  for ($x = 0; $x < abs($length/3); $x++) {
    $randomCode .= $uppercase{mt_rand(0, $max)};
  }
  $max = strlen($specialcharacters) - 1;
  for ($x = 0; $x < abs($length/3); $x++) {
    $randomCode .= $specialcharacters{mt_rand(0, $max)};
  }
  $max = strlen($numbers) - 1;
  for ($x = 0; $x < abs($length/3); $x++) {
    $randomCode .= $numbers{mt_rand(0, $max)};
  }
  $newpass=str_shuffle($randomCode);

  // Get the email address of the person
  $result=mysqli_query($link,"SELECT email FROM Participants WHERE badgeid=$badgeid");
  if ($result and (mysqli_num_rows($result)==1)) {
      $dbobject=mysqli_fetch_object($result);
      $to=$dbobject->email;
      $from=$_SESSION['programemail'];
      $headers="From: $from <$from>\r\n";
      $flags="-f$from -r$from";
      $subject="Requested update.";
      $body="Hello,\n";
      $body.="The key you requested: $newpass\n";
      $body.="Please change it at your earliest convenience.\n";
      $body.="Thank you for being part of our event.\n";
      $pairedvalue_array=array(// for 5.5
			       // "password2='".password_hash($password, PASSWORD_DEFAULT)."'");
			       "password='".md5($newpass)."'");
      $ustr=update_table_element($link,"Update Password", "Participants", $pairedvalue_array, "badgeid", $badgeid);
      $ok=mail($to,$subject,$body,$headers,$flags);
      if ($ustr=="Table Participants updated successfully.<BR>") {
	$message.="New password sent.<BR>";
      }
  } else {
    $message_error.="There is something wrong with your request. ";
    $message_error.="Please contact the staff for further assistance.";
  }
}

require ('login.php');
exit();

