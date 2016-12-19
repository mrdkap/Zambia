<?php
global $participant,$message,$message_error,$message2,$congoinfo;
require_once('PartCommonCode.php');

$title="Participant View";
$conid=$_SESSION['conid'];
$message_error.=$message2;

topofpagereport($title,$description,$additionalinfo,$message,$message_error);
getCongoData($badgeid);

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
      Participants
    JOIN Interested USING (badgeid)
    JOIN InterestedTypes USING (interestedtypeid)
  WHERE
    badgeid=$badgeid AND
    conid=$conid
EOD;

if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    RenderError($title,$message);
    exit();
    }

list($interested)= mysql_fetch_array($result, MYSQL_NUM);

// to make the words below make sense
if ($interested=="") {
  $interested="not having been in touch";
}

    if (may_I('postcon')) { 
      $verbiage=get_verbiage("Welcome_0");
      if ($verbiage != "") {
	echo eval('?>' . $verbiage);
      } else {
?>
<P>Thank you for your participation in the <?php echo $_SESSION['conname']; ?> event.  With your help it was a great con.  We look forward 
to your participation again next year.</P>
<P>We will post instructions for participating in brainstorming for the next event soon.</P>
<P>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--Program and Events Committees</P>
<?php
    correct_footer();
    exit();
      }
    }
    $verbiage=get_verbiage("Welcome_1");
    if ($verbiage != "") {
      echo eval('?>' . $verbiage);
    } else {
?>

<P><H3> Please check back often as more options will become available as we get closer to the convention. </H3>

<?php } ?>

<P> Dear
<?php /* echo $congoinfo["firstname"]; echo " "; echo $congoinfo["lastname"]; */ echo $congoinfo["badgename"];?>,

<P> Welcome to the <?php echo $_SESSION['conname']; ?> website.</P>

<?php /*
<P> First, please take a moment to indicate your ability and interest in partipating in <?php echo $_SESSION['conname']; ?>.
      <TABLE><TR><TD>&nbsp;&nbsp;&nbsp;</TD>
      <TD><LABEL for="interested" class="padbot0p5">I am interested and able to participate in <?php echo $_SESSION['conname']; ?>. &nbsp;</LABEL>
      <SELECT name=interested class="yesno">
				   <OPTION value=0 <?php if (($interested==0) OR ($interested>2)) {echo "selected";} ?> >&nbsp;</OPTION>
            <OPTION value=1 <?php if ($interested==1) {echo "selected";} ?> >Yes</OPTION>
            <OPTION value=2 <?php if ($interested==2) {echo
            "selected";} ?> >No</OPTION></SELECT>
      </TD></TR></TABLE>
      */ ?>

<P> Your attendence is currently listed as: <?php echo $interested; ?>.</P>

<P> If this does not match with your expectations please get in touch with
your liaison person as soon as possible.</P>

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
  $verbiage=get_verbiage("Welcome_2");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else {
?>
 <P>If you are a presenter please use our <A HREF="MyProposals.php">"Submit a Proposal"</A> form to submit a class, panel, workshop and/or presentation proposal.</P>
<?php
  }
}
if (may_I('my_availability')) {
  $verbiage=get_verbiage("Welcome_3");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else {
?>
  <P> We need to know your availability for scheduling. Please complete the questions in our <A HREF="my_sched_constr.php">"My Availability"</A> form so we can best accommodate your scheduling preferences.
    <UL>
      <LI> Set the total number of time slots you would be willing to commit to for the whole event.</LI>
      <LI> Set the number of time slots per day to which you would be willing to commit. </LI>
      <LI> Indicate the times you are available. </LI>
      <LI> Indicate any conflicts or other constraints. </LI>
    </UL></P>
<?php
  }
}
$verbiage=get_verbiage("Welcome_4");
if ($verbiage != "") {
  echo eval('?>' . $verbiage);
} else {
?>
 <P>Please check the contact information we have on file for you under <A HREF="my_contact.php">"My Profile"</A>. Here you can change your password<?php
if (may_I('EditBio')) { ?>, edit your name as you wish for it to appear in our publications, and edit your bio<?php } ?>.
If you are a new presenter or vendor, we will need a short and long bio for <?php echo $_SESSION['conname']; ?> web and program book publications.
    <UL>
      <LI>Check your contact information.</LI>
      <LI>Change your password.</LI>
<?php  if (may_I('EditBio')) { ?>
      <LI>Edit your name as you want to appear in our publications.</LI>
      <LI>Enter a short and long bio for web and program book publications.</LI>
<?php } ?>
    </UL></P>
<?php
}
if (may_I('my_schedule')) { 
  $verbiage=get_verbiage("Welcome_5");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else {
?>
  <P>We offer a personalized view of your schedule. To see what you have been scheduled to do at the con, see <A HREF="MySchedule.php">"My Schedule"</A>. If there are issues, conflicts or questions please email us at 
<a href="mailto: <?php echo $_SESSION['programemail']; ?>"><?php echo $_SESSION['programemail']; ?></a>. As a courtesy to you, any previous schedules are listed on this page.</P>
<?php
  }
}
if (may_I('search_panels')) {
  $verbiage=get_verbiage("Welcome_6");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else {
?>
<HR>
  <P>We offer panel discussions.  To see what has been suggested for <?php echo $_SESSION['conname']; ?>, <A HREF="my_sessions1.php">"Search Panels"</A> and select the ones you are interested in. (Please save your selections often.)</P>
<?php 
  }
}
if (may_I('my_panel_interests')) {
  $verbiage=get_verbiage("Welcome_7");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else {
?>
  <P>View what you've selected on the <A HREF="PartPanelInterests.php">"My Panel Interests"</A> page, and rank your interest level.  We don't know which panels will be offered, but we appreciate your input to help us decide.</P>
<?php
  }
}
if (may_I('my_gen_int_write')) { 
  $verbiage=get_verbiage("Welcome_8");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
 } else {
?>
  <P>If you would like to provide additional information or constraints, please visit <A HREF="my_interests.php">"My Event Preferences"</A>.</P>
<?php
  }
} ?>

<P>Thank you for your time, and we look forward to seeing you at <?php echo $_SESSION['conname']; ?>.</P>
<P>- <a href="mailto: <?php echo $_SESSION['programemail']; ?>"><?php echo $_SESSION['programemail']; ?> </a> </P>
<?php correct_footer(); ?>
