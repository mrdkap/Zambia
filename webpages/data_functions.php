<?php
// Function conv_min2hrsmin()
// Input is unchecked form input in minutes
// Output is string in MySql time format
function conv_min2hrsmin($mininput) {
    $min=filter_var($mininput,FILTER_SANITIZE_NUMBER_INT);
    if (($min<1) or ($min>3000)) return "00:00:00";
    $hrs = floor($min/60);
    $minr= $min % 60;
    return (sprintf("%02d:%02d:00",$hrs,$minr));
    }
//
// Function get_nameemail_from_post($name, $email)
// Reads the data posted by the browser form and populates
// the variables from the arguments.  Also stores them in
// SESSION variables.
//
function get_nameemail_from_post(&$name, &$email) {
    $name=stripslashes($_POST['name']);
    $email=stripslashes($_POST['email']);
    $_SESSION['name']=$name;
    $_SESSION['email']=$email;
    return;
    }
//
// Function get_participant_availability_from_post()
// Reads the data posted by the browser form and populates
// the $partavail global variable with it.
//
function get_participant_availability_from_post() {
    global $partAvail;
    // for numeric fields in ParticipantAvailability--convert to 0 if blank
    $partAvail["maxprog"]=($_POST["maxprog"]=="")?0:$_POST["maxprog"];
    for ($i=1; $i<=$_SESSION['connumdays']; $i++) {
        $partAvail["maxprogday$i"]=($_POST["maxprogday$i"]!="")?$_POST["maxprogday$i"]:0;
        }
    for ($i=1; $i<=$_SESSION['conavailabilityrows']; $i++) {
        $x1=$partAvail["availstartday_$i"]=$_POST["availstartday_$i"];
        $x2=$partAvail["availstarttime_$i"]=$_POST["availstarttime_$i"];
        $x3=$partAvail["availendday_$i"]=$_POST["availendday_$i"];
        $x4=$partAvail["availendtime_$i"]=$_POST["availendtime_$i"];
        //error_log("Zambia, get: $i, $x1, $x2, $x3, $x4");
        }
    $partAvail["preventconflict"]=stripslashes($_POST["preventconflict"]);
    $partAvail["numkidsfasttrack"]=($_POST["numkidsfasttrack"]=="")?0:$_POST["numkidsfasttrack"]+0;
    $partAvail["otherconstraints"]=stripslashes($_POST["otherconstraints"]);
    }

// Function get_session_from_post()
// Reads the data posted by the browser form and populates
// the $session global variable with it.
//
function get_session_from_post() {
    global $session;
    $session["sessionid"]=$_POST["sessionid"];
    $session["track"]=$_POST["track"];
    $session["type"]=$_POST["type"];
    $session["divisionid"]=$_POST["divisionid"];
    $session["pubstatusid"]=$_POST["pubstatusid"];
    $session["languagestatusid"]=$_POST["languagestatusid"];
    if (isset($_POST["title"])) {
            $session["title"]=stripslashes($_POST["title"]);
            }
        else {
            $session["title"]="";
            }
    $session["secondtitle"]=stripslashes($_POST["secondtitle"]);
    $session["description_raw_web"]=stripslashes($_POST["description_raw_web"]);
    $session["description_raw_book"]=stripslashes($_POST["description_raw_book"]);
    $session["description_edited_web"]=stripslashes($_POST["description_edited_web"]);
    $session["description_edited_book"]=stripslashes($_POST["description_edited_book"]);
    $session["description_good_web"]=stripslashes($_POST["description_good_web"]);
    $session["description_good_book"]=stripslashes($_POST["description_good_book"]);
    $session["pocketprogtext"]=stripslashes($_POST["pocketprogtext"]);
    $session["progguiddesc"]=stripslashes($_POST["progguiddesc"]);
    $session["persppartinfo"]=stripslashes($_POST["persppartinfo"]);
    //error_log("Zambia->get_session_from_post->\$_POST[\"pubchardest\"]: ".print_r($_POST["pubchardest"],TRUE)); // for debugging only
    $session["pubchardest"]=$_POST["pubchardest"];
    //error_log("Zambia->get_session_from_post->\$session[\"pubchardest\"]: ".print_r($session["pubchardest"],TRUE)); // for debugging only
    $session["featdest"]=$_POST["featdest"];
    //error_log("Zambia->get_session_from_post->\$session[\"featdest\"]: ".print_r($session["featdest"],TRUE)); // for debugging only
    $session["servdest"]=$_POST["servdest"];
    $session["vendfeatdest"]=$_POST["vendfeatdest"];
    $session["spacedest"]=$_POST["spacedest"];
    $session["duration"]=stripslashes($_POST["duration"]);
    $session["atten"]=$_POST["atten"];
    $session["kids"]=$_POST["kids"];
    $session["invguest"]=isset($_POST["invguest"]);
    $session["signup"]=isset($_POST["signup"]);
    $session["roomset"]=$_POST["roomset"];
    $session["notesforpart"]=stripslashes($_POST["notesforpart"]);
    $session["servnotes"]=stripslashes($_POST["servnotes"]);
    $session["status"]=$_POST["status"];
    $session["notesforprog"]=stripslashes($_POST["notesforprog"]);
    }

// Function set_session_defaults() 
// Populates the $session global variable with default data
// for use when creating a new session.  Note that if a field is
// an index into a table of options, the default value of "0" signifies
// that "Select" will be displayed in the gui.
//
function set_session_defaults() {
    global $session;
    //$session["sessionid"] set elsewhere
    $session["track"]=0; // prompt with "SELECT"
    $session["type"]=1; // default to "Panel"
    $session["divisionid"]=2; // default to "Programming"
    $session["pubstatusid"]=2; // default to "Public"
    $session["languagestatusid"]=1; // default to "English"
    $session["title"]="";
    $session["secondtitle"]="";
    $session["description_raw_web"]="";
    $session["description_raw_book"]="";
    $session["description_edited_web"]="";
    $session["description_edited_book"]="";
    $session["description_good_web"]="";
    $session["description_good_book"]="";
    $session["pocketprogtext"]="";
    $session["progguiddesc"]="";
    $session["persppartinfo"]="";
    $session["featdest"]="";
    $session["servdest"]="";
    $session["pubchardest"]="";
    $session["duration"]=$_SESSION['condefaultduration'];
    $session["atten"]="";
    $session["kids"]=1; // "Kids Not Allowed"
    $session["signup"]=false; // leave checkbox blank initially
    $session["roomset"]="5"; // default to "Theater"
    $session["notesforpart"]="";
    $session["servnotes"]="";
    $session["status"]=1; // Brainstorm=1 Edit Me=6 Vetted=2 (should probably switch on Phases)
    $session["notesforprog"]="";
    $session["invguest"]=false; // leave checkbox blank initially
    }
// Function parse_mysqli_time($time)
// Takes the string $time in "hhh:mm:ss" and return array of "day" and "hour" and "minute"
//
function parse_mysqli_time($time) {
    $h=0+substr($time,0,strlen($time)-6);
    $result['hour']=fmod($h,24);
    $result['day']=intval($h/24);
    $result['minute']=substr($time,strlen($time)-5,2);
    return($result);
    }
//
// Function parse_mysqli_time_hours($time)
// Takes the string $time in "hhh:mm:ss" and return array of "hours", "minutes", and "seconds"
//
function parse_mysqli_time_hours($time) {
    sscanf($time,"%d:%d:%d",$hours,$minutes,$seconds);
    $result['hours']=$hours;
    $result['minutes']=$minutes;
    $result['seconds']=$seconds;
    return($result);
    }
//
// Function time_description($time)
// Takes the string $time and return string describing time
// $time is mysql output measured from start of con
// result is like "Fri 1:00 PM"
//
function time_description($time) {
    global $daymap;
    $atime=parse_mysqli_time($time);
    $result="";
    $result.=$daymap['short'][$atime["day"]+1]." ";
    $hour=fmod($atime["hour"],12);
    $result.=(($hour==0)?12:$hour).":".$atime["minute"]." ";
    $result.=($atime["hour"]>=12)?"PM":"AM";
    return($result);
    }

// Function fix_slashes($arg)
// Takes the string $arg and removes multiple slashes, 
// slash-quote and slash-double quote.
function fix_slashes($arg) {    
    while (($pos=strpos($arg,"\\\\"))!==false) {
        if ($pos==0) {
                $arg=substr($arg,1);
                }
            else {
                $arg=substr($arg,0,$pos).substr($arg,$pos+1);
                }
        }
    while (($pos=strpos($arg,"\\'"))!==false) {
        if ($pos==0) {
                $arg=substr($arg,1);
                }
            else {
                $arg=substr($arg,0,$pos).substr($arg,$pos+1);
                }
        }
    while (($pos=strpos($arg,"\\\""))!==false) {
        if ($pos==0) {
                $arg=substr($arg,1);
                }
            else {
                $arg=substr($arg,0,$pos).substr($arg,$pos+1);
                }
        }
    return $arg;
    }

// Function may_I($permatomtag)
// $permatomtag is a string which designates a permission atom
// returns TRUE if user has this permission in the current phase(s)
//
function may_I($permatomtag) {
  if ((isset($_SESSION['permission_set'])) and ($_SESSION['permission_set']==true)) {
    $test=in_array($permatomtag,$_SESSION['permission_set']);
    }
    return ($test);
  }    
?>
