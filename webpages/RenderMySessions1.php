<?php
function RenderMySessions1($result) {

  echo '<FORM name="resform" method=POST action="SubmitMySessions1.php">';

  if (may_I('my_panel_interests')) {
    echo "<DIV class=\"submit\" id=\"submit\"><BUTTON class=\"SubmitButton\" type=\"submit\" name=\"save\">Save</BUTTON></DIV>\n";
    echo "<p> If you have selected any panels, please remember ";
    echo "to SAVE before leaving the page. ";
    echo "(Use either \"Save\" buttons at the top or bottom.) </p>";
    echo "<p> You will find below the results of your search.  ";
    echo "We have included the session id, track, title, duration, ";
    echo "a check box for you to indicate interest, followed by ";
    echo "the description as it will appear in the program guide ";
    echo "and some additional information for you as a ";
    echo "prospective panelist.";  
  }
  echo "    <HR>\n";
  echo "    <TABLE>\n";
  echo "        <COL><COL><COL><COL>\n";
  $i=0;
  while (list($sessionid,$trackname,$title,$duration,$desc_good_web,$persppartinfo,$rbadgeid)= mysql_fetch_array($result, MYSQL_NUM)) {
    echo "        <INPUT type=hidden name=\"sessionid".$i."\" value=\"".$sessionid."\"></TD>\n";
    echo "        <TR>\n";
    echo "            <TD rowspan=4 class=\"border0000\" id=\"sessidtcell\"><b>".$sessionid."&nbsp;&nbsp;";
    echo "            <TD class=\"border0000\"><b>".$trackname."</TD>\n";
    echo "            <TD class=\"border0000\"><b>".htmlspecialchars($title,ENT_NOQUOTES)."</TD>\n";
    echo "            <TD class=\"border0000\"><b>".$duration."</TD>\n";
    echo "        </TR>\n";

    if (may_I('my_panel_interests')) {
      echo "        <TR>\n";
      echo "            <TD colspan=2 class=\"addbox\">Add this panel to my list: <INPUT type=\"checkbox\" name=\"int".$i."\" ";
      echo ((strlen($rbadgeid)>1)?"checked":"")."><INPUT type=hidden name=\"checked".$i."\" value=\"";
      echo ((strlen($rbadgeid)>1)?"1":"0")."\"></TD>\n";
      echo "        </TR>\n";
    }
    echo "        <TR><TD colspan=3 class=\"border0010\">".htmlspecialchars($desc_good_web,ENT_NOQUOTES)."</TD></TR>\n";
    echo "        <TR><TD colspan=3 class=\"border0000\">".htmlspecialchars($persppartinfo,ENT_NOQUOTES)."</TD></TR>\n";
    echo "        <TR><TD colspan=4 class=\"border0020\">&nbsp;</TD></TR>\n";
    echo "        <TR><TD colspan=4 class=\"border0000\" id=\"smallspacer\">&nbsp;</TD></TR>\n";
    $i++;
  }
  echo "        <INPUT type=\"hidden\" name=\"maxrow\" value=\"".($i-1)."\">\n";
  echo "    </TABLE>";
  if (may_I('my_panel_interests')) {
    echo "<DIV class=\"submit\" id=\"submit2\"><BUTTON class=\"SubmitButton\" type=\"submit\" name=\"save\">Save</BUTTON></DIV>\n";
  }
  echo "      </FORM>\n";
}
?>
