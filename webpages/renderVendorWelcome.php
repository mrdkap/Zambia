<?php
require_once('VendorCommonCode.php');
global $participant,$message,$message_error,$message2,$congoinfo;

// LOCALIZATIONS
$title="Vendor View";
$description="";
$additionalinfo="";
$message_error.=$message2;

topofpagereport($title,$description,$additionalinfo,$message,$message_error);

if (may_I('Vendor')) {
  $verbiage=get_verbiage("VendorWelcome_0");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else { ?>
<P>Here you can create/update your vendor profile, and
   apply to be a vendor at this event, and see the other vendors
   who might also be vending at said event.</P>
<P>Most events are Juried, so not everyone who applies might get
   in.</P>
<P>You have to indicate that you are interested in the event, to
   be considered by the folks who will decide, to begin with.</P>
<?php 
if (may_I("Vendor")) { 
  echo "<UL>\n";
  echo "  <LI> <A HREF=\"VendorSearch.php\">List </A>the known vendors.\n";
  echo "  <LI> <A HREF=\"VendorSubmitVendor.php\">Update</A> your contact (vendor) information.\n";
  if (may_I("vendor_apply")) {
    echo "  <LI> <A HREF=\"VendorApply.php\">Check, update, or apply</A> to be a vendor for ".$_SESSION['conname'].".\n";
  }
  echo "</UL>\n";
} else { 
  echo "<OL>\n";
  echo "  <LI> To apply to be considered for the upcoming FFF, first check the\n";
  echo "    <A HREF=\"VendorSearch.php\">List</A> of known vendors. If you see your\n";
  echo "    company name in the list, write down (or click through) the Login number\n";
  echo "    you see there.</LI>\n";
  echo "  <LI> If you don't see you company name in the\n";
  echo "    <A HREF=\"VendorSearch.php\">List</A>, then\n";
  echo "    <A HREF=\"VendorSubmitVendor.php\">Enter</A> new vendor information using\n";
  echo "    the New Vendor tab above.</LI>\n";
  echo "  <LI> Be sure when you are using the <A HREF=\"VendorSubmitVendor.php\">New\n";
  echo "    Vendor</A> tab to fill in all required fields.  Any fields left blank will\n";
  echo "    result in your application not being entered.</LI>\n";
  echo "  <LI> If you remember your Login number, and password, <A HREF=\"login.php\">log\n";
  echo "    in</A> to the system.</LI>\n";
  echo "  <LI> If you remember your Login number, but not your password, please email\n";
  echo "    <A HREF=mailto:".$_SESSION['vendoremail'].">".$_SESSION['vendoremail']."</A> for assistance.</LI>\n";
  echo "</OL>\n";
}
  } // end of local words
} else { // brainstorm/vendor not permitted
  $verbiage=get_verbiage("VendorWelcome_1");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else { 
    echo "<P>We are not accepting new vendors at this time for ".$_SESSION['conname'].".</P>\n";
    echo "<P>You may still use the \"Search\" tab to view the vendors who might be attending and/or those that have been accepted.</P>\n";
  }
} //end of brainstorm/vendor not permitted
echo "<P>Thank you and we look forward to reading your suggestions.</P>\n";
correct_footer(); 
?>
