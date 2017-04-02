<?php
function SubmitAdminParticipants () {
  global $link;

  // Passed in Variables
  $interested = $_POST["interested"];
  $wasinterested = $_POST["wasinterested"];
  $password = $_POST["password"];
  $cpassword = $_POST["cpassword"];
  $partid = $_POST["partid"];
  $pubsname = stripslashes($_POST["pubsname"]);

  // Check password state
  if ($password=="" and $cpassword=="") {
    $update_password=false;
  } elseif ($password==$cpassword) {
    $update_password=true;
  } else {
    $message="Passwords do not match each other.  Database not updated.";
    echo "<P class=\"errmsg\">".$message."\n";
    return;
  }

  // Update Participants table
  if ($update_password==true) {
    $pairedvalue_array=array("pubsname='".mysql_real_escape_string(stripslashes($pubsname))."'",
			     // for 5.5
			     // "password2='".password_hash($password, PASSWORD_DEFAULT)."'");
			     "password='".md5($password)."'");
  } else {
    $pairedvalue_array=array("pubsname='".mysql_real_escape_string(stripslashes($pubsname))."'");
  }
  $message.=update_table_element($link,"Admin Participants", "Participants", $pairedvalue_array, "badgeid", $partid);

  // Update Interested table
  if ($interested != $wasinterested) {
    $query ="UPDATE Interested SET ";
    $query.="interestedtypeid=".$interested." ";
    $query.="WHERE badgeid=\"".$partid."\" AND conid=".$_SESSION['conid'];
    if (!mysql_query($query,$link)) {
      $message.=$query."<BR>Error updating Interested table.  Database not updated.";
      echo "<P class=\"errmsg\">".$message."</P>\n";
      return;
    }
    ereg("Rows matched: ([0-9]*)", mysql_info($link), $r_matched);
    if ($r_matched[1]==0) {
      $element_array=array('conid','badgeid','interestedtypeid');
      $value_array=array($_SESSION['conid'], $partid, mysql_real_escape_string(stripslashes($interested)));
      $message.=submit_table_element($link,"Admin Participants","Interested", $element_array, $value_array);
    } elseif ($r_matched[1]>1) {
      $message.="There might be something wrong with the table, there are multiple interested elements for this year.";
    }
  }

  // Remove from sessions
  if ($wasinterested==1 and $interested==2) {
    $query="DELETE FROM ParticipantOnSession where badgeid = \"$partid\"";
    if (!mysql_query($query,$link)) {
      $message=$query."<BR>Error updating database.  Database not updated.";
      echo "<P class=\"errmsg\">".$message."\n";
      return;
    }
    $message.="Participant removed from ".mysql_affected_rows($link)." session(s).";
  }
  echo "<P class=\"regmsg\">".$message."\n";
}
?>
