<?php
function SubmitCommentOnProgramming () {
    global $link;

    $element_array = array('rbadgeid','conid','commenter','comment');
    $value_array = array($_SESSION['badgeid'],$_SESSION['conid'],mysqli_real_escape_string($link,$_POST['commenter']),mysqli_real_escape_string($link,$_POST['comment']));

    $message.=submit_table_element($link,"Comment On Programming","CommentsOnProgramming",$element_array, $value_array);
    echo "<P class=\"regmsg\">".$message."\n";
    }

function SubmitCommentOnParticipants () {
    global $link;

    $element_array = array('badgeid','rbadgeid','conid','commenter','comment');
    $value_array = array($_POST['partid'],$_SESSION['badgeid'],$_SESSION['conid'],mysqli_real_escape_string($link,$_POST['commenter']),mysqli_real_escape_string($link,$_POST['comment']));

    $message.=submit_table_element($link,"Comment On Participants","CommentsOnParticipants",$element_array, $value_array);
    echo "<P class=\"regmsg\">".$message."\n";
    }

function SubmitCommentOnSessions () {
    global $link;

    $element_array = array('sessionid','conid','rbadgeid','commenter','comment');
    $value_array = array($_POST['sessionid'],$_SESSION['conid'],$_SESSION['badgeid'],mysqli_real_escape_string($link,$_POST['commenter']),mysqli_real_escape_string($link,$_POST['comment']));

    $message.=submit_table_element($link,"Comment On Sessions","CommentsOnSessions",$element_array, $value_array);
    echo "<P class=\"regmsg\">".$message."\n";
    }
?>
