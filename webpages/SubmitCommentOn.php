<?php
function SubmitCommentOnProgramming () {
    global $link;

    $element_array = array('rbadgid','conid','commenter','comment');
    $value_array = array($_SESSION['badgeid'],$_SESSION['conid'],mysql_real_escape_string($_POST['commenter']),mysql_real_escape_string($_POST['comment']));

    $message.=submit_table_element($link,"Comment On Programming","$ReportDB.CommentsOnProgramming",$element_array, $value_array);
    echo "<P class=\"regmsg\">".$message."\n";
    }

function SubmitCommentOnParticipants () {
    global $link;

    $element_array = array('badgeid','rbadgeid','conid','commenter','comment');
    $value_array = array($_POST['partid'],$_SESSION['badgeid'],$_SESSION['conid'],mysql_real_escape_string($_POST['commenter']),mysql_real_escape_string($_POST['comment']));

    $message.=submit_table_element($link,"Comment On Participants","$ReportDB.CommentsOnParticipants",$element_array, $value_array);
    echo "<P class=\"regmsg\">".$message."\n";
    }

function SubmitCommentOnSessions () {
    global $link;

    $element_array = array('sessionid','rbadgeid','commentter','comment');
    $value_array = array($_POST['sessionid'],$_SESSION['badgeid'],mysql_real_escape_string($_POST['commenter']),mysql_real_escape_string($_POST['comment']));

    $message.=submit_table_element($link,"Comment On Sessions","CommentsOnSessions",$element_array, $value_array);
    echo "<P class=\"regmsg\">".$message."\n";
    }
?>