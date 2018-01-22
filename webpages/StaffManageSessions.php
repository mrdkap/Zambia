<?php
require_once('StaffCommonCode.php');
global $link, $participant, $message, $message_error, $congoinfo;

$title="Staff - Manage Sessions";
$description="<P>On this page you will find the online tools for managing Panels, Events, Films, Anime, and Videos.  (Which is why we refer to them with the target neutral term sessions.)</P>\n";

topofpagereport($title,$description,$additionalinfo,$message,$message_error);
?>

<hr>
<DL>
<?php if ((may_I("SuperProgramming")) or (may_I("SuperLogistics"))) { ?>
   <DT><A HREF="AdminSetupServices.php">Update the Services and Features available</A></DT>
   <DD>Update what Services and Features are offered as standard for a schedule element.</DD>
<?php } ?>
   <DT><A HREF="CreateSession.php">Create a New Session</A></DT>
   <DD>Used for creating new sessions.  They are intially created in status "edit me".  Once created, a second persion edits for content (and uniqueness). This person promotes the session to status "Brainstorm".  A third set of eyes does a basic grammar and spelling edit and promotes the session to status "Vetted".   At that time it is ready for general viewing by prospective panelists.</DD>
   <DT><A HREF="EditSession.php">Edit an Existing Session</A></DT>
   <DD>Rapidly access a Session from the list of Sessions to Edit or Update.</DD>
   <DT><A HREF="genreport.php?reportname=viewsessioncountreport">View Counts of Sessions</A>(<A HREF="genreport.php?reportname=viewrollupsessioncountreport">Alternate View Counts of Sessions</A>)</DT>
   <DD>A quick report broken down by status and then by track to give an idea of where we are.</DD>
   <DT><A HREF="genreport.php?reportname=ViewAllSessions">View All Sessions</A></DT>
   <DD>A tabular report on all sessions organized by track.  Key information on each session is visible from the top level and a link takes you down into the details for any session.</DD>
   <DT><A HREF="CommentOnSessions.php">Session Comments</A></DT>
   <DD>Add comments and feedback specifically for Sessions.</DD>
   <DT><A HREF="VoteOnSession.php">Vote on Sessions</A></DT>
   <DD>Add or update your vote on a set of presenter Sessions.</DD>
   <DT><A HREF="ViewPrecis.php">Precis View</A></DT>
   <DD>This shows all the active Precis, in the status of "Brainstorm", "Edit me", "Vetted", "Assigned", or "Scheduled".</DD>
   <DT><A HREF="KonOpasData.php">Publish to KonOpas/WebApp</A> <A HREF="timeline.php">timeline</A></DT>
   <DD>This pushes/publishes the current con information to the KonOpas/WebApp tool.</DD>
</DL>

<P> Session Search (shows same data as Precis View except on all sessions):</P>
<FORM method=POST action="ViewPrecis.php">
<?php $search=RenderSearchSession(0,0,0,""); echo $search ?>
</FORM>
<?php correct_footer(); ?>
