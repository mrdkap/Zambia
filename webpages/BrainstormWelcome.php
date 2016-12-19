<?php
require_once('BrainstormCommonCode.php');
global $participant,$message,$message_error,$message2,$congoinfo;
$conid=$_SESSION['conid'];

// LOCALIZATIONS
$title="Class/Presenter Submission";
$description="";
$additionalinfo="";
$message.=$message2;

/* This should be refined with further may_I's in Permissions, but
   for now, we are working off of the raw Phase state. */
$query = <<<EOD
SELECT
    phasetypename
  FROM
      Phase
    JOIN PhaseTypes USING (phasetypeid)
  WHERE
    conid=$conid AND
  phasetypeid in (19,20,21,22,23,24) AND
    phasestate="0"
EOD;

list($rows,$header_array,$phase_array)=queryreport($query,$link,$title,$description,0);

$accepting_string="";
if ($rows > 0) {
  // Nifty Multi-level implode hack here, to get just the phasetypenames of the phases acceptable
  $accepting_string_tmp=implode(", ", array_map(function ($entry) { return $entry['phasetypename']; }, $phase_array));

  // For readability, replace the last ", " with ", and "
  $accepting_string=strrev(implode(strrev(", and "), explode(strrev(", "), strrev($accepting_string_tmp), 2)));
}

// Begin the presentation of the information
topofpagereport($title,$description,$additionalinfo,$message,$message_error);

if ((may_I('BrainstormSubmit')) and ($accepting_string != "")) {
  echo "<H2>We are accepting submissions of types $accepting_string at this time.</H2>\n";
  $verbiage=get_verbiage("BrainstormWelcome_0");
  if ($verbiage != "") {
    echo eval('?>' . $verbiage);
  } else { ?>
<p> Here you can submit new suggestions or look at existing ideas for 
panels, events, movies, films, presentations, speeches, concerts, etc.  
<p> As suggestions come in and we read through them, we will rework 
them, combine similar ideas into a single item, 
split large ones into pieces that will fit in an hour, etc.    
Please expect the suggestions you submit to evolve over time.  
<p> Also, please note that we always have more suggestions than are 
physically possible with the space and time we have, so not 
everything will make it.   We do save good ideas for future conventions. 
<UL> 
  <LI> <A HREF="BrainstormReport.php?status=search">Search </A> for similar ideas or get inspiration.
  <LI> Email <?php echo "<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A> ";?> to suggest modifications on existing suggestion.
  <LI> <A HREF="BrainstormCreateSession.php">Enter a new suggestion.</A>
  <LI> See the list of <A HREF="BrainstormReport.php?status=all">All</A> suggestions (we have seen some and not see others).
  <LI> See the list of <A HREF="BrainstormReport.php?status=unseen">New</A> suggestions that have been entered recently (may not be fit for young eyes, we have not see these yet). 
  <LI> See the list of <A HREF="BrainstormReport.php?status=reviewed">Reviewed</A> suggestions we are currently working through.
  <LI> See the list of <A HREF="BrainstormReport.php?status=likely">Likely to Occur</A> suggestions we are or will allow participants to sign up for. 
  <LI> See the list of <A HREF="BrainstormReport.php?status=scheduled">Scheduled</A> suggestions.  These are very likely to happen at con.
  <LI> Email <?php echo "<A HREF=\"mailto:".$_SESSION['programemail']."\">".$_SESSION['programemail']."</A> ";?> to volunteer to help process these ideas.
<?php 
      if(may_I('Participant')) {
           echo '<li> <a href="welcome.php">Return To Participant View</a>';
           }
      echo "</ul>\n";
      } // end of local words
    } // end of if brainstorming permitted
   else { // Brainstorming not permitted
     $verbiage=get_verbiage("BrainstormWelcome_1");
     if ($verbiage != "") {
       echo eval('?>' . $verbiage);
     } else { ?>
<P> We are not accepting suggestions at this time for <?php echo $_SESSION['conname'];?>.
<P> You may still use the "Search Sessions" tab to view the sessions which have been selected and to read their precis.  Note,
 many of these sessions will still not be scheduled if there is too little participant interest or if a suitable location and time
 slot is not available. </P> 
<?php 
     } // end of local words
   } //end of if brainstorming not permitted ?>
<P>Thank you and we look forward to reading your suggestions,<br>
<A HREF="mailto: <?php echo $_SESSION['programemail']."\">".$_SESSION['programemail']; ?></A></P>
<?php correct_footer(); ?>
