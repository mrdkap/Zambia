<?php
function render_session_interests($badgid,$session_interest_count,$message,$message_error) {
    global $session_interests, $title;
    topofpagereport($title,$description,$additionalinfo,$message,$message_error);

    /*
      removing this until I can figure out the validate function fixes

    // "Add" Section
    echo "<FORM name=\"addform\" method=POST action=\"PartPanelInterests_POST.php\">\n";
    echo "    <table>\n";
    echo "        <tr>\n";
    echo "            <td>Add Session ID to my List</td>\n";
    echo "            <td><Input type=\"text\" name=\"addsessionid\" size=10></td>\n";
    echo "            <td><BUTTON type=\"submit\" name=\"add\" id=\"add\">Add</BUTTON></td>\n";
    echo "            </tr>\n";
    echo "        </table>\n";
    echo "    </FORM>\n";
    echo "<HR>\n";
    */

    // "Update Ranks" Section
    echo "<FORM name=\"sessionform\" method=POST action=\"PartPanelInterests_POST2.php\">\n";
    echo "<DIV class=\"submit\" id=\"submit\"><BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submitranks\">Save</BUTTON></DIV>\n";
    echo "<P>Please use the following scale when ranking your interest in the panels you have chosen:  </P>\n";
    echo "<P>1 - Oooh! Oh! Pick Me!, 2-3 - I'd like to if I can, 4-5 - Meh, I can take it or leave it. </P>\n";
    echo "<P>You are limited to 4 sessions each of preferences 1-4.  There is no limit to the number of sessions for which you can express preference 5.</P>\n";
    echo "<H3>List of Sessions in Which I'm Interested in Participating</H3>\n";
    echo "<TABLE>\n";
    echo "  <COL><COL><COL><COL>\n";
    $j=1; //use $j so that skipped sessions don't skip numbering
    for ($i=1; $i<=$session_interest_count; $i++) {
        if (!$session_interests[$i]['title']) continue;
        echo "    <TR>\n";
        echo "        <TD rowspan=6 class=\"border0000 hilit\" id=\"sessidtcell\">{$session_interests[$i]['sessionid']}";
        echo "            <INPUT type=\"hidden\" name=\"sessionid$j\" value=\"{$session_interests[$i]['sessionid']}\"></TD>\n";
        echo "        <TD class=\"border0000 hilit vatop\">{$session_interests[$i]['trackname']}</TD>\n";
        echo "        <TD colspan=2 class=\"border0000 hilit vatop\">".htmlspecialchars($session_interests[$i]['title'],ENT_NOQUOTES)."</TD>\n";
        echo "        <TD class=\"border0000 hilit vatop\">Duration: {$session_interests[$i]['duration']}</TD>\n";
        echo "        </TR>\n";
	echo "    <TR>\n";
        echo "        <TD colspan=2 class=\"border0000 usrinp\">Remove this panel from my list:<INPUT type=\"checkbox\" value=1 name=\"delete$j\"></TD></TR>\n";
        echo "    <TR>\n";
        echo "        <TD class=\"border0000 usrinp\">Rank: <INPUT type=\"text\" size=3 name=\"rank$j\" value=\"{$session_interests[$i]['rank']}\"></TD>\n";
        echo "        <TD class=\"border0000 usrinp\">I'd like to moderate this panel:<INPUT type=\"checkbox\" value=1 name=\"mod$j\" ";
        echo "            ".(($session_interests[$i]['willmoderate'])?"checked":"")."></TD>\n";
        echo "        </TR>\n";
        echo "    <TR>\n";
        echo "        <TD  class=\"border0000 usrinp\" colspan=4>My notes regarding this panel for Programming and other panel participants: <br>";
        echo "            <TEXTAREA height=5em cols=80 name=\"comments$j\" id=\"intCmnt\">". htmlspecialchars( $session_interests[$i]['comments'],ENT_COMPAT)."</TEXTAREA></TD>\n";
        echo "        </TR>\n";
        echo "    <TR>\n";
        echo "        <TD colspan=4 class=\"border0020\" id=\"smallspacer\">&nbsp;</TD>\n";
        echo "        </TR>\n";
        echo "    <TR>\n";
        echo "        <TD colspan=4 class=\"border0010\">".htmlspecialchars($session_interests[$i]['desc_good_web'],ENT_NOQUOTES)."</TD>\n";
        echo "        </TR>\n";
        echo "    <TR>\n";
        echo "        <TD colspan=4 class=\"border0000\">".htmlspecialchars($session_interests[$i]['persppartinfo'],ENT_NOQUOTES)."</TD>\n";
        echo "        </TR>\n";
        echo "    <TR>\n";
        echo "        <TD colspan=4 class=\"border0020\" id=\"smallspacer\">&nbsp;</TD>\n";
        echo "        </TR>\n";
        echo "    <TR>\n";
        echo "        <TD colspan=5 class=\"border0000\" id=\"smallspacer\">&nbsp;</TD>\n";
        echo "        </TR>\n";
		$j++;
        }
    echo "    </TABLE>\n";
    echo "<DIV class=\"submit\" id=\"submit2\"><BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submitranks\">Save</BUTTON></DIV>\n";
    echo "</FORM>\n";
    correct_footer();
    }
?>