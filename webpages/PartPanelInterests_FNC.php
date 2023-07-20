<?php
/* Function get_session_interests_from_db($badgeid)
   Returns count; Will render its own errors
   Populates global $session_interest with
   ['sessionid'] ['rank'] ['willmoderate'] ['comments']
   and populates $session_interest_index */
function get_session_interests_from_db($badgeid) {
  global $session_interests, $session_interest_index, $title, $link;
  $conid=$_SESSION['conid'];

  $query= <<<EOD
SELECT
    sessionid,
    rank,
    willmoderate,
    comments
  FROM
      ParticipantSessionInterest
  WHERE
    badgeid='$badgeid' AND
    conid=$conid
  ORDER BY
    IFNULL(rank,9999),
    sessionid
EOD;
  if (!$result=mysqli_query($link,$query)) {
    $message.=$query."<BR>Error querying database.<BR>";
    RenderError($title,$message);
    exit();
  }
  $session_interest_count=mysqli_num_rows($result);
  for ($i=1; $i<=$session_interest_count; $i++ ) {
    $session_interests[$i]=mysqli_fetch_array($result, MYSQLI_ASSOC);
    $session_interest_index[$session_interests[$i]['sessionid']]=$i;
  }
  return ($session_interest_count);
}
    
/* Function get_si_session_info_from_db($session_interest_count)
   Will render its own errors
   Reads global $session_interest to get sessionid's to retrieve
   Reads global $session_interest_index
   Populates global $session_interest with
   ['trackname'] ['title'] ['duration'] ['progguiddesc'] ['persppartinfo'] */
function get_si_session_info_from_db($session_interest_count) {
  global $session_interests, $session_interest_index, $title, $link;
  $conid=$_SESSION['conid'];

  // generate sessionidlist.  Probably should be an implode.
  if ($session_interest_count==0) return;
  for ($i=1; $i<=$session_interest_count; $i++ ) {
    $sessionidlist.=$session_interests[$i]['sessionid'].", ";
  }
  $sessionidlist=substr($sessionidlist,0,-2); // drop extra trailing ", "

  // If session for which participant is interested no longer has status
  // valid for signup, then don't bother retrieving it.
  $query= <<<EOD
SELECT
    sessionid,
    trackname,
    concat(title_good_web,if((subtitle_good_web IS NULL),"",concat(": ",subtitle_good_web))) AS title,
    duration,
    desc_good_web,
    persppartinfo
  FROM
      Sessions
    JOIN Tracks USING (trackid)
    JOIN SessionStatuses using (statusid)
    JOIN (SELECT
        sessionid,
	descriptiontext as title_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='title' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') TGW USING (sessionid)
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as subtitle_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='subtitle' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') SGW USING (sessionid)
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as desc_good_web
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
          conid=$conid AND
	  descriptiontypename='description' AND
	  biostatename='good' AND
	  biodestname='web' AND
	  descriptionlang='en-us') DGW USING (sessionid)
  WHERE
    conid=$conid AND
    sessionid in ($sessionidlist) AND
    may_be_scheduled=1
EOD;

  if (!$result=mysqli_query($link,$query)) {
    $message.=$query."<BR>Error querying database.<BR>";
    RenderError($title,$message);
    exit();
  }
  $num_rows=mysqli_num_rows($result);
  for ($i=1; $i<=$num_rows; $i++ ) {
    $this_row=mysqli_fetch_array($result, MYSQLI_ASSOC);
    $j=$session_interest_index[$this_row['sessionid']];
    $session_interests[$j]['trackname']=$this_row['trackname'];
    $session_interests[$j]['title']=$this_row['title'];
    $session_interests[$j]['duration']=$this_row['duration'];
    $session_interests[$j]['desc_good_web']=$this_row['desc_good_web'];
    $session_interests[$j]['persppartinfo']=$this_row['persppartinfo'];
  }
  //echo "<P>message: $message</P>";
  return (true);
}

// Function get_session_interests_from_post()
// Reads the data posted by the browser form and populates
// the $partavail global variable with it.  Returns
// the maximum index value.
//
function get_session_interests_from_post() {
  global $session_interests,$session_interest_index;
  $i=1;
  while (isset($_POST["sessionid$i"])) {
    $session_interests[$i]['sessionid']=$_POST["sessionid$i"];
    $session_interest_index[$_POST["sessionid$i"]]=$i;
    $session_interests[$i]['rank']=$_POST["rank$i"];
    $session_interests[$i]['delete']=(isset($_POST["delete$i"]))?true:false;
    $session_interests[$i]['comments']=stripslashes($_POST["comments$i"]);
    $session_interests[$i]['willmoderate']=(isset($_POST["mod$i"]))?true:false;
    $i++;
  }
  $i--;
  //echo "<P>I: $i</P>";
  //print_r($session_interest_index);
  return($i);
}

/* Function update_session_interests_in_db($session_interest_count)
   Reads the data posted by the browser form and populates
   the $partavail global variable with it.  Returns
   the maximum index value. */
function update_session_interests_in_db($badgeid,$session_interest_count) {
  global $session_interests,$link,$title,$message;
  //print_r($session_interests);
  $deleteSessionIds="";
  $noDeleteCount=0;
  for ($i=1; $i<=$session_interest_count; $i++) {
    if ($session_interests[$i]['delete']) {
      $deleteSessionIds.=$session_interests[$i]['sessionid'].", ";
    } else {
      $noDeleteCount++;
    }
  }
  if ($deleteSessionIds) {
    $deleteSessionIds=substr($deleteSessionIds,0,-2); //drop trailing ", "
    $query="DELETE FROM ParticipantSessionInterest WHERE badgeid=\"$badgeid\" AND conid=".$_SESSION['conid']." AND sessionid IN ($deleteSessionIds)";
    if (!mysqli_query($link,$query)) {
      $message=$query."<BR>Error updating database.  Database not updated.";
      RenderError($title,$message);
      exit();
    }
    $deleteCount=mysqli_affected_rows($link);
    $message="$deleteCount record(s) deleted.<BR>\n";
  }
  if ($noDeleteCount) {
    $query = "REPLACE INTO ParticipantSessionInterest (badgeid, sessionid, conid, rank, willmoderate, comments, ibadgeid) VALUES ";
    for ($i=1;$i<=$session_interest_count;$i++) {
      if ($session_interests[$i]['delete']) continue;
      $query.="(\"$badgeid\",{$session_interests[$i]['sessionid']},";
      $query.=$_SESSION['conid'].",";
      $rank=$session_interests[$i]['rank'];
      $query.=($rank==""?"null":$rank).",";
      $query.=($session_interests[$i]['willmoderate']?1:0).",";
      $query.="\"".mysql_real_escape_string($session_interests[$i]['comments'],$link)."\",";
      $query.=$_SESSION['badgeid'];
      $query.="),";
    }
    $query=substr($query,0,-1); // drop trailing ","
    if (!mysqli_query($link,$query)) {
      $message=$query."<BR>Error updating database.  Database not updated.";
      RenderError($title,$message);
      exit();
    }
    $noDeleteCount=mysqli_affected_rows($link)/2; // Replace affects twice as many rows because it deletes then inserts
    $message.="$noDeleteCount record(s) updated or saved.<BR>\n";
  }
  return (true);
}
?>
