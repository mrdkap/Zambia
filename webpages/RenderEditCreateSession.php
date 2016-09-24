<?php
/* This function will output the page with the form to add or create a session
   Variables
   action: "create"/"edit"/"brainstorm"/"propose"
   session: array with all data of record to edit or defaults for create
   message: a string to display before the form
   message_error: an urgent string to display before the form */
function RenderEditCreateSession ($action, $session, $message, $message_error) {
  global $link, $name, $email, $debug;
  $conid=$_SESSION['conid'];
  $badgeid=$_SESSION['badgeid'];
  require_once("CommonCode.php");
  require_once("javascript_functions.php");



  // This switched on, the specific action, and ends if one of the
  // specified ones don't exist.
  if ($action=="create") {
    $title="Add New Session";
    $description="<P>Saving an added session presumes you are going to add another new session next.</P>";
  } elseif ($action=="edit") {
    $title="Edit Session";
    $description="<P>Hit either the top or the bottom \"Save\" button when you are done with your changes.</P>\n";
    $additionalinfo ="<P>Clicking on the session number will allow you to assign participants to your session.";
    $additionalinfo.=" A specific person's class should have their Invited Guests Only box checked so other";
    $additionalinfo.=" presenters won't be presented with the option of that class.</P>\n";
  } elseif ($action=="propose") {
    $title="Propose Session";
    $description="<P>Saving a propopsed session presumes you are going to propose another new session next.</P>";
    /* $additionalinfo.="  <FORM name=\"brainstormform\" method=\"POST\" action=\"doLogin.php\">\n";
    $additionalinfo.="    <INPUT type=\"hidden\" name=\"badgeid\" value=\"100\">\n";
    $additionalinfo.="    <INPUT type=\"hidden\" name=\"passwd\" value=\"submit\">\n";
    $additionalinfo.="    <INPUT type=\"hidden\" name=\"target\" value=\"brainstorm\">\n";
    $additionalinfo.="    <INPUT type=\"submit\" name=\"submit\" value=\"Log out and look at what has already been submitted.\">\n";
    $additionalinfo.="  </FORM>\n"; */

    //Accepted
    $query=<<<EOD
SELECT
    title
  FROM
      Sessions
    JOIN SessionStatuses USING (statusid)
  WHERE
    conid=$conid AND
    statusname in ('Vetted','Assigned','Scheduled') AND
    suggestor=$badgeid
EOD;

    list($accepted_rows,$accepted_header_array,$accepted_array)=queryreport($query,$link,$title,$query,0);

    $accepted_string="";
    for ($i=1 ; $i<=$accepted_rows ; $i++) {
      $accepted_string.=$accepted_array[$i]['title'] . ", ";
    }
    $accepted_string=trim($accepted_string,", ");

    $additionalinfo.="<P>Here are the sessions accepted so far: $accepted_string</P>\n";

    // Proposed
    $query=<<<EOD
SELECT
    title
  FROM
      Sessions
    JOIN SessionStatuses USING (statusid)
  WHERE
    conid=$conid AND
    statusname not in ('duplicate','Vetted','Assigned','Scheduled') AND
    suggestor=$badgeid
EOD;

    list($proposed_rows,$proposed_header_array,$proposed_array)=queryreport($query,$link,$title,$query,0);

    $proposed_string="";
    for ($i=1 ; $i<=$proposed_rows ; $i++) {
      $proposed_string.=$proposed_array[$i]['title'] . ", ";
    }
    $proposed_string=trim($proposed_string,", ");

    $additionalinfo.="<P>Here are the (other) sessions you proposed: $proposed_string</P>\n";

  } elseif ($action=="brainstorm") {
    $title="Proposed Session";
    $description="<P>Saving a propopsed session presumes you are going to propose another new session next.</P>";
  } else {
    exit();
  }

  // Get the various length limits
  $limit_array=getLimitArray();

  // Begin the output
  topofpagereport($title,$description,$additionalinfo,$message,$message_error);

  if (isset($debug)) {
    echo $debug."<BR>\n";
  }

  $typequery = <<<EOD
SELECT
    typeid,
    phasetypename
  FROM
      Phase
    JOIN PhaseTypes USING (phasetypeid)
    JOIN Types ON (phasetypename=typename)
  WHERE
    conid=$conid AND
    phasetypeid in (19,20,21,22,23,24) AND
    phasestate="0"
EOD;

?>
    <DIV class="formbox">
        <FORM name="sessform" class="bb"  method=POST action="SubmitEditCreateSession.php">
            <INPUT type="hidden" name="name" value="<?php echo htmlspecialchars($name,ENT_COMPAT);?>">
            <INPUT type="hidden" name="email" value="<?php echo htmlspecialchars($email,ENT_COMPAT);?>">
            <INPUT type="hidden" name="conid" value="<?php echo $_SESSION['conid'];?>">
            <DIV style="margin: 0.5em; padding: 0em"><TABLE style="margin: 0em; padding: 0em" ><COL width=600><COL>
              <TR style="margin: 0em; padding: 0em">
                <TD style="margin: 0em; padding: 0em">&nbsp;</TD>
                <TD style="margin: 0em; padding: 0em">
                    <BUTTON class="ib" type=submit value="save" onclick="mysubmit()">Save</BUTTON>
                    </TD></TR></TABLE>
                </DIV>
            <DIV class="denseform">
		<?php if ($action=="brainstorm") { ?>
		<SPAN><LABEL for="name">Your name: </LABEL>
		      <INPUT type="TEXT" name="name" value="<?php echo htmlspecialchars($name,ENT_COMPAT);?>"></SPAN>
		<SPAN><LABEL for="email">Your email address: </LABEL>
		      <INPUT type="TEXT" name="email" value="<?php echo htmlspecialchars($email,ENT_COMPAT);?>"></SPAN>
                </DIV><DIV class="denseform">
                <?php } else { ?>
                <INPUT type="hidden" name="name" value="<?php echo htmlspecialchars($name,ENT_COMPAT);?>">
                <INPUT type="hidden" name="email" value="<?php echo htmlspecialchars($email,ENT_COMPAT);?>">
                <?php } ?>
                <?php if (($action=="propose") or ($action=="brainstorm")) { ?>
                <INPUT type="hidden" name="divisionid" value="<?php echo $session["divisionid"];?>">
                <INPUT type="hidden" name="sessionid" value="<?php echo $session["newsessionid"];?>">
                <?php } else { ?>
                <SPAN><LABEL for="sessionid">Session #: </LABEL><A HREF=StaffAssignParticipants.php?selsess=<?php echo $session["sessionid"];?>>
                      <?php echo $session["sessionid"];?></A>
                      <INPUT type="hidden" name="sessionid" value="<?php echo $session["sessionid"];?>"></SPAN>
                <SPAN><LABEL for="divisionid">Division: </LABEL><SELECT name="divisionid">
                     <?php populate_select_from_table("Divisions", $session["divisionid"], "SELECT", FALSE); ?>
                     </SELECT>&nbsp;&nbsp;</SPAN>
                <?php } ?>
                <SPAN><LABEL for="track">Track: </LABEL><SELECT name="track">
                    <?php populate_select_from_table("Tracks", $session["track"], "SELECT", FALSE); ?>
                    </SELECT>&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="type">Type: </LABEL><SELECT name="type">
		    <?php if ($action=="brainstorm") {
                            populate_select_from_query($typequery, $session["type"], "SELECT", FALSE);
                          } else {
                            populate_select_from_table("Types", $session["type"], "SELECT", FALSE); } ?>
                    </SELECT>&nbsp;&nbsp;</SPAN>
	        <?php if (($action=="propose") or ($action=="brainstorm")) { ?>
                <INPUT type="hidden" name="pubstatusid" value="<?php echo $session["pubstatusid"];?>">
                <?php } else { ?>
                <SPAN><LABEL for="pubstatusid">Pub. Status: </LABEL><SELECT name="pubstatusid">
                    <?php populate_select_from_table("PubStatuses", $session["pubstatusid"], "SELECT", FALSE); ?>
                    </SELECT></SPAN>
                <?php } ?>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="title">Title: </LABEL><INPUT type=text size="50" name="title"
                    value="<?php echo htmlspecialchars($session["title"],ENT_COMPAT); ?>">&nbsp;&nbsp;</SPAN>
<?php // Modifiy this in a loop "for each language in LANGUAGE_LIST".
/*
This will depend on a Bios like structure for title and secondtitle.

If there is only one language in LANGUAGE_LIST, then put forth the code above
for Title/Subtitle, otherwise, for each LANGUAGE in LANGUAGE_LIST, prepend the
title/subtitle with the language designator (eg en-uk Title:, or the like),
going down in an array.

Also create a Session Language: set of choices from the LANGUAGE_LIST, so one
can indicate what language the session would be taught in.  This will be passed
as a modified "languagestatusid", so make sure everything agrees, once it is in
place.
*/
?>
                <?php if (($action=="propose") or ($action=="brainstorm")) { ?>
                <INPUT type="hidden" name="invguest" value="invguest">
                <INPUT type="hidden" name="signup" value="">
                <?php } else { ?>
                <SPAN id="sinvguest"><LABEL for="invguest">Invited Guests Only? </LABEL>
                    <INPUT type="checkbox" value="invguest" id="invguest" <?php if ($session["invguest"]) {echo " checked ";} ?>
                    name="invguest">&nbsp;&nbsp;</SPAN>
                <SPAN id="ssignup"><LABEL for="signup">Sign up Req.?</LABEL>
                    <INPUT type="checkbox" value="signup" id="signup" <?php if ($session["signup"]) {echo " checked ";} ?>
                    name="signup">&nbsp;&nbsp;</SPAN>
		<?php } ?>
                <?php if (strtoupper($_SESSION['conallowkids'])=="FALSE") { ?>
	        <SPAN><INPUT type="hidden" name="kids" value="<?php echo $session["kids"];?>"></SPAN>
		<?php } else { ?>
                <SPAN><LABEL for="kids">Kid ?:</LABEL>
                    <SELECT name="kids"><?php populate_select_from_table("KidsCategories", $session["kids"], "SELECT", FALSE); ?></SELECT>
                    </SPAN>
		<?php } ?>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="secondtitle">Subtitle: </LABEL><INPUT type=text size="50" name="secondtitle"
                    value="<?php echo htmlspecialchars($session["secondtitle"],ENT_COMPAT) ?>">&nbsp;&nbsp;</SPAN>
                <INPUT type="hidden" name="languagestatusid"
                    value="<?php echo htmlspecialchars($session["languagestatusid"],ENT_COMPAT); ?>">
                </DIV>
<?php // Modifiy this in a loop "for each language in LANGUAGE_LIST".
/*
This will depend on a Bios like structure for title and secondtitle.

If there is only one language in LANGUAGE_LIST, then put forth the code above
for Title/Subtitle, otherwise, for each LANGUAGE in LANGUAGE_LIST, prepend the
title/subtitle with the language designator (eg en-uk Title:, or the like),
going down in an array.

Also create a Session Language: set of choices from the LANGUAGE_LIST, so one
can indicate what language the session would be taught in.  This will be passed
as a modified "languagestatusid", so make sure everything agrees, once it is in
place.
*/
?>
            <DIV class="denseform">
                <?php if (($action=="propose") or ($action=="brainstorm")) { ?>
                <INPUT type="hidden" name="atten" value="">
                <INPUT type="hidden" name="duration" value="<?php echo htmlspecialchars($session["duration"],ENT_COMPAT) ?>">
                <?php } else { ?>
                <SPAN><LABEL for="atten">Est. Atten.:</LABEL>
                    <INPUT type=text size="3" name="atten" value="<?php
                    echo htmlspecialchars($session["atten"],ENT_COMPAT) ?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="duration">Duration:</LABEL>
                    <INPUT type=text size="5" name="duration" value="<?php
                    echo htmlspecialchars($session["duration"],ENT_COMPAT) ?>">&nbsp;&nbsp;</SPAN>
		<?php } ?>
                <?php if ($action=="brainstorm") {
		    echo "<INPUT type=\"hidden\" name=\"roomset\" value=\"".$session["roomset"]."\">";
                      } else { ?>
                <SPAN><LABEL for="roomset">Room Set: </LABEL>
                    <SELECT name="roomset"><?php populate_select_from_table("RoomSets", $session["roomset"], "SELECT", FALSE); ?>
                    </SELECT>&nbsp;&nbsp;</SPAN>
		<?php } ?>
	        <?php if (($action=="propose") or ($action=="brainstorm")) { ?>
                <INPUT type="hidden" name="status" value="1">
                <?php } else { ?>
                <SPAN><LABEL for="status">Status:</LABEL>
                    <SELECT name="status"><?php populate_select_from_table("SessionStatuses", $session["status"], "", FALSE); ?></SELECT>
                    </SPAN>
		<?php } ?>
                </DIV>
        <HR class="withspace">
        <DIV class="thinbox">
            <TABLE><COL width="100"><COL>
                <TR>
		    <TD class="txtalbl"><LABEL class="dense" for="description_good_web">Web Description (<?php echo $limit_array['min']['web']['desc']."-".$limit_array['max']['web']['desc'] ?>):</LABEL></TD>
                    <TD class="txta"><TEXTAREA class="textlabelarea" cols=80 rows=5 name="description_good_web"
                            ><?php echo htmlspecialchars($session["description_good_web"],ENT_NOQUOTES);?></TEXTAREA></TD>
                    </TR>
                <?php if ($action!="brainstorm") { ?>
                <TR>
                    <TD class="txtalbl"><LABEL class="dense" for="description_good_book">Program Book Description (<?php echo $limit_array['min']['book']['desc']."-".$limit_array['max']['book']['desc'] ?>):</LABEL></TD>
                    <TD class="txta"><TEXTAREA class="textlabelarea" cols=80 name="description_good_book"
                            ><?php echo htmlspecialchars($session["description_good_book"],ENT_NOQUOTES);?></TEXTAREA></TD>
                    </TR>
		<?php } ?>
<?php // Modifiy this in a loop "for each language in LANGUAGE_LIST".
/*
This will depend on a Bios like structure for descriptions.

If there is only one language in LANGUAGE_LIST, then put forth the code above
for web/book descriptions, otherwise, for each LANGUAGE in LANGUAGE_LIST, prepend the
web/book descriptions with the language designator (eg en-uk Web descripton:, or the
like), and it's limits, going down in an array.
*/
?>
                <?php if (($action=="propose") or ($action=="brainstorm")) { ?>
                <INPUT type="hidden" name="persppartinfo" value="">
                <?php } else { ?>
                <TR id="trprospartinfo">
                    <TD class="txtalbl-last"><LABEL class="dense" for="persppartinfo">Prospective Participant Info:</LABEL></TD>
                    <TD class="txta-last"><TEXTAREA class="textlabelarea" cols=80 name="persppartinfo"
                            ><?php echo htmlspecialchars($session["persppartinfo"],ENT_NOQUOTES);?></TEXTAREA></TD>
                    </TR>
		<?php } ?>
                <?php if ($action=="brainstorm") { ?>
                <INPUT type="hidden" name="notesforpart" value="<?php echo "$name $email $badgeid" ?>">
                <INPUT type="hidden" name="servnotes" value="">
                <TR id="trprognotes">
                    <TD class="txtalbl-last" colspan=2><LABEL class="dense" for="notesforprog">Additional info (including if there is a particular presenter you want to present this) for Programming Committee:</LABEL></TD></TR>
                    <TD></TD><TD class="txta-last"><TEXTAREA class="textlabelarea" cols=80 name="notesforprog"><?php echo htmlspecialchars($session["notesforprog"],ENT_NOQUOTES);?></TEXTAREA></TD>
                    </TR>
                </TABLE>
            </DIV>
                <?php } elseif ($action=="propose") { ?>
                <INPUT type="hidden" name="notesforpart" value="<?php echo "$name $email $badgeid" ?>">
                <INPUT type="hidden" name="notesforprog" value="">
                <TR id="trservnotes">
                    <TD class="txtalbl-last" colspan=2><LABEL class="dense" for="servnotes">Additional info, equipment, or services you might want for your class (including if there is a handout or the like):</LABEL></TD></TR>
                    <TD></TD><TD class="txta-last"><TEXTAREA class="textlabelarea" cols=80 name="servnotes"><?php echo htmlspecialchars($session["servnotes"],ENT_NOQUOTES);?></TEXTAREA></TD>
                    </TR>
                </TABLE>
            </DIV>
            <?php } else { ?>
            <DIV class="thinbox">
            <TABLE><COL width="100"><COL>
                <TR>
                    <TD class="txtalbl"><LABEL class="dense" for="notesforpart">Notes for Participants:</LABEL></TD>
                    <TD class="txta"><TEXTAREA class="textlabelarea" cols=80 name="notesforpart"
                            ><?php echo htmlspecialchars($session["notesforpart"],ENT_NOQUOTES);?></TEXTAREA></TD>
                    </TR>
                <TR>
                    <TD class="txtalbl"><LABEL class="dense" for="servnotes">Notes for Tech and Hotel:</LABEL></TD>
                    <TD class="txta"><TEXTAREA class="textlabelarea" cols=80 name="servnotes"
                            ><?php echo htmlspecialchars($session["servnotes"],ENT_NOQUOTES);?></TEXTAREA></TD>
                    </TR>
                <TR>
                    <TD class="txtalbl-last"><LABEL class="dense" for="notesforprog">Notes for Programming Committee:</LABEL></TD>
                    <TD class="txta-last"><TEXTAREA class="textlabelarea" cols=80 rows=5 name="notesforprog"
                            ><?php echo htmlspecialchars($session["notesforprog"],ENT_NOQUOTES);?></TEXTAREA></TD>
                    </TR>
                </TABLE>
		</DIV>
	    <?php } ?>
	    <?php if (($action=="propose") or ($action=="brainstorm")) { ?>
            <INPUT type="hidden" name="servdest[]" value="">
            <INPUT type="hidden" name="featdest[]" value="">
            <?php } else { ?>
        <!-- Replace the below with checkbox-lists -->
        <HR class="withspace"><DIV class="thinbox">
        <TABLE><COL><COL><COL>
        <TR>
        <TD class="nospace">
        <DIV class="thinbox" style="margin-top: 1em; width: 37em; font-size: 85%">
            <DIV class="blockwbox" style="width: 33em; padding-left: 0.5em; padding-right: 0.5em; margin: 0.5em"> <!-- Features Box; -->
                <DIV class="blockstitle"><LABEL>Required Features of Room: <?php echo $action; ?></LABEL></DIV>
                    <DIV class="tab-row">
                        <SPAN class="tab-celltitle"><LABEL for="featsrc">Possible Features</LABEL></SPAN>
                        <SPAN class="tab-cell"><LABEL>&nbsp;</LABEL></SPAN>
                        <SPAN class="tab-celltitle"><LABEL for="featdest[]">Selected Features</LABEL></SPAN>
                        </DIV>
                    <DIV class="tab-row">
                        <SPAN class="tab-cell">
                             <SELECT class="selectfwidth" id="featsrc" name="featsrc" size=6 multiple>
                                <?php populate_multisource_from_table("Features", $session["featdest"]); ?></SELECT>
                             </SPAN>
                        <SPAN class="thickobject">
                            <BUTTON onclick="fadditems(document.sessform.featsrc,document.sessform.featdest)"
                                name="additems" value="additems" type="button">&nbsp;&rarr;&nbsp;</BUTTON>
                            <BUTTON onclick="fdropitems(document.sessform.featsrc,document.sessform.featdest)"
                                name="dropitems" value="dropitems" type="button">&nbsp;&larr;&nbsp;</BUTTON>
                            </SPAN>
                    <DIV class="tab-row">
                        <SPAN class="tab-cell">
                            <SELECT class="selectfwidth" id="featdest" name="featdest[]" size=6 multiple >
                                <?php populate_multidest_from_table("Features", $session["featdest"]); ?></SELECT>
                            </SPAN>
                        </DIV>
                </DIV> <!-- Features -->
</DIV>
            <DIV class="blockwbox" style="width: 33em; padding-left: 0.5em; padding-right: 0.5em; margin: 0.5em"> <!-- Services Box; -->
                <DIV class="blockstitle"><LABEL>Services Required</LABEL></DIV>
                    <DIV class="tab-row">
                        <SPAN class="tab-celltitle"><LABEL for="servsrc">Possible Services</LABEL></SPAN>
                        <SPAN class="tab-cell"><LABEL>&nbsp;</LABEL></SPAN>
                        <SPAN class="tab-celltitle"><LABEL for="servdest[]">Selected Services</LABEL></SPAN>
                        </DIV>
                    <DIV class="tab-row">
                        <SPAN class="tab-cell">
                             <SELECT class="selectfwidth" id="servsrc" name="servsrc" size=6 multiple>
                                <?php populate_multisource_from_table("Services", $session["servdest"]); ?></SELECT>
                             </SPAN>
                        <SPAN class="thickobject">
                            <BUTTON onclick="fadditems(document.sessform.servsrc,document.sessform.servdest)"
                                name="additems" value="additems" type="button">&nbsp;&rarr;&nbsp;</BUTTON>
                            <BUTTON onclick="fdropitems(document.sessform.servsrc,document.sessform.servdest)"
                                name="dropitems" value="dropitems" type="button">&nbsp;&larr;&nbsp;</BUTTON>
                            </SPAN>
                    <DIV class="tab-row">
                        <SPAN class="tab-cell">
                            <SELECT class="selectfwidth" id="servdest" name="servdest[]" size=6 multiple >
                                <?php populate_multidest_from_table("Services", $session["servdest"]); ?></SELECT>
                            </SPAN>
                        </DIV>
                </DIV> <!-- Services -->
            </DIV>
</DIV></TD>
<?php } ?>
<?php if (($action=="propose") or ($action=="brainstorm")) { ?>
<INPUT type="hidden" name="pubchardest[]" value="">
<INPUT type="hidden" name="vendfeatdest[]" value="">
<INPUT type="hidden" name="spacedest[]" value="">
<?php } else { ?>
<TD style="vertical-align: top; padding-left: 1em" id="spubchar">
    <DIV>
        <LABEL class="dense" for="pubchardest">Publication<BR>Characteristics</LABEL>
        </DIV>
    <DIV style="font-size: 85%">
        <SELECT id="pubchardest" name="pubchardest[]" multiple><?php populate_multiselect_from_table("PubCharacteristics",
            $session["pubchardest"]); ?></SELECT>&nbsp;</TD>

        <TD class="nospace">
        <DIV class="thinbox" style="margin-top: 1em; width: 59em; font-size: 85%">
            <DIV class="blockwbox" style="width: 55em; padding-left: 0.5em; padding-right: 0.5em; margin: 0.5em"> <!-- VendorFeatures Box; -->
                <DIV class="blockstitle"><LABEL>Additional Vendor Features for the Space</LABEL></DIV>
                    <DIV class="tab-row">
                        <SPAN class="tab-celltitle"><LABEL for="vendfeatsrc">Possible Features</LABEL></SPAN>
                        <SPAN class="tab-cell"><LABEL>&nbsp;</LABEL></SPAN>
                        <SPAN class="tab-celltitle"><LABEL for="vendfeatdest[]">Selected Features</LABEL></SPAN>
                        </DIV>
                    <DIV class="tab-row">
                        <SPAN class="tab-cell">
                             <SELECT class="selectffwidth" id="vendfeatsrc" name="vendfeatsrc" size=6 multiple>
                                <?php populate_multisource_from_table("VendorFeatures", $session["vendfeatdest"]); ?></SELECT>
                             </SPAN>
                        <SPAN class="thickobject">
                            <BUTTON onclick="fadditems(document.sessform.vendfeatsrc,document.sessform.vendfeatdest)"
                                name="additems" value="additems" type="button">&nbsp;&rarr;&nbsp;</BUTTON>
                            <BUTTON onclick="fdropitems(document.sessform.vendfeatsrc,document.sessform.vendfeatdest)"
                                name="dropitems" value="dropitems" type="button">&nbsp;&larr;&nbsp;</BUTTON>
                            </SPAN>
                    <DIV class="tab-row">
                        <SPAN class="tab-cell">
                            <SELECT class="selectffwidth" id="vendfeatdest" name="vendfeatdest[]" size=6 multiple >
                                <?php populate_multidest_from_table("VendorFeatures", $session["vendfeatdest"]); ?></SELECT>
                            </SPAN>
                        </DIV>
                </DIV> <!-- VendorFeatures -->
</DIV>
            <DIV class="blockwbox" style="width: 55em; padding-left: 0.5em; padding-right: 0.5em; margin: 0.5em"> <!-- Spaces Box; -->
                <DIV class="blockstitle"><LABEL>Spaces Requested</LABEL></DIV>
                    <DIV class="tab-row">
                        <SPAN class="tab-celltitle"><LABEL for="spacesrc">Possible Spaces</LABEL></SPAN>
                        <SPAN class="tab-cell"><LABEL>&nbsp;</LABEL></SPAN>
                        <SPAN class="tab-celltitle"><LABEL for="spacedest[]">Selected Spaces</LABEL></SPAN>
                        </DIV>
                    <DIV class="tab-row">
                        <SPAN class="tab-cell">
                             <SELECT class="selectffwidth" id="spacesrc" name="spacesrc" size=6 multiple>
                                <?php populate_multisource_from_table("VendorSpaces", $session["spacedest"]); ?></SELECT>
                             </SPAN>
                        <SPAN class="thickobject">
                            <BUTTON onclick="fadditems(document.sessform.spacesrc,document.sessform.spacedest)"
                                name="additems" value="additems" type="button">&nbsp;&rarr;&nbsp;</BUTTON>
                            <BUTTON onclick="fdropitems(document.sessform.spacesrc,document.sessform.spacedest)"
                                name="dropitems" value="dropitems" type="button">&nbsp;&larr;&nbsp;</BUTTON>
                            </SPAN>
                    <DIV class="tab-row">
                        <SPAN class="tab-cell">
                            <SELECT class="selectffwidth" id="spacedest" name="spacedest[]" size=6 multiple >
                                <?php populate_multidest_from_table("VendorSpaces", $session["spacedest"]); ?></SELECT>
                            </SPAN>
                        </DIV>
                </DIV> <!-- Spaces -->
            </DIV>
</DIV></TD>
<?php } ?>
</DIV>

</TR></TABLE>
            </DIV>
        <HR class="withspace">
            <DIV style="margin: 0.5em; padding: 0em"><TABLE style="margin: 0em; padding: 0em" ><COL width=600><COL>
              <TR style="margin: 0em; padding: 0em">
                <TD style="margin: 0em; padding: 0em">&nbsp;</TD>
                <TD style="margin: 0em; padding: 0em">
                    <BUTTON class="ib" type=submit value="save" onclick="mysubmit()">Save</BUTTON>
                    </TD></TR></TABLE>
                </DIV>
      <?php echo "<INPUT type=\"hidden\" name=\"action\" value=\"$action\">";?>
      </FORM>
    </DIV>
<?php correct_footer(); } ?>
