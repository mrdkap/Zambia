<?php
require_once('CommonCode.php');
global $link;

// LOCALIZATIONS
$_SESSION['return_to_page']='login.php';

// Get the variables passed in

// Conid
if((!empty($_POST['newconid'])) AND (is_numeric($_POST['newconid']))) {
  $conid=$_POST['newconid'];
}

if (empty($conid)) {
  $conid=$_SESSION['conid'];
}

if (empty($conid)) {
  $message_error.="Bad conid ($conid), no con biscuit.";
  RenderError($title,$message_error);
  exit();
}


// Permission Role
if (!empty($_POST['perm'])) {
  if ($_POST['perm']=="Photo") {
    $permission_type="PhotoSub";
  } elseif ($_POST['perm']=="Participant") {
    $permission_type="Participant";
  } elseif ($_POST['perm']=="Host") {
    $permission_type="Host";
  } elseif ($_POST['perm']=="Demo") {
    $permission_type="Demo";
  } elseif ($_POST['perm']=="Teacher") {
    $permission_type="Teacher";
  } elseif ($_POST['perm']=="Presenter") {
    $permission_type="Presenter";
  } elseif ($_POST['perm']=="Author") {
    $permission_type="Author";
  } elseif ($_POST['perm']=="Organizer") {
    $permission_type="Organizer";
  } elseif ($_POST['perm']=="Performer") {
    $permission_type="Performer";
  } else {
    $message_error.="Cannot create permission " . $_POST['perm'];
    RenderError($title,$message_error);
    exit();
  }
}

// Who is the person and the who is not empty and a possible badgeid
if ((!empty($_POST['who'])) and (is_numeric($_POST['who']))) {
  $proposed=$_POST['who'];
} else {
  $message_error.=$_POST['who'] ." is not a proper badgeid.";
  RenderError($title,$message_error);
  exit();
}

$message.=propose_individual($title, $description, $conid, $permission_type, $proposed, $message, $message_error);

// Display the page
header("Location: login.php?login=$proposed&newconid=$conid");

?>