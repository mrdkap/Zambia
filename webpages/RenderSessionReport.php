<?php
function RenderSessionReport() {
  global $result; 
  require_once('StaffCommonCode.php');

  //This is not called by anything, perhaps it should be removed.

  $title="Session Report";
  $description="<P>Here are the results of your search.  The report includes Session id, track, title, duration, estimated attendance, web and book description, and, notes for prospective participants.</P>";
  $additionalinfo="";

  topofpagereport($title,$description,$additionalinfo,$message,$message_error);
?>

<TABLE>
<?php
   while (list($sessionid,$trackname,$title,$duration,$estatten,$desc_good_web,$desc_good_book,$persppartinfo)= mysql_fetch_array($result, MYSQL_NUM)) {
     echo "        <TR>\n";
     echo "            <TD rowspan=3 class=\"border0000\" id=\"sessidtcell\">
<A HREF=\"EditSession.php?id=".$sessionid."\"><b>".$sessionid."</a>&nbsp;&nbsp;</TD>\n";
     echo "            <TD class=\"border0000\"><b>".$trackname."</TD>\n";
     echo "            <TD class=\"border0000\"><b>".htmlspecialchars($title,ENT_NOQUOTES)."</TD>\n";
     echo "            <TD class=\"border0000\"><b>".$duration." hr</TD>\n";
     echo "            <TD rowspan=3 class=\"border0000\">".$estatten."&nbsp;&nbsp;</TD>\n";
     echo "            </TR>\n";
     echo "        <TR><TD colspan=3 class=\"border0010\">".htmlspecialchars($desc_good_web,ENT_NOQUOTES)."</TD></TR>\n";
     echo "        <TR><TD colspan=3 class=\"border0010\">".htmlspecialchars($desc_good_book,ENT_NOQUOTES)."</TD></TR>\n";
     echo "        <TR><TD colspan=3 class=\"border0000\">".htmlspecialchars($persppartinfo,ENT_NOQUOTES)."</TD></TR>\n";
     echo "        <TR><TD colspan=5 class=\"border0020\">&nbsp;</TD></TR>\n";
   }
?>
</TABLE>
<?php
    correct_footer();
}
?>
