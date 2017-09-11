<?php
require_once('VendorCommonCode.php');
global $participant,$message,$message_error,$message2,$congoinfo;

// LOCALIZATIONS
$title="Vendor View";
$description="";
$additionalinfo="";
$message_error.=$message2;
$conid=$_SESSION['conid'];

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
      echo "  <LI> If you remember your Login number, and password, <A HREF=\"login.php?newconid=$conid\">log\n";
      echo "    in</A> to the system.</LI>\n";
      echo "  <LI> If you remember your Login number, but not your password, please email\n";
      echo "    <A HREF=mailto:".$_SESSION['vendoremail'].">".$_SESSION['vendoremail']."</A> for assistance.</LI>\n";
      echo "</OL>\n";
    } // end of second may_I "Vendor" which might be redundant?  Possibly?
  } // end of local current vendor words
} elseif (may_I('vendor_apply')) { // Applying to vend at this show
  echo "<!-- <form action=\"https://nelaonline.org/fetish-fair-fleamarket/summer-fleamarket/summer-vendors/vendor-application\" method=\"post\" accept-charset=\"utf-8\" class=\"crud_form\" id=\"\" enctype=\"multipart/form-data\"> -->\n";
  echo "<FORM name=\"vendorform\" action=\"tmp_form.php\" method=POST>\n";
  $verbiage=get_verbiage("VendorWelcomeApply");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else {
    echo "<P>Please stand by, this is still under construction.</P>\n";
    echo "<P>If you are a returning vendor, please\n";
    echo "<A HREF=\"login.php?newconid=$conid\">log in</A>\n";
    echo "with your email address and password.</P>\n";
    echo "<P>If you wish to apply to be a vendor, please fill out the following form:</P>\n";
  } // end of local apply to be vendor words
  $verbiage=get_verbiage("VendorWelcomeContract");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else {
    echo "<hr>\n";
    echo "<H2>Vendor Contract</H2>\n";
    echo "<P>Insert vendor contract here.</P>\n";
    echo "<P>Enter your full legal name to hereby agree to the " . $_SESSION['connamelong'];
    echo " Vendor Contract. You will NOT be accepted if you do not agree to the contract.</P>\n";
    echo "<P><input type=\"text\" name=\"vendoracknowledgement\" value=\"\" id=\"vendoracknowledgement\" maxlength=\"50\"></P>\n";
  }
  echo "<P><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"New Vendor\">Update</BUTTON></P>\n";
  echo "</form>\n";
} else { // brainstorm/vendor not permitted
    echo "<P>This page should never be seen.</P>\n";
  $verbiage=get_verbiage("VendorWelcome_1");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else { 
    echo "<P>We are not accepting new vendors at this time for ".$_SESSION['conname'].".</P>\n";
    echo "<P>You may still use the \"Search\" tab to view the vendors who might be attending and/or those that have been accepted.</P>\n";
  } // end of local not accepting at this time words
} //end of brainstorm/vendor not permitted
echo "<P>Thank you and we look forward to reading your suggestions.</P>\n";
correct_footer(); 
?>
