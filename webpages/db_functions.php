<?php
include ('../Local/db_name.php');

/* Function prepare_db()
   Opens database channel. */
function prepare_db() {
  global $link, $message, $message_error;
  $link = mysql_connect(DBHOSTNAME,DBUSERID,DBPASSWORD);
  if ($link===false) return (false);
  return (mysql_select_db(DBDB,$link));
}

/* Function vendor_prepare_db()
   Opens database channel. */
function vendor_prepare_db() {
  global $vlink, $message, $message_error;
  $vlink = mysql_connect(VENDORHOSTNAME,VENDORUSERID,VENDORPASSWORD);
  if ($vlink===false) return (false);
  return (mysql_select_db(VENDORDB,$vlink));
}

/* Function record_session_history($sessionid, $badgeid, $name, $email, $editcode, $statusid)
   The table SessionEditHistory has a timestamp column which is automatically set to the
   current timestamp by MySQL. */
function record_session_history($sessionid, $badgeid, $name, $email, $editcode, $statusid) {
  global $link, $message, $message_error;
  $conid=$_SESSION['conid'];
  $name=mysql_real_escape_string($name,$link);
  $email=mysql_real_escape_string($email,$link);

  $query = <<<EOD
INSERT
  INTO
      SessionEditHistory
  SET
    sessionid=$sessionid,
    conid=$conid,
    badgeid="$badgeid",
    name="$name",
    email_address="$email",
    sessioneditcode=$editcode,
    statusid=$statusid
EOD;
  $result = mysql_query($query,$link);
  if (!$result) {
    $message_error.=$query."<BR>\n".mysql_error($link);
    return $result;
  }
  return(true);
}

/* Function get_name_and_email(&$name, &$email)
   Gets name and email from db if they are available and not already set
   returns FALSE if error condition encountered.  Error message in global $message_error */
function get_name_and_email(&$name, &$email) {
  global $link, $message, $message_error, $badgeid;
  if (isset($name) && $name!='') {
    //$name="foo"; //for debugging only
    return(TRUE);
  }
  if (isset($_SESSION['name'])) {
    $name=$_SESSION['name'];
    $email=$_SESSION['email'];
    //error_log("get_name_and_email found a name in the session variables.");
    return(TRUE);
  }
  if (may_I('Staff') || may_I('Participant')) { //name and email should be found in db if either set
    $query="SELECT pubsname from Participants where badgeid='$badgeid'";
    //error_log($query); //for debugging only
    $result=mysql_query($query,$link);
    if (!$result) {
      $message_error.=$query."<BR> ";
      $message_error.=mysql_error($link)."<BR> ";
      $message_error.="Error reading from database. No further execution possible.<BR> ";
      error_log($message_error);
      return(FALSE);
    }
    $name=mysql_result($result, 0);
    if ($name=='') {
      $name=' '; //if name is null or '' in db, set to ' ' so it won't appear unpopulated in query above
    }
    $query="SELECT pubsname,email from Participants where badgeid='$badgeid'";
    $result=mysql_query($query,$link);
    if (!$result) {
      $message_error.=$query."<BR> ";
      $message_error.=mysql_error($link)."<BR> ";
      $message_error.="Error reading from database. No further execution possible.<BR> ";
      error_log($message_error);
      return(FALSE);
    }
    if ($name==' ') {
      $name=mysql_result($result, 0, 0);
    } // name will be ' ' if pubsname is null.  In that case use badgename.
    $email=mysql_result($result, 0, 1);
  }
  return(TRUE); //return TRUE even if didn't retrieve from db because there's nothing to be done
}

/* Function populate_select_from_table(...)
   Reads parameters (see below) and a specified table from the db.
   Outputs HTML of the "<OPTION>" values for a Select control.
   set $default_value=-1 for no default value (note not really supported by HTML)
   set $default_value=0 for initial value to be set as $option_0_text
   otherwise the initial value will be equal to the row whose id == $default_value
   assumes id's in the table start at 1
   if $default_flag is true, the option 0 will always appear.
   if $default_flag is false, the option 0 will only appear when $default_value is 0. */
function populate_select_from_table($table_name, $default_value, $option_0_text, $default_flag) {
  global $link, $message, $message_error;
  if ($default_value==0) {
    echo "<OPTION value=0 selected>".$option_0_text."</OPTION>\n";
  } elseif ($default_flag) {
    echo "<OPTION value=0>".$option_0_text."</OPTION>\n";
  }
  $result=mysql_query("Select * from ".$table_name." order by display_order",$link);
  while ($arow = mysql_fetch_array($result, MYSQL_NUM)) {
    $option_value=$arow[0];
    $option_name=$arow[1];
    echo "<OPTION value=".$option_value." ";
    if ($option_value==$default_value)
      echo "selected";
    echo ">".$option_name."</OPTION>\n";
  }
}

/* Function populate_select_from_query(...)
   Reads parameters (see below) and a specified query for the db.
   Outputs HTML of the "<OPTION>" values for a Select control.
   set $default_value=-1 for no default value (note not really supported by HTML)
   set $default_value=0 for initial value to be set as $option_0_text
   otherwise the initial value will be equal to the row whose id == $default_value
   assumes id's in the table start at 1
   if $default_flag is true, the option 0 will always appear.
   if $default_flag is false, the option 0 will only appear when $default_value is 0. */
function populate_select_from_query($query, $default_value, $option_0_text, $default_flag) {
  global $link, $message, $message_error;
  if ($default_value==0) {
    echo "<OPTION value=0 selected>".$option_0_text."</OPTION>\n";
  } elseif ($default_flag) {
    echo "<OPTION value=0>".$option_0_text."</OPTION>\n";
  }
  $result=mysql_query($query,$link);
  while (list($option_value,$option_name)= mysql_fetch_array($result, MYSQL_NUM)) {
    echo "<OPTION value=".$option_value." ";
    if ($option_value==$default_value) {
      echo "selected";
    }
    echo ">".$option_name."</OPTION>\n";
  }
}

/* Function populate_select_from_query_inline(...)
   Reads parameters (see below) and a specified query for the db.
   Outputs HTML of the "<OPTION>" values for a Select control.
   set $default_value=-1 for no default value (note not really supported by HTML)
   set $default_value=0 for initial value to be set as $option_0_text
   otherwise the initial value will be equal to the row whose id == $default_value
   assumes id's in the table start at 1
   if $default_flag is true, the option 0 will always appear.
   if $default_flag is false, the option 0 will only appear when $default_value is 0. */
function populate_select_from_query_inline($query, $default_value, $option_0_text, $default_flag) {
  global $link, $message, $message_error;
  $returnstring="";
  if ($default_value==0) {
    $returnstring.="<OPTION value=0 selected>".$option_0_text."</OPTION>\n";
  } elseif ($default_flag) {
    $returnstring.="<OPTION value=0>".$option_0_text."</OPTION>\n";
  }
  $result=mysql_query($query,$link);
  while (list($option_value,$option_name)= mysql_fetch_array($result, MYSQL_NUM)) {
    $returnstring.="<OPTION value=".$option_value." ";
    if ($option_value==$default_value) {
      $returnstring.="selected";
    }
    $returnstring.=">".$option_name."</OPTION>\n";
  }
  return($returnstring);
}

/* Function populate_checkbox_block_from_array($label, $element_list, $key, $value, $box_array)
   Reads parameters (see below) and an array to work from, and
   produces a series of checkboxes that can be selected.
   label: The label for the table output
   element_list: The list of already selected items
   key: The part of the array to key off of.
   value: The part of the array to get the value out of.
   array: The array of possible items. */
function populate_checkbox_block_from_array($label, $element_list, $key, $value, $box_array) {
  global $link, $message, $message_error;
  $returnstring="";
  $list_array=explode(",",$element_list);
  for ($i=1; $i<=count($box_array); $i++) {
    if(in_array($box_array[$i][$key],$list_array)) {
      $returnstring.="<INPUT type=\"hidden\" name=\"was".$label."[".$box_array[$i][$key]."]\" value=\"indeed\">\n";
      $returnstring.="<INPUT type=\"checkbox\" name=\"".$label."[".$box_array[$i][$key]."]\" value=\"checked\" checked>".$box_array[$i][$value]."\n";
    } else {
      $returnstring.="<INPUT type=\"hidden\" name=\"was".$label."[".$box_array[$i][$key]."]\" value=\"not\">\n";
      $returnstring.="<INPUT type=\"checkbox\" name=\"".$label."[".$box_array[$i][$key]."]\" value=\"checked\">".$box_array[$i][$value]."\n";
    }
  }
  $returnstring.="<INPUT type=\"hidden\" name=\"".$label."_list\" value=\"".$element_list."\">\n";
  return($returnstring);
}

/* Function populate_radio_block_from_array($label, $element_list, $key, $value, $button_array)
   Reads parameters (see below) and an array to work from, and
   produces a series of radio buttons that can be selected.
   label: The label for the table output
   element_list: The list of already selected items
   key: The part of the array to key off of.
   value: The part of the array to get the value out of.
   button_array: The array of possible items. */
function populate_radio_block_from_array($label, $element_list, $key, $value, $button_array) {
  global $link, $message, $message_error;
  $returnstring="";
  $list_array=explode(",",$element_list);
  for ($i=1; $i<=count($button_array); $i++) {
    if(in_array($button_array[$i][$key],$list_array)) {
      $returnstring.="<INPUT type=\"radio\" name=\"".$label."\" id=\"".$label."\" value=\"".$button_array[$i][$value]."\" checked=\"checked\">".$button_array[$i][$key]."\n";
    } else {
      $returnstring.="<INPUT type=\"radio\" name=\"".$label."\" id=\"".$label."\" value=\"".$button_array[$i][$value]."\">".$button_array[$i][$key]."\n";
    }
  }
  $returnstring.="<INPUT type=\"hidden\" name=\"was".$label."\" value=\"".$element_list."\">\n";
  return($returnstring);
}

/* Function populate_multiselect_from_table(...)
   Reads parameters (see below) and a specified table from the db.
   Outputs HTML of the "<OPTION>" values for a Select control with
   multiple enabled.
   assumes id's in the table start at 1 '
   skipset is array of integers of values of id from table to preselect
   assumes mulit-year element in all useage */
function populate_multiselect_from_table($table_name, $skipset) {
  global $link, $message, $message_error;
  // error_log("Zambia->populate_multiselect_from_table->\$skipset: ".print_r($skipset,TRUE)."\n"); // only for debugging
  if ($skipset=="") $skipset=array(-1);
  $result=mysql_query("SELECT * from ".$table_name." WHERE conid=".$_SESSION['conid']." ORDER BY display_order",$link);
  while (list($option_value,$option_name)= mysql_fetch_array($result, MYSQL_NUM)) {
    echo "<OPTION value=\"".$option_value."\"";
    if (array_search($option_value,$skipset)!==FALSE) {
      echo " selected";
    }
    echo">$option_name</OPTION>\n";
  }
}

/* Function populate_multisource_from_table(...)
   Reads parameters (see below) and a specified table from the db.
   Outputs HTML of the "<OPTION>" values for a Select control associated
   with the *source* of an active update box.
   assumes id's in the table start at 1 '
   skipset is array of integers of values of id from table not to include
   assumes mulit-year element in all useage */
function populate_multisource_from_table($table_name, $skipset) {
  global $link, $message, $message_error;
  if ($skipset=="") $skipset=array(-1);
  $result=mysql_query("SELECT * from ".$table_name." WHERE conid=".$_SESSION['conid']." ORDER BY display_order",$link);
  while (list($option_value,$option_name)= mysql_fetch_array($result, MYSQL_NUM)) {
    if (array_search($option_value,$skipset)===false) {
      echo "<OPTION value=".$option_value.">".$option_name."</OPTION>\n";
    }
  }
}

/* Function populate_multidest_from_table(...)
   Reads parameters (see below) and a specified table from the db.
   Outputs HTML of the "<OPTION>" values for a Select control associated
   with the *destination* of an active update box.
   assumes id's in the table start at 1                        '
   skipset is array of integers of values of id from table to include
   in "dest" because they were skipped from "source"
   assumes mulit-year element in all useage */
function populate_multidest_from_table($table_name, $skipset) {
  global $link, $message, $message_error;
  if ($skipset=="") $skipset=array(-1);
  $result=mysql_query("SELECT * from ".$table_name." WHERE conid=".$_SESSION['conid']." ORDER BY display_order",$link);
  while (list($option_value,$option_name)= mysql_fetch_array($result, MYSQL_NUM)) {
    if (array_search($option_value,$skipset)!==false) {
      echo "<OPTION value=".$option_value.">".$option_name."</OPTION>\n";
    }
  }
}

/* Function update_session()
   Takes data from global $session array and updates the
   tables Sessions, Descriptions, SessionHasFeature, SessionHasPubChar,
   SessionHasService, SessionHasVendorFeature, SessionHasVendorSpace
   pocketprogtext=description_good_book so zeroed out
   progguiddesc=description_good_web so zeroed out */
function update_session() {
  global $link, $session, $message, $message_error;

  if ($_SESSION['condurationminutes']=="TRUE") {
    $duration="duration='".conv_min2hrsmin($session["duration"],$link)."'";
  } else {
    $duration="duration='".mysql_real_escape_string($session["duration"],$link)."'";
  }

  // Fix secondtitle to subtitle
  $session["subtitle"]=$session["secondtitle"];

  // Fix submitable characters
  $checklist=array("title","subtitle","secondtitle","description_good_web","description_good_book");
  foreach ($checklist as $i) {
    $workstring=$session[$i];
    $desctext0=iconv('UTF-8', 'ISO-8859-1//TRANSLIT',$workstring);
    $desctext1=mb_convert_encoding($workstring, 'ISO-8859-1', 'UTF-8');
    $donestring=$desctext0;
    if (empty($donestring)) {$donestring=$desctext1;}
    if (empty($donestring)) {$donestring=$workstring;}
    $session[$i]=$donestring;
  }

  $pairedvalue_array=array("trackid=".$session["track"],
			   "typeid=".$session["type"],
			   "divisionid=".$session["divisionid"],
			   "pubstatusid=".$session["pubstatusid"],
			   "title='".mysql_real_escape_string($session["title"],$link)."'",
			   "secondtitle='".mysql_real_escape_string($session["secondtitle"],$link)."'",
			   "pocketprogtext=''",
			   "progguiddesc=''",
			   "persppartinfo='".mysql_real_escape_string($session["persppartinfo"],$link)."'",
			   $duration,
			   "estatten=".($session["atten"]!=""?$session["atten"]:"null"),
			   "kidscatid=".$session["kids"],
			   "signupreq=".($session["signup"]?"1":"0"),
			   "invitedguest=".($session["invguest"]?"1":"0"),
			   "roomsetid=".$session["roomset"],
			   "notesforpart='".mysql_real_escape_string($session["notesforpart"],$link)."'",
			   "servicenotes='".mysql_real_escape_string($session["servnotes"],$link)."'",
			   "statusid=".$session["status"],
			   "notesforprog='".mysql_real_escape_string($session["notesforprog"],$link)."'");

  $match_string="sessionid=".$session['sessionid']." AND conid=".$_SESSION['conid'];

  $message.=update_table_element_extended_match ($link,$title,"Sessions",$pairedvalue_array, $match_string);

  /* There should be a more dignified way of doing this, but for the
     meantime, while we are in transition...

     + descriptiontype (i) is title, subtitle and description, and
     should probably be harvested from the descrioptiontype so if more
     are added, at any point in time, they can be used.

     + biostateid (j) is currently locked to 3, since we aren't
     actually editing these yet, and here should be locked to 1,
     otherwise, since this is the raw creation.

     + biodestid (k) is web and book destinations, and should be
     actually harvested from BioDests, so if more are added, at any
     point in time, they can be used.

     + descriptionlang (l) is currently fixed to en-us, if we ever
     actually go multi-lingual this will need to be fixed. */

  // Create the Descriptions array entries.

  $desc_array[1][3][1]['en-us']=$session["title"];
  $desc_array[1][3][2]['en-us']=$session["title"];
  $desc_array[2][3][1]['en-us']=$session["subtitle"];
  $desc_array[2][3][2]['en-us']=$session["subtitle"];
  $desc_array[3][3][1]['en-us']=$session["description_good_web"];
  $desc_array[3][3][2]['en-us']=$session["description_good_book"];

  // Run the loops.
  $j=3;
  $l="en-us";
  for ($i=1; $i<=3; $i++) {
    for ($k=1; $k<=2; $k++) {

      // Check to see if it exists.
      if (isset($desc_array[$i][$j][$k][$l]) and ($desc_array[$i][$j][$k][$l] != "")) {
	$wherestring="sessionid=".$session['sessionid']." AND conid=".$_SESSION['conid'];
	$wherestring.=" AND descriptiontypeid=$i AND biostateid=$j AND biodestid=$k AND descriptionlang=\"$l\"";
	$query="SELECT descriptionid FROM Descriptions WHERE $wherestring";

	// Retrieve query
	list($desctestrows,$header_array,$desctest_array)=queryreport($query,$link,$title,$description,0);

	// If it doesn't exist, create it, if it does, update it.
	if ($desctestrows==0) {
	  $element_array=array('sessionid','conid','descriptiontypeid','biostateid',
			       'biodestid','descriptionlang','descriptiontext');
	  $value_array=array($session['sessionid'],$_SESSION['conid'],$i,$j,$k,$l,
			     htmlspecialchars_decode($desc_array[$i][$j][$k][$l]));
	  $message.=submit_table_element($link,$title,"Descriptions",$element_array,$value_array);
	} else {
	  $pairedvalue_array=array("descriptiontext='".mysql_real_escape_string($desc_array[$i][$j][$k][$l])."'");
	  $match_string=$wherestring;
	  $message.=update_table_element_extended_match ($link,$title,"Descriptions",$pairedvalue_array, $match_string);
	}
      }
    }
  }

  /* Set up the various feature tables, so we can loop across all of them. */
  $tn_array["featdest"]="SessionHasFeature";
  $te_array["featdest"]="featureid";
  $tn_array["servdest"]="SessionHasService";
  $te_array["servdest"]="serviceid";
  $tn_array["pubchardest"]="SessionHasPubChar";
  $te_array["pubchardest"]="pubcharid";
  $tn_array["vendfeatdest"]="SessionHasVendorFeature";
  $te_array["vendfeatdest"]="vendorfeatureid";
  $tn_array["spacedest"]="SessionHasVendorSpace";
  $te_array["spacedest"]="vendorspaceid";

  // Loop across the keys of the array created above.
  foreach (array_keys($tn_array) as $j) {

    // First, delete all old entries.
    $match_string="sessionid=".$session['sessionid']." AND conid=".$_SESSION['conid'];
    $message.=delete_table_element($link, $title, $tn_array[$j],$match_string);

    // Then, if any entries exist, create them, possibly anew.
    if ($session[$j]!="") {
      for ($i=0 ; $session[$j][$i]!="" ; $i++ ) {
	$element_array=array('sessionid','conid',$te_array[$j]);
	$value_array=array($session["sessionid"],$_SESSION['conid'],$session[$j][$i]);
	$message.=submit_table_element($link,$title,$tn_array[$j],$element_array,$value_array);
      }
    }
  }

  /* Set up the Vendor Adjustment Value, in case it every gets used.*/
  $query="DELETE from SessionHasVendorAdjust where sessionid=".$session["sessionid"];
  $query.=" AND conid=".$_SESSION['conid'];
  if (($session["vendoradjustvalue"]!="") or ($session["vendoradjustnote"]!="")) {
    $query="INSERT into SessionHasVendorAdjust set sessionid=".$id." ";
    if ($session["vendoradjustvalue"]!="") {
      $query.=", vendoradjustvalue=";
      $query.=$session["vendoradjustvalue"]." ";
    }
    if ($session["vendoradjustnote"]!="") {
      $query.=", vendoradjustnote='";
      $query.=$session["vendoradjustnote"]."' ";
    }
    $query.=", conid=".$_SESSION['conid'];
    $result = mysql_query($query,$link);
    if (!$result) {
      $message_error.=mysql_error($link);
      $message_error.=" query=$query";
      return $message_error;
    }
  }
  return true;
}

/* Function get_next_session_id()
   Reads Session table from db to determine next unused value
   of sessionid. */
function get_next_session_id() {
  global $link, $message, $message_error;

  $result=mysql_query("SELECT MAX(sessionid) FROM Sessions where conid=".$_SESSION['conid'],$link);
  if (!$result) {return "";}
  list($maxid)=mysql_fetch_array($result, MYSQL_NUM);
  if (!$maxid) {return "1";}
  if ($maxid==-1) {return "1";}
  return $maxid+1;
}

/* Function insert_session()
   Takes data from global $session array and creates new rows in
   the tables Sessions, SessionHasFeature, SessionHasService,
   SessionHasPubChar, SessionHasVendorFeature, and SessionHasVendorSpace
   pocketprogtext and pregguiddesc zeroed out
   */
function insert_session() {
  global $session, $link, $query, $message, $message_error;

  // Fix secondtitle to subtitle
  $session["subtitle"]=$session["secondtitle"];

  // Fix submitable characters
  $checklist=array("title","subtitle","secondtitle","description_good_web","description_good_book");
  foreach ($checklist as $i) {
    $workstring=$session[$i];
    $desctext0=iconv('UTF-8', 'ISO-8859-1//TRANSLIT',$workstring);
    $desctext1=mb_convert_encoding($workstring, 'ISO-8859-1', 'UTF-8');
    $donestring=$desctext0;
    if (empty($donestring)) {$donestring=$desctext1;}
    if (empty($donestring)) {$donestring=$workstring;}
    $session[$i]=$donestring;
  }

  $query="INSERT into Sessions set ";
  $query.="sessionid=".$session['sessionid'].',';
  $query.="conid=".$_SESSION['conid'].", ";
  $query.="trackid=".$session["track"].',';
  $temp=$session["type"];
  $query.="typeid=".(($temp==0)?"null":$temp).", ";
  $temp=$session["divisionid"]; // Unknown=6
  $query.="divisionid=".(($temp==0)?6:$temp).", ";
  $query.="pubstatusid=".$session["pubstatusid"].',';
  $query.="languagestatusid=".$session["languagestatusid"].',';
  $query.="title=\"".mysql_real_escape_string($session["title"],$link).'",';
  $query.="secondtitle=\"".mysql_real_escape_string($session["secondtitle"],$link).'",';
  $query.="pocketprogtext='',";
  $query.="progguiddesc='',";
  $query.="persppartinfo=\"".mysql_real_escape_string($session["persppartinfo"],$link).'",';
  if ($_SESSION['condurationminutes']=="TRUE") {
    $query.="duration=\"".conv_min2hrsmin($session["duration"],$link)."\", ";
  } else {
    $query.="duration=\"".mysql_real_escape_string($session["duration"],$link)."\", ";
  }
  $query.="estatten=".($session["atten"]!=""?$session["atten"]:"null").',';
  $query.="kidscatid=".$session["kids"].',';
  $query.="signupreq=";
  if ($session["signup"]) {$query.="1,";} else {$query.="0,";}
  $temp=$session["roomset"];
  $query.="roomsetid=".(($temp==0)?"null":$temp).", ";
  $query.="notesforpart=\"".mysql_real_escape_string($session["notesforpart"],$link).'",';
  $query.="servicenotes=\"".mysql_real_escape_string($session["servnotes"],$link).'",';
  $query.="statusid=".$session["status"].',';
  $query.="notesforprog=\"".mysql_real_escape_string($session["notesforprog"],$link).'",';
  $query.="suggestor=\"".$_SESSION['badgeid'].'",';
  $query.="warnings=0,invitedguest="; // warnings db field not editable by form
  if ($session["invguest"]) {$query.="1";} else {$query.="0";}
  $result = mysql_query($query,$link);
  if (!$result) {
    $message_error.=mysql_error($link);
    $message_error.=" query=$query";
    return $message_error;
  }
  $id = mysql_insert_id($link);

  /* There should be a more dignified way of doing this, but for the
     meantime, while we are in transition...

     + descriptiontype (i) is title, subtitle and description, and
     should probably be harvested from the descrioptiontype so if more
     are added, at any point in time, they can be used.

     + biostateid (j) is currently locked to 3, since we aren't
     actually editing these yet, and here should be locked to 1,
     otherwise, since this is the raw creation.

     + biodestid (k) is web and book destinations, and should be
     actually harvested from BioDests, so if more are added, at any
     point in time, they can be used.

     + descriptionlang (l) is currently fixed to en-us, if we ever
     actually go multi-lingual this will need to be fixed. */

  // Create the Descriptions array entries.

  $desc_array[1][3][1]['en-us']=$session["title"];
  $desc_array[1][3][2]['en-us']=$session["title"];
  $desc_array[2][3][1]['en-us']=$session["subtitle"];
  $desc_array[2][3][2]['en-us']=$session["subtitle"];
  $desc_array[3][3][1]['en-us']=$session["description_good_web"];
  $desc_array[3][3][2]['en-us']=$session["description_good_book"];

  // Run the loops.
  $j=3;
  $l="en-us";
  for ($i=1; $i<=3; $i++) {
    for ($k=1; $k<=2; $k++) {

      // Check to see if it exists.
      if (isset($desc_array[$i][$j][$k][$l]) and ($desc_array[$i][$j][$k][$l] != "")) {

	// Limit check should go here.

	// Create and submit the arrays.
	$element_array=array('sessionid','conid','descriptiontypeid','biostateid',
			     'biodestid','descriptionlang','descriptiontext');
	$value_array=array($id,$_SESSION['conid'],$i,$j,$k,$l,
			   htmlspecialchars_decode($desc_array[$i][$j][$k][$l]));
	$message.=submit_table_element($link,$title,"Descriptions",$element_array,$value_array);
      }
    }
  }

  /* Set up the various feature tables, so we can loop across all of them. */
  $tn_array["featdest"]="SessionHasFeature";
  $te_array["featdest"]="featureid";
  $tn_array["servdest"]="SessionHasService";
  $te_array["servdest"]="serviceid";
  $tn_array["pubchardest"]="SessionHasPubChar";
  $te_array["pubchardest"]="pubcharid";
  $tn_array["vendfeatdest"]="SessionHasVendorFeature";
  $te_array["vendfeatdest"]="vendorfeatureid";
  $tn_array["spacedest"]="SessionHasVendorSpace";
  $te_array["spacedest"]="vendorspaceid";

  // Loop across the keys of the array created above.
  foreach (array_keys($tn_array) as $j) {

    // If any entries exist, create them.
    if ($session[$j]!="") {
      for ($i=0 ; $session[$j][$i]!="" ; $i++ ) {
	$element_array=array('sessionid','conid',$te_array[$j]);
	$value_array=array($id,$_SESSION['conid'],$session[$j][$i]);
	$message.=submit_table_element($link,$title,$tn_array[$j],$element_array,$value_array);
      }
    }
  }

  /* Set up the Vendor Adjustment Value, in case it every gets used.*/
  if (($session["vendoradjustvalue"]!="") or ($session["vendoradjustnote"]!="")) {
    $query="INSERT into SessionHasVendorAdjust set sessionid=".$id." ";
    if ($session["vendoradjustvalue"]!="") {
      $query.=", vendoradjustvalue=";
      $query.=$session["vendoradjustvalue"]." ";
    }
    if ($session["vendoradjustnote"]!="") {
      $query.=", vendoradjustnote='";
      $query.=$session["vendoradjustnote"]."' ";
    }
    $query.=", conid=".$_SESSION['conid'];
    $result = mysql_query($query,$link);
    if (!$result) {
      $message_error.=mysql_error($link);
      $message_error.=" query=$query";
      return $message_error;
    }
  }
  return $id;
}

/* Function retrieve_session_from_db()
   Reads Sessions, SessionHasFeature, and SessionHasService
   SessionHasVendorFeature, and SessionHasVeondorSpace tables
   from db to populate global array $session. */
function retrieve_session_from_db($sessionid,$conid) {
  global $session, $link, $message, $message_error;
  // $conid is now passed in.
  //$conid=$_SESSION['conid']; // make it a variable so it can be substituted

/* For the title and descriptions (these should become not hard-coded):
   descriptiontypeid: 1=title 2=subtitle 3=description
   biostateid: 1=raw 2=edited 3=good
   biodestid: 1=web 2=book
   descriptionlang: Only using "en-us" for now. */

  $query= <<<EOD
SELECT
    sessionid, conid, trackid, typeid, divisionid, pubstatusid,
    languagestatusid, title, secondtitle, pocketprogtext, progguiddesc,
    description_good_web, description_good_book, title_good_web,
    persppartinfo, duration,estatten, kidscatid, signupreq, roomsetid,
    notesforpart, servicenotes, statusid, notesforprog, suggestor,
    warnings, invitedguest, ts, subtitle_good_web
  FROM
      Sessions
    LEFT JOIN (SELECT
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
	descriptiontext as description_good_web
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
    LEFT JOIN (SELECT
        sessionid,
	descriptiontext as description_good_book
      FROM
          Descriptions
	JOIN DescriptionTypes USING (descriptiontypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
        conid=$conid AND
	descriptiontypename='description' AND
	biostatename='good' AND
	biodestname='book' AND
	descriptionlang='en-us') DGB USING (sessionid)
  WHERE
    conid=$conid AND
    sessionid=$sessionid
EOD;

  $result=mysql_query($query,$link);
  if (!$result) {
    $message_error.=$query."<BR>\n".mysql_error($link);
    return (-3);
  }
  $rows=mysql_num_rows($result);
  if ($rows!=1) {
    $message_error.=$rows;
    return (-2);
  }
  $sessionarray=mysql_fetch_array($result, MYSQL_ASSOC);
  $session["sessionid"]=$sessionarray["sessionid"];
  $session["conid"]=$sessionarray["conid"];
  $session["track"]=$sessionarray["trackid"];
  $session["type"]=$sessionarray["typeid"];
  $session["divisionid"]=$sessionarray["divisionid"];
  $session["pubstatusid"]=$sessionarray["pubstatusid"];
  $session["languagestatusid"]=$sessionarray["languagestatusid"];
  $session["title"]=$sessionarray["title"];
  $session["title_good_web"]=$sessionarray["title_good_web"];
  $session["secondtitle"]=$sessionarray["secondtitle"];
  $session["subtitle_good_web"]=$sessionarray["subtitle_good_web"];
  $session["pocketprogtext"]=$sessionarray["pocketprogtext"];
  $session["progguiddesc"]=$sessionarray["progguiddesc"];
  if (($sessionarray["description_good_web"]=="") and ($sessionarray["progguiddesc"]!="")) {
    $session["description_good_web"]=$sessionarray["progguiddesc"];
  } else {
    $session["description_good_web"]=$sessionarray["description_good_web"];
  }
  if (($sessionarray["description_good_book"]=="") and ($sessionarray["pocketprogtext"]!="")) {
    $session["description_good_book"]=$sessionarray["pocketprogtext"];
  } else {
    $session["description_good_book"]=$sessionarray["description_good_book"];
  }

  $session["persppartinfo"]=$sessionarray["persppartinfo"];
  $timearray=parse_mysql_time_hours($sessionarray["duration"]);
  if ($_SESSION['condurationminutes']=="TRUE") {
    $session["duration"]=" ".strval(60*$timearray["hours"]+$timearray["minutes"]);
  } else {
    $session["duration"]=" ".$timearray["hours"].":".sprintf("%02d",$timearray["minutes"]);
  }
  $session["atten"]=$sessionarray["estatten"];
  $session["kids"]=$sessionarray["kidscatid"];
  $session["signup"]=$sessionarray["signupreq"];
  $session["roomset"]=$sessionarray["roomsetid"];
  $session["notesforpart"]=$sessionarray["notesforpart"];
  $session["servnotes"]=$sessionarray["servicenotes"];
  $session["status"]=$sessionarray["statusid"];
  $session["notesforprog"]=$sessionarray["notesforprog"];
  $session["suggestor"]=$sessionarray["suggestor"];
  $session["invguest"]=$sessionarray["invitedguest"];

  /* Set up the various feature tables, so we can loop across all of them. */
  $tn_array["featdest"]="SessionHasFeature";
  $te_array["featdest"]="featureid";
  $tn_array["servdest"]="SessionHasService";
  $te_array["servdest"]="serviceid";
  $tn_array["pubchardest"]="SessionHasPubChar";
  $te_array["pubchardest"]="pubcharid";
  $tn_array["vendfeatdest"]="SessionHasVendorFeature";
  $te_array["vendfeatdest"]="vendorfeatureid";
  $tn_array["spacedest"]="SessionHasVendorSpace";
  $te_array["spacedest"]="vendorspaceid";

  // Loop across the keys of the array created above.
  foreach (array_keys($tn_array) as $j) {
    $result=mysql_query("SELECT ".$te_array[$j]." FROM ".$tn_array[$j]." where sessionid=".$sessionid,$link);
    if (!$result) {
      $message_error.=mysql_error($link);
      return (-3);
    }
    unset($session[$j]);
    while ($row=mysql_fetch_array($result, MYSQL_NUM)) {
      $session[$j][]=$row[0];
    }
  }
  return (37);
}

/* Function isLoggedIn()
   Reads the session variables and checks password in db to see if user is
   logged in.  Returns true if logged in or false if not.  Assumes db already
   connected on $link.
   The script will check login status.  If user is logged in
   it will pass control to script (???) to implement edit my contact info.
   If user not logged in, it will pass control to script (???) to
   log user in.
   check login script, included in db_connect.php. */
function isLoggedIn() {
  global $link, $message, $message_error;

  if (!isset($_SESSION['badgeid']) || !isset($_SESSION['password'])) {
    return false;
  }

  // remember, $_SESSION['password'] will be encrypted.
  if(!get_magic_quotes_gpc()) { //get global configuration setting
    $_SESSION['badgeid'] = addslashes($_SESSION['badgeid']);
  }

  // addslashes to session username before using in a query.
  $result=mysql_query("SELECT password FROM Participants where badgeid='".$_SESSION['badgeid']."'",$link);
  if (!$result) {
    $message_error.=mysql_error($link);
    unset($_SESSION['badgeid']);
    unset($_SESSION['password']);

    // kill incorrect session variables.
    return (-3);
  }

  if (mysql_num_rows($result)!=1) {
    unset($_SESSION['badgeid']);
    unset($_SESSION['password']);

    // kill incorrect session variables.
    $message_error.="Incorrect number of rows returned when fetching password from db.";
    return (-1);
  }

  $row=mysql_fetch_array($result, MYSQL_NUM);
  $db_pass = $row[0];

  // now we have encrypted pass from DB in
  //$db_pass['password'], stripslashes() just incase:
  $db_pass = stripslashes($db_pass);
  $_SESSION['password'] = stripslashes($_SESSION['password']);

  //echo $db_pass."<BR>";
  //echo $_SESSION['password']."<BR>";

  //compare:
  if($_SESSION['password'] != $db_pass) {
    // kill incorrect session variables.
    unset($_SESSION['badgeid']);
    unset($_SESSION['password']);
    $message_error.="Incorrect userid or password.";
    return (false);
  } else {
    // valid password for username
    // $i=set_permission_set($_SESSION['badgeid']);

    // // should now be part of session variables
    // if ($i!=0) {
    //  // error_log("Zambia: permission_set error $i\n");
    //}
    return(true); // they have correct info
  }           // in session variables.
}


/* Function retrieve_participant_from_db()
   Reads Particpants tables
   from db to populate global array $participant. */
function retrieve_participant_from_db($badgeid) {
  global $participant, $link, $message, $message_error;

  $result=mysql_query("SELECT pubsname, password FROM Participants where badgeid='$badgeid'",$link);
  if (!$result) {
    $message_error.=mysql_error($link);
    return (-3);
  }
  $rows=mysql_num_rows($result);
  if ($rows!=1) {
    $message_error.="Participant rows retrieved: $rows ";
    return (-2);
  }
  $participant=mysql_fetch_array($result, MYSQL_ASSOC);
  return (0);
}

/* Function getCongoData()
   Reads CongoDump table from db to return congoinfo array. */
function getCongoData($badgeid) {
  global $message, $message_error, $link;

    $query= <<<EOD
SELECT
        badgeid,
	firstname,
	lastname,
	badgename,
	phone,
	postaddress1,
	postaddress2,
	postcity,
	poststate,
	postzip,
        postcountry,
        pubsname,
        bestway,
        altcontact,
        prognotes,
        P.email,
        P.regtype
    FROM
        CongoDump
      JOIN Participants P USING (badgeid)
    WHERE
        badgeid="$badgeid"
EOD;
    $result=mysql_query($query,$link);
    if (!$result) {
        $message_error.=mysql_error($link)."\n<BR>Database Error.<BR>No further execution possible.";
        return(-1);
        };
    $rows=mysql_num_rows($result);
    if ($rows!=1) {
        $message_error.=$rows." rows returned for badgeid when 1 expected.<BR>Database Error.<BR>No further execution possible.";
        return(-1);
        };
    if (retrieve_participant_from_db($badgeid)!=0) {
        $message_error.="<BR>In Congo but not in Participants, no further execution possible.";
        return(-1);
        };
    $participant["password"]="";
    $congoinfo=mysql_fetch_array($result, MYSQL_ASSOC);
    return($congoinfo);
    }

/* Function retrieve_participantAvailability_from_db()
   Reads ParticipantAvailability and ParticipantAvailabilityTimes tables
   from db to populate global array $partAvail.
   Returns 0: success; -1: badgeid not found; -2: badgeid matches >1 row;
          -3: other error ($message_error populated) */
function retrieve_participantAvailability_from_db($badgeid) {
  global $partAvail, $link, $message, $message_error;
  $conid=$_SESSION['conid'];

  // Participant Availbiity table.
  $query= <<<EOD
SELECT
    badgeid,
    maxprog,
    preventconflict,
    otherconstraints,
    numkidsfasttrack
  FROM
      ParticipantAvailability
  WHERE
    conid=$conid AND
    badgeid=$badgeid
EOD;
  $result=mysql_query($query,$link);
  if (!$result) {
    $message_error.=$query."<BR>\n".mysql_error($link);
    return (-3);
  }
  $rows=mysql_num_rows($result);
  if ($rows==0) {
    return (-1);
  }
  if ($rows!=1) {
    $message_error.=$query."<BR>\n returned $rows rows.";
    return (-2);
  }
  $partAvailarray=mysql_fetch_array($result, MYSQL_NUM);
  $partAvail["badgeid"]=$partAvailarray[0];
  $partAvail["maxprog"]=$partAvailarray[1];
  $partAvail["preventconflict"]=$partAvailarray[2];
  $partAvail["otherconstraints"]=$partAvailarray[3];
  $partAvail["numkidsfasttrack"]=$partAvailarray[4];

  // Participant Availbility days.
  if ($_SESSION['connumdays']>1) {
    $query="SELECT badgeid, day, maxprog FROM ParticipantAvailabilityDays WHERE conid=\"$conid\" AND badgeid=\"$badgeid\"";
    $result=mysql_query($query,$link);
    if (!$result) {
      $message_error.=$query."<BR>\n".mysql_error($link);
      return (-3);
    }
    for ($i=1; $i<=$_SESSION['connumdays']; $i++) {
      unset($partAvail["maxprogday$i"]);
    }
    if (mysql_num_rows($result)>0) {
      while ($row=mysql_fetch_array($result, MYSQL_NUM)) {
	$i=$row[1];
	$partAvail["maxprogday$i"]=$row[2];
      }
    }
  }

  // Participant availibility times.
  $query="SELECT badgeid, availabilitynum, starttime, endtime FROM ParticipantAvailabilityTimes ";
  $query.="where conid=$conid AND badgeid=\"$badgeid\" order by starttime";
  $result=mysql_query($query,$link);
  if (!$result) {
    $message_error.=$query."<BR>\n".mysql_error($link);
    return (-3);
  }
  for ($i=1; $i<=$_SESSION['conavailabilityrows']; $i++) {
    unset($partAvail["starttimestamp_$i"]);
    unset($partAvail["endtimestamp_$i"]);
  }
  $i=1;
  while ($row=mysql_fetch_array($result, MYSQL_NUM)) {
    $partAvail["starttimestamp_$i"]=$row[2];
    $partAvail["endtimestamp_$i"]=$row[3];
    $i++;
  }
  return (0);
}

/* Function set_permission_set($badgeid)
   Performs complicated join to get the set of permission atoms available to the user
   Stores them in global variable $permission_set */
function set_permission_set($badgeid) {
  global $link, $message, $message_error;

  // First do simple permissions
  $_SESSION['permission_set']="";
  $conid=$_SESSION['conid'];
  $query= <<<EOD
SELECT
    DISTINCT permatomtag
  FROM
      Phase PH,
      Permissions P
    JOIN PermissionAtoms USING (permatomid)
    JOIN UserHasPermissionRole UHPR USING (permroleid)
  WHERE
    ((UHPR.badgeid='$badgeid' AND UHPR.conid=$conid) OR P.badgeid='$badgeid' ) AND
    (P.phasetypeid is null OR (P.phasetypeid = PH.phasetypeid AND PH.phasestate = TRUE and PH.conid=$conid))
EOD;

  // Assign result
  $result=mysql_query($query,$link);

  // error_log("set_permission_set query:  ".$query);

  // If result fails error out
  if (!$result) {
    $message_error.=$query." \n ".mysql_error($link)." \n <BR>Database Error.<BR>No further execution possible.";
    error_log("Zambia: ".$message_error);
    return(-1);
  };

  // If no rows, person has no permissions.
  $rows=mysql_num_rows($result);
  if ($rows==0) {
    $message_error.=$query." \n <BR> No rows returned from query.";
    error_log("Zambia: ".$message_error);
    return(0);
  };

  // Set the permissions
  for ($i=0; $i<$rows; $i++) {
    $onerow=mysql_fetch_array($result, MYSQL_BOTH);
    $_SESSION['permission_set'][]=$onerow[0];
  };

  // Second, do <<specific>> permissions
  $_SESSION['permission_set_specific']="";
  $query= <<<EOD
SELECT
    DISTINCT permatomtag,
    elementid
  FROM
      PermissionAtoms PA,
      Phase PH,
      PermissionRoles PR,
      UserHasPermissionRole UHPR,
      Permissions P
  WHERE
    ((UHPR.badgeid='$badgeid' and UHPR.permroleid = P.permroleid and UHPR.conid=$conid)
        or P.badgeid='$badgeid' ) and
    (P.phasetypeid is null or (P.phasetypeid = PH.phasetypeid and PH.phasestate = TRUE and PH.conid=$conid)) and
    P.permatomid = PA.permatomid and
    PA.elementid is not null
EOD;

  // Assign result
  $result=mysql_query($query,$link);

  // If result fails error out
  if (!$result) {
    $message_error.=$query." \n ".mysql_error($link)." \n <BR>Database Error.<BR>No further execution possible.";
    error_log("Zambia: ".$message_error);
    return(-1);
  };

  // If there are zero rows, possibly note it in the error log, but don't error out, often the case.
  $rows=mysql_num_rows($result);
  if ($rows==0) {
    //error_log("Zambia: ".$query." \n <BR> No rows returned from query.");
    return(0);
  };

  // If there are actually rows, set them
  for ($i=0; $i<$rows; $i++) {
    $_SESSION['permission_set_specific'][]=mysql_fetch_array($result, MYSQL_ASSOC);
  };

  // Successful return
  return(0);
}

/* Function db_error($title,$query,$staff)
   Populates a bunch of messages to help diagnose a db error. */
function db_error($title,$query,$staff) {
  global $link, $message, $message_error;
  $message_error.="Database error.<BR>\n";
  $message_error.=mysql_error($link)."<BR>\n";
  $message_error.=$query."<BR>\n";
  RenderError($title,$message_error);
}

/* Function get_idlist_from_db($table_name,$id_col_name,$desc_col_name,$desc_col_match);
   Returns a string with a list of id's from a configuration table */
function get_idlist_from_db($table_name,$id_col_name,$desc_col_name,$desc_col_match) {
  global $link, $message, $message_error;
  // error_log("zambia - get_idlist_from_db: desc_col_match: $desc_col_match");
  $query = "SELECT GROUP_CONCAT($id_col_name) from $table_name where ";
  $query.= "$desc_col_name in ($desc_col_match)";
  // error_log("zambia - get_idlist_from_db: query: $query");
  $result=mysql_query($query,$link);
  return(mysql_result($result,0));
}

/* Function unlock_participant($badgeid);
   Removes all locks from participant table for participant in parameter
   and all locks held by the user known from the session
   call with $badgeid='' to unlock based on user only. */
function unlock_participant($badgeid) {
  global $query, $link, $message, $message_error;

  if (!empty($_SESSION['badgeid'])) {
    $query="UPDATE Bios SET biolockedby=NULL WHERE ";
    if (isset($_SESSION['badgeid'])) {
      $query.="biolockedby='".$_SESSION['badgeid']."'";
      if ($badgeid!='') {
	$query.=" and badgeid='$badgeid'";
      }
    } else {
      if ($badgeid!='') {
	$query.="badgeid='$badgeid'";
      } else {
	return($query.": Nothing to unlock"); //can't find anything to unlock
      }
    }
    //error_log("Zambia: unlock_participants: ".$query);
    $result=mysql_query($query,$link);
    if (!$result) {
      return ($query.": -1");
    } else {
      return ($query.": 0");
    }
  }
}

/* Function lock_participant($badgeid);
   Locks Bios for participant in parameter, if not alreadly locked. */
function lock_participant($badgeid) {
  global $query, $link, $message, $message_error;
  //error_log("Zambia: lock_participant: ".$query);
  $userbadgeid=$_SESSION['badgeid'];
  $query="UPDATE Bios SET biolockedby='$userbadgeid' WHERE biolockedby IS NULL and badgeid='$badgeid'";

  $result=mysql_query($query,$link);
  if (!$result) {
    return (-1);
  }
  if (mysql_affected_rows($link) > 0) {
    return (0);
  } else {
    return (-2);
  }
}

/* Function get_sstatus()
   Populates the global sstatus array from the database. */
function get_sstatus() {
  global $link, $sstatus, $message, $message_error;
  $query = "SELECT statusid, may_be_scheduled, validate from SessionStatuses";
  $result=mysql_query($query,$link);
  while ($arow = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $statusid=$arow['statusid'];
    $may_be_scheduled=($arow['may_be_scheduled']==1?1:0);
    $validate=($arow['validate']==1?1:0);
    $sstatus[$statusid]=array('may_be_scheduled'=>$may_be_scheduled, 'validate'=>$validate);
  }
}

function get_enum_values($table,$field) {
  global $link, $message, $message_error;
  $query=<<<EOD
SELECT
    SUBSTRING(COLUMN_TYPE,5) AS Type
  FROM
      INFORMATION_SCHEMA.COLUMNS
  WHERE
    TABLE_NAME="$table" AND
    COLUMN_NAME="$field"
EOD;

  $result=mysql_query($query,$link);
  $typearray=mysql_fetch_array($result, MYSQL_ASSOC);
  preg_match("/^\(\'(.*)\'\)$/", $typearray['Type'], $matches);
  $enum=explode("','", $matches[1]);
  return $enum;

}

?>
