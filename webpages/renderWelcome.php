<?php
   global $participant,$message,$message_error,$message2,$congoinfo;
   //error_log("Zambia: Reached renderWelcome.php"); 
   $title="Participant View";
   require_once('PartCommonCode.php');
   $conid=$_SESSION['conid'];
   participant_header($title);
   getCongoData($badgeid);

    if ($message_error!="") { 
        echo "<P class=\"errmsg\">$message_error</P>\n";
        }
    if ($message!="") {
        echo "<P class=\"regmsg\">$message</P>\n";
        }
    $chpw=($participant["password"]=="4cb9c8a8048fd02294477fcb1a41191a");

/* Get interested state from table.  Below the full table isn't
   generated, because we _only_ want to give them a limited set of
   responses.  Tentatively (although not coded yet) 
   "Pending" or "Not Accepted", not modifyable, "Invited", "Suggested"
   or "Yes" => "No", "No", "Not Applied", or not on the table at all
   => "Suggested", perhaps this should be coded into the table itself,
   since different cons might do things differently. */
$query = <<<EOD
SELECT
    interestedtypename
  FROM
      $ReportDB.Participants
    JOIN $ReportDB.Interested I USING (badgeid)
    JOIN $ReportDB.InterestedTypes USING (interestedtypeid)
  WHERE
    badgeid=$badgeid AND
    I.conid=$conid
EOD;

if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }

list($interested)= mysql_fetch_array($result, MYSQL_NUM);

// to make the words below make sense
if ($interested=="") {
  $interested="not having been in touch";
}

    if (may_I('postcon')) { 
      if (file_exists("../Local/Verbiage/Welcome_0")) {
	echo file_get_contents("../Local/Verbiage/Welcome_0");
      } else {
?>
<P>Thank you for your participation in the <?php echo CON_NAME; ?> event.  With your help it was a great con.  We look forward 
to your participation again next year.</P>
<P>We will post instructions for participating in brainstorming for the next event soon.</P>
<P>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--Program and Events Committees</P>
<?php
    participant_footer();
    exit();
      }
    }
    if (file_exists("../Local/Verbiage/Welcome_1")) {
      echo file_get_contents("../Local/Verbiage/Welcome_1");
    } else {
?>

<P><H3> Please check back often as more options will become available as we get closer to the convention. </H3>

<?php } ?>

<P> Dear
<?php echo $congoinfo["firstname"]; echo " "; echo $congoinfo["lastname"]; ?>,

<P> Welcome to the <?php echo CON_NAME; ?> website.</P>

<?php /*
<P> First, please take a moment to indicate your ability and interest in partipating in <?php echo CON_NAME; ?>.
      <TABLE><TR><TD>&nbsp;&nbsp;&nbsp;</TD>
      <TD><LABEL for="interested" class="padbot0p5">I am interested and able to participate in <?php echo CON_NAME; ?>. &nbsp;</LABEL>
      <SELECT name=interested class="yesno">
				   <OPTION value=0 <?php if (($interested==0) OR ($interested>2)) {echo "selected";} ?> >&nbsp;</OPTION>
            <OPTION value=1 <?php if ($interested==1) {echo "selected";} ?> >Yes</OPTION>
            <OPTION value=2 <?php if ($interested==2) {echo
            "selected";} ?> >No</OPTION></SELECT>
      </TD></TR></TABLE>
      */ ?>

<P> You're attendence is currently listed as: <?php echo $interested; ?>.</P>

<P> If this does not match with your expectations please, get in touch with
your liaison person, as soon as possible.</P>

<HR>
<?php if ($chpw) { ?>
<FORM class="nomargin" name="pwform" method=POST action="SubmitWelcome.php">
  <DIV id="update_section">
    <P>Now take a moment and personalize your password.
    <TABLE>
      <TR>
        <TD>&nbsp;&nbsp;&nbsp;</TD>
        <TD>Change Password</TD>
        <TD><INPUT type="password" size="10" name="password"></TD>
      </TR>
      <TR>
        <TD>&nbsp;&nbsp;&nbsp;</td><td>Confirm New Password&nbsp;</TD>
        <TD><INPUT type="password" size="10" name="cpassword"></TD>
      </TR>
    </TABLE>
    <DIV class="submit">
      <DIV id="submit" >
        <BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
      </DIV>
    </DIV>
  </DIV>
</FORM>
<?php } ?>

<?php
if (may_I('BrainstormSubmit')) { 
  if (file_exists("../Local/Verbiage/Welcome_2")) {
    echo file_get_contents("../Local/Verbiage/Welcome_2");
  } else {
?>
 <P>If you are a presenter please use our <A HREF="MyProposals.php">"Submit a Proposal"</A> form to submit a class, panel, workshop and/or presentation proposal.</P>
<?php
  }
}
if (may_I('my_availability')) {
  if (file_exists("../Local/Verbiage/Welcome_3")) {
    echo file_get_contents("../Local/Verbiage/Welcome_3");
  } else {
?>
  <P> We need to know your availability for scheduling. Please complete the questions in our <A HREF="my_sched_constr.php">"My Availability"</A> form so we can best accomodate your scheduling preferences.
    <UL>
      <LI> Set the total number of times you would be willing to commit to, for all of <?php echo CON_NAME; ?>.</LI>
      <LI> Set the per day number of times you would be willing to commit to. </LI>
      <LI> Indicate the times you are able to commit to <?php echo CON_NAME; ?>. </LI>
      <LI> Indicate any conflicts or other constraints. </LI>
    </UL></P>
<?php
  }
}
if (file_exists("../Local/Verbiage/Welcome_4")) {
  echo file_get_contents("../Local/Verbiage/Welcome_4");
} else {
?>
 <P>Please check the contact information we have on file for you under <A HREF="my_contact.php">"My Profile"</A>. Here you can change your password<?php
if (may_I('EditBio')) { ?>, edit your name as wish for it to appear in our publications and edit your bio<?php } ?>.
If you are a new presenter or vendor, we will need a short and long bio for <?php echo CON_NAME; ?> web and program book publications.
    <UL>
      <LI>Check your contact information.</LI>
      <LI>Change your passowrd.</LI>
<?php  if (may_I('EditBio')) { ?>
      <LI>Edit your name as you want to appear in our publications.</LI>
      <LI>Enter a short and long bio for <?php echo CON_NAME; ?> web and program book publications.</LI>
<?php } ?>
    </UL></P>
<?php
}
if (may_I('my_schedule')) { 
    if (file_exists("../Local/Verbiage/Welcome_5")) {
      echo file_get_contents("../Local/Verbiage/Welcome_5");
    } else {
?>
  <P>We offer a personalized view of your schedule. To see what you have been scheduled to do at the con, see <A HREF="MySchedule.php">"My Schedule"</A>. If there are issues, conflict or questions please email us at 
<a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?></a> As a courtesy to you, any previous schedules are listed on this page.</P>
<?php
  }
}
if (may_I('search_panels')) {
  if (file_exists("../Local/Verbiage/Welcome_6")) {
    echo file_get_contents("../Local/Verbiage/Welcome_6");
  } else {
?>
<HR>
  <P>We offer panel discussions.  To see what has been suggested for <?php echo CON_NAME; ?>, <A HREF="my_sessions1.php">"Search Panels"</A> and select the ones you are interest in. (Please save your selections often.)</P>
<?php 
  }
}
if (may_I('my_panel_interests')) {
  if (file_exists("../Local/Verbiage/Welcome_7")) {
    echo file_get_contents("../Local/Verbiage/Welcome_7");
  } else {
?>
  <P>View what you've selected on the <A HREF="PartPanelInterests.php">"My Panel Interests"</A> page, and rank your interest level.  We don't know which panels will be offered, but we appreciate your input to help us decide.</P>
<?php
  }
}
if (may_I('my_gen_int_write')) { 
  if (file_exists("../Local/Verbiage/Welcome_8")) {
    echo file_get_contents("../Local/Verbiage/Welcome_8");
 } else {
?>
  <P>If you would like to provide additional information or constraints, please visit <A HREF="my_interests.php">"My Event Preferences"</A>.</P>
<?php
  }
} ?>

<P>Thank you for your time, and we look forward to seeing you at <?php echo CON_NAME; ?>.</P>
<P>- <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a> </P>
<?php correct_footer(); ?>
