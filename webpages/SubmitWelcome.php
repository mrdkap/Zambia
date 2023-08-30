<?php
// The current version of the Welcome Page does not include a form for doing this, but this
// code for SubmitWelcome supports having the user change his password directly on the
// Welcome page.  A previous version prompted the user to change his password if it was
// still the initial password.
require ('PartCommonCode.php');
global $link, $message, $message_error;

$title="Welcome";
$password = $_POST['password'];
$cpassword = $_POST['cpassword'];

// If interested is changed.
if ($_POST['interested']!=$participant['interested']) {
  $query ="UPDATE Interested SET ";
  $query.="interestedtypeid=".$_POST['interested']." ";
  $query.="WHERE badgeid=\"".$badgeid."\" AND conid=".$_SESSION['conid'];
  if (!mysqli_query($link,$query)) {
    $message_error.=$query."<BR>Error updating Interested table.  Database not updated.";
    echo "<P class=\"errmsg\">".$message_error."</P>\n";
    return;
  }
  if (mysqli_affected_rows($link)==0) {
    $element_array=array('conid','badgeid','interestedtypeid');
    $value_array=array($_SESSION['conid'], $badgeid, mysqli_real_escape_string($link,stripslashes($_POST['interested'])));
    $message.=submit_table_element($link,$title,"Interested", $element_array, $value_array);
  } elseif (mysqli_affected_rows($link)>1) {
    $message_error.="There might be something wrong with the table, there are multiple interested elements for this year.";
  }
  $participant['interested']=$_POST['interested'];
}

// If password is changed.
    if ($password=="" and $cpassword=="") {
            $update_password=false;
	    }
        elseif ($password==$cpassword) {
            $update_password=true;
            }
        else {
            $message_error="Passwords do not match each other.  Database not updated.";
            if (retrieve_participant_from_db($badgeid)==0) {
                    require ('renderWelcome.php');
                    exit();
                    }
                else {
                    $message_error.="<BR>Failure to re-retrieve data for Participant.";
                    RenderError($title,$message_error);
                    exit();
                    }
            }
	$query = "UPDATE Participants SET ";
	if ($update_password==true) {
		$query=$query."password=\"".md5($password)."\", ";
		}
	$query.=" WHERE badgeid=\"".$badgeid."\"";                               //"
    if (!mysqli_query($link,$query)) {
		$message_error.=$query."<BR>Error updating database.  Database not updated.";
		RenderError($title,$message_error);
		exit();
		}
    $message.="Database updated successfully.";
    if ($update_password==true) {
	$_SESSION['password']=md5($password);
	}
    if (retrieve_participant_from_db($badgeid)==0) {
            require ('renderWelcome.php');
            exit();
            }
        else {
            $message_error.="<BR>Failure to re-retrieve data for Participant.";
            RenderError($title,$message_error);
            exit();
            }
    $result=mysqli_query($link,"Select password from Participants where badgeid='".$badgeid."'");
    if (!$result) {
    	$message_error.="Incorrect badgeid or password.";
        require ('login.php');
	exit();
	}
    $dbobject=mysqli_fetch_object($result);
    $dbpassword=$dbobject->password;
    //echo $badgeid."<BR>".$dbpassword."<BR>".$password."<BR>".md5($password);
    //exit(0);
    if (md5($password)!=$dbpassword) {
    	$message_error.="Incorrect badgeid or password.";
        require ('login.php');
	exit(0);
	}
/*
    $result=mysqli_query($link,"Select badgename from Participants where badgeid='".$badgeid."'");
    if ($result) {
    		$dbobject=mysqli_fetch_object($result);
    		$badgename=$dbobject->badgename;
    		$_SESSION['badgename']=$badgename;
    		}
    	else {
    		$_SESSION['badgename']="";
		}
    $_SESSION['badgeid']=$badgeid;
    $_SESSION['password']=$dbpassword;
*/
    require ('ParticipantHome.php');
    exit();
?>
