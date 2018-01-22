<?php
    global $link, $participant, $message, $message_error, $congoinfo;
    $title="Welcome";
    require ('PartCommonCode.php');
    if (retrieve_participant_from_db($badgeid)==0) {
        require ('renderWelcome.php');
        exit();
        }
    $message_error.="<BR>Error retrieving data from DB.  No further execution possible.";
    RenderError($title,$message_error);
    exit();
?>
