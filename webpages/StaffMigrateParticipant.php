<?php
require_once ('StaffCommonCode.php');

$title="Migrate Participant";
$description="<P>Locate someone who already exists, and migrate them to ".$_SESSION['conname']." so they can be appropriately utilized.</P>\n";

// Start the page, choose the individual from the database, and end the page.
topofpagereport($title,$description,$additionalinfo);
select_participant($selpartid, 'ALL', "StaffEditCreateParticipant.php?action=migrate");
correct_footer();
?>