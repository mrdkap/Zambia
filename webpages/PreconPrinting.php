<?php
require_once('StaffCommonCode.php');

$title="Staff - Useful Printing Links";
$description="<P>On this page you will find the tools for printing what is needful before the event.</P>\n";

topofpagereport($title,$description,$additionalinfo,$message,$message_error);
?>

<HR>
<DL>
  <DT id="schedules"><A HREF="SchedulePrint.php?group=Participant">Presenters</A> <A HREF="SchedulePrint.php?group=Programming">Programming Volunteers</A> <A HREF="SchedulePrint.php?group=General">General Volunteers</A></DT>
  <DD id="schedules">Preview and then print a schedule for each group.</DD>
  <DT id="classintro"><A HREF="ClassIntroPrint.php">Introduction pages</A></DT>
  <DD id="classintro">Preview and then print the Introduction pages, including the roles info for everyone applicable.</DD>
  <DT id="logistics"><A HREF="LogisticsPrint.php">Room States</A></DT>
  <DD id="logistics">Preview and then print the Logistics pages for con logistics management.</DD>
  <DT id="gridwide"><A HREF="Postgrid-wide.php?print_p=y">Times x Rooms</A><DT>
  <DD id="gridwide">Print the public grid with the Times across the top and the Rooms down the left side.</DD>
  <DT id="gridtall"><A HREF="Postgrid.php?print_p=y">Rooms x Times</A><DT>
  <DD id="gridtall">Print the public grid with the Rooms across the top and the Times down the left side.</DD>
  <DT id="badges"><A HREF="BadgesPrint.php">Badges</A></DT>
  <DD id="badges">Print up simple paper badges.</DD>
  <DT id="badgebacks"><A HREF="BadgeBackPrint.php">Badge Backs</A></DT>
  <DD id="badgebacks">Print up the sechedule for the back of the simple paper badges.</DD>
  <DT id="tents"><A HREF="TentsPrint.php">Tents</A></DT>
  <DD id="tents">Print the name-tents.</DD>
  <DT id="lables"><A HREF="LabelsPrint.php">Labels</A></DT>
  <DD id="labels">Print up sticky-lables to go on a folder or envelope for everyone.</DD>
  <DT id="letters"><A HREF="WelcomeLettersPrint.php">Welcome Letters</A></DT>
  <DD id="letters">Preview and then print the Welcome Letters for Presenters, Volunteers, and folks who are doing both.</DD>
  <DT id="feedback"><A HREF="StaffFeedback.php">Feedback forms</A></DT>
  <DD id="feedback">Feedback forms for the various days or types to be printed, probably on different colour paper, for easier sorting.</DD>
  <DT id="specialbadges"><A HREF="BadgeBios.php">Special Badge Information</A></DT>
  <DD id="specialbadges">The special hard-printed badges.</DD>
</DL>
<HR>
<P>Some useful views for the program book:</P>
<DL>
  <DT id="programbookbio"><A HREF="BookBios.php">Bios</A>
    <A HREF="BookBios.php?pic_p=N">(without images)</A>
    <A HREF="BookBios.php?short=Y">(short)</A></DT>
  <DD id="programbookbio">An alphabetical list of the Biographical Information for the Program Book.</DD>
  <DT id="programbookstaffbio"><A HREF="BookStaffBios.php">Staff Bios</A>
    <A HREF="BookStaffBios.php?pic_p=N">(without images)</A>
    <A HREF="BookStaffBios.php?short=Y">(short)</A></DT>
  <DD id="programbookstaffbio">An alphabetical list of the Biographical Information of the Staff members for the Program Book.</DD>
  <DT id="programbookdesc"><A HREF="BookSched.php?format=desc">Descriptions</A>
    <A HREF="BookSched.php?format=desc&short=Y">(short)</A></DT>
  <DD id="programbookdesc">An alphabetical list of the descriptions for the Program Book.</DD>
  <DT id="programbooksched"><A HREF="BookSched.php?format=sched">Schedule</A>
    <A HREF="BookSched.php?format=sched&short=Y">(short)</A></DT>
  <DD id="programbooksched">A time-sorted schedule for the Program Book.</DD>
  <DT id="programbooktrack"><A HREF="BookSched.php?format=tracks">Tracks list by Name</A>
    <A HREF="BookSched.php?format=tracks&short=Y">(short)</A></DT>
  <DD id="programbooktrack">A track then name sorted schedule for the Program Book.</DD>
  <DT id="programbooktrack"><A HREF="BookSched.php?format=trtime">Tracks list by Time</A>
    <A HREF="BookSched.php?format=trtime&short=Y">(short)</A></DT>
  <DD id="programbooktrack">A track then time sorted schedule for the Program Book.</DD>
  <DT id="programbookroom"><A HREF="BookSched.php?format=rooms">Rooms list</A>
    <A HREF="BookSched.php?format=rooms&short=Y">(short)</A></DT>
  <DD id="programbookroom">A room-sorted schedule for the Program Book.</DD>
</DL>
<HR>
<P>And one that is useful after the event feedback is done.</P>
<DL>
<?php /*
  <DT id="returnedfeedback"><A HREF="FeedbackPrint.php">Returned Feedback</A></DT>
  <DD id="returnedfeedback">All the feedback on all the schedule elements that we have.</DD>
  */ ?>
  <DT id="altreturnedfeedback"><A HREF="StaffSched.php?format=feedback">Alternative Feedback View</A></DT>
  <DD id="atlreturnedfeedback">All the schedule elements, with the feedback that we have.</DD>
</DL>
<?php correct_footer(); ?>
