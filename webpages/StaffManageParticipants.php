<?php
require_once('StaffCommonCode.php');
$title="Staff - Manage Participants";
$description="<P>On this page you will find the online tools for managing Participants.</P>\n";

topofpagereport($title,$description,$additionalinfo,$message,$message_error);
?>
<hr>
<DL>
   <DT id="managebios"><A HREF="StaffManageBios.php">Manage biographies.</A></DT>
   <DD id="managebios">Manage and edit participant biographies.</DD>
   <DT><A HREF="AdminParticipants.php">Administer participants</A></DT>
   <DD>Use this tool to modify the "interested" flag for a participant, change a password for a participant, or delete a participant from all sessions.</DD>
   <DT><A HREF="StaffEditCreateParticipant.php?action=migrate">Migrate Participant from another con-instance.</A></DT> 
   <DD>Pick a participant from the list of all possible participants across all the years, and set them for this year.</DD>
   <DT><A HREF="StaffEditCreateParticipant.php?action=create">Enter Participants</A></DT> 
   <DD>Manually create new participants in the database and enter their data</DD>
   <DT><A HREF="InviteParticipants.php">Invite a participant to a session</A></DT>
   <DD>Use this tool to put sessions marked "invited guests only" on the interest list for a participant.</DD>
   <DT><A HREF="StaffAssignParticipants.php">Assign participants to a session</A></DT>
   <DD>Use this tool to assign participants to a session and select moderator.</DD>
<?php if (may_I("SuperLiaison") OR may_I("Treasurer")) { ?>
   <DT><A HREF="PresenterCompensation.php">Presenter Compensation Table</A></DT>
   <DD>Read and update the Presenter Compensation.  PLEASE only change with the permission of the Div Head.</DD>
<?php } ?>
   <DT><A HREF="PopulateLiaisonTasks.php">Populate Liaison Tasks</A></DT>
   <DD>Populate the generic task set for all of the Presenter Liaisons, with the appropriate presenters.</DD>
   <DT><A HREF="ProgVolSchedule.php">Entered Schedule Data</A></DT>
   <DD>Look at all the collected data to start scheduling from.</DD>
   <DT><A HREF="MaintainRoomSched.php">Maintain room schedule</A></DT>
   <DD>Assign sessions at particular times in a room.</DD>
   <DT><A HREF="NoteOnParticipant.php">Participant Notes</A></DT>
   <DD>Add flow notes and notes on out of band communications with Program Participants.</DD>
   <DT><A HREF="StaffCommentOnParticipants.php">Participant Comments</A></DT>
   <DD>Add comments and feedback specifically for Program Participants.</DD>
<?php if(may_I("SendEmail")) { ?>
   <DT><A HREF="StaffSendEmailCompose.php">Set up Email to be sent.</A></DT>
   <DD>Select a set of Zambia individuals and send them a mail-merge letter.</DD>
   <DT><A HREF="HandSendQueuedMail.php">Send Queued Emails individually</A></DT>
   <DD>View (edit) and send an email that has been queued.</DD>
<?php } ?>
</DL>

<?php correct_footer(); ?>
