<?php
require_once('VendorCommonCode.php');
global $link,$message,$message_error;

$title="Staff - Manage Vendors";
$description="<P>On this page you will find the online tools for managing Vendors.</P>\n";

topofpagereport($title,$description,$additionalinfo,$message,$message_error);
?>

<hr>
<DL>
  <DT><A HREF="Documentation/Vending_Design_Document.html">Vending
  Design Documentation</A></DT>
  <DD>General documentation on all the ins and outs of the vending
  system.  The "Flow" is probably the most useful place to start.</DD>

  <DT><A HREF="VendorWelcome.php">Create a new Vendor entry</A></DT>
  <DD>Create a new entry in the appropriate tables for a new Vendor
  instance.  This should only be used if there isn't already an
  existing entry for said vendor in the first place.</DD>

  <DT><A HREF="VendorSubmitVendor.php">Update Business Info</A></DT>
  <DD>Update the Business Information, which is the persistent
  information across all events, for a vendor.  Can help with any
  particular field they are having trouble with.</DD>

  <DT><A HREF="VendorApply.php">Apply/Update Vendor Application</A></DT>
  <DD>Propose or update an existing vendor for this con instance.
  This could be any number of changes, including getting them set up
  for this event, or changing any paricular feild they are having
  trouble with.</DD>

  <DT><A HREF="StaffManageBios.php">Manage Bios</A></DT>
  <DD>The 5th element on this page is to update the Vendor Bios, from
  the Bios Matrix.  If you need help with understanding this Matrix,
  see the <A HREF="Documentation/Bio_Editing.html">Bios Editing
  Documentation</A> page.</DD>

  <DT><A HREF="VendorAdminState.php">Administrate State</A></DT>
  <DD>Change a vendor between the various states.  For example from
  Applied to Duplicate.  This might be necessary if the Vendor is
  having trouble with the Self Serve nature of the website, or they
  have communicated the information for this stage by email or the
  like.</DD>

  <DT><A HREF="VendorSetupLocation.php">Set up Locations</A></DT>
  <DD>Set up this con instance's possible vendor locations, so they
  might be available as pull-downs when assigning them.</DD>

  <DT><A HREF="VendorSetupSpaceFeature.php">Set up Spaces and Amenities</A></DT>
  <DD>Set up the Amenities and Spaces for this event, pulled from the
  standard pool.</DD>

  <DT><A HREF="PubsSetupAds.php">Set up Ads and Sponsorships</A></DT>
  <DD>Set up the Sponsor Levels, Digital Ads and Print Ads for this
  event, pulled from the standard pool.</DD>

  <DT><A HREF="VendorAssignSpace.php">Assign Space</A></DT>
  <DD>Assign the type of booth for invoicing purposes, and then the
  specific booth location for mapping purposes for each vendor.  Make
  sure the possible Spaces, Amenities, Ads, Sponsors and Locations are set before
  coming here.</DD>

  <DT><A HREF="VendorPayAdj.php">Payment Adjustment</A></DT>
  <DD>Add/Change an amount to adjust the total that a vendor will have
  on their invoice.  This can be a positive number (they owe us more)
  or a negative number (they owe us less).</DD>

  <DT><A HREF="genreport.php?reportname=mytasklistdisplay">My Task List</A></DT>
  <DD>The list of tasks assigned, with appropriate times, to make sure
  everything goes smoothly.</DD>

  <DT><A HREF="TaskListUpdate.php?activityid=-1">Task List Entry</A></DT>
  <DD>Add an element to the task list.</DD>

  <DT><A HREF="StaffSendEmailCompose.php">Set up Email to be sent</A></DT>
  <DD>Select a set of Zambia individuals and send them a mail-merge
  letter.</DD>

  <DT><A HREF="HandSendQueuedMail.php">Send Queued Emails individually</A></DT>
  <DD>View (edit) and send an email that has been queued.</DD>

  <DT><A HREF="genreport.php?reportname=generalvendreport">General
  Vendor Report</A><DT>
  <DD>The probably most useful vendor report has almost all the
  information across all vendors in it for reference/printing/cvs
  downloading.</DD>

  <DT><A HREF="genindex.php?gflowname=Vend">Vendor Reports</A></DT>
  <DD>A collection of useful reports.  If you want more written please
  ask someone who has the authorization to do so.</DD>

  <DT><A HREF="genreport.php?reportname=personalflow

  <DT><A HREF="VolunteerCheckIn.php">Time Cards</A></DT>
  <DD>Don't forget to log your time spent on working on the event.</DD>

</DL>
<?php correct_footer(); ?>
