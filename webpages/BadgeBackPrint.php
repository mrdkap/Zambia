<?php
require_once('StaffCommonCode.php');

/* Global Variables */
global $link;
$conid=$_SESSION['conid']; // make it a variable so it can be substituted

// LOCALIZATIONS
$_SESSION['return_to_page']="BadgesPrint.php";
$title="Badge Print";
$description="<P>Badges for Printing.</P>\n";

// Extra Message for inclusion.
$importantinfo0="";
$importantinfo1="";

$verbiage=get_verbiage("Badge_Back_Print_0");
if ($verbiage != "") {
  ob_start();
  eval ('?>' . $verbiage);
  $importantinfo0.=ob_get_clean();
}

$verbiage=get_verbiage("Badge_Back_Print_1");
if ($verbiage != "") {
  ob_start();
  eval ('?>' . $verbiage);
  $importantinfo1.=ob_get_clean();
}

// Specific people
$whichparticipants='';
if ((isset($_GET['badgeids'])) and ($_GET['badgeids'] != '')) {
   $whichparticipants="badgeid in (".$_GET['badgeids'].") AND";
}

// Postscript Header
$header=<<<EOD
%!PS-Adobe-3.0

/deffont {
  findfont exch scalefont def
} bind def

/reencode_font {
  findfont reencode 2 copy definefont pop def
} bind def

% reencode the font
% <encoding-vector> <fontdict> -> <newfontdict>
/reencode { %def
  dup length 5 add dict begin
    { %forall
      1 index /FID ne
      { def }{ pop pop } ifelse
    } forall
    /Encoding exch def

    % Use the font's bounding box to determine the ascent, descent,
    % and overall height; don't forget that these values have to be
    % transformed using the font's matrix.
    % We use 'load' because sometimes BBox is executable, sometimes not.
    % Since we need 4 numbers an not an array avoid BBox from being executed
    /FontBBox load aload pop
    FontMatrix transform /Ascent exch def pop
    FontMatrix transform /Descent exch def pop
    /FontHeight Ascent Descent sub def

    % Define these in case they're not in the FontInfo (also, here
    % they're easier to get to.
    /UnderlinePosition 1 def
    /UnderlineThickness 1 def

    % Get the underline position and thickness if they're defined.
    currentdict /FontInfo known {
      FontInfo

      dup /UnderlinePosition known {
        dup /UnderlinePosition get
        0 exch FontMatrix transform exch pop
        /UnderlinePosition exch def
      } if

      dup /UnderlineThickness known {
        /UnderlineThickness get
        0 exch FontMatrix transform exch pop
        /UnderlineThickness exch def
      } if

    } if
    currentdict
  end
} bind def

/ISO-8859-1Encoding [
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/space /exclam /quotedbl /numbersign /dollar /percent /ampersand /quoteright
/parenleft /parenright /asterisk /plus /comma /minus /period /slash
/zero /one /two /three /four /five /six /seven
/eight /nine /colon /semicolon /less /equal /greater /question
/at /A /B /C /D /E /F /G
/H /I /J /K /L /M /N /O
/P /Q /R /S /T /U /V /W
/X /Y /Z /bracketleft /backslash /bracketright /asciicircum /underscore
/quoteleft /a /b /c /d /e /f /g
/h /i /j /k /l /m /n /o
/p /q /r /s /t /u /v /w
/x /y /z /braceleft /bar /braceright /asciitilde /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef /.notdef
/space /exclamdown /cent /sterling /currency /yen /brokenbar /section
/dieresis /copyright /ordfeminine /guillemotleft /logicalnot /hyphen /registered /macron
/degree /plusminus /twosuperior /threesuperior /acute /mu /paragraph /bullet
/cedilla /onesuperior /ordmasculine /guillemotright /onequarter /onehalf /threequarters /questiondown
/Agrave /Aacute /Acircumflex /Atilde /Adieresis /Aring /AE /Ccedilla
/Egrave /Eacute /Ecircumflex /Edieresis /Igrave /Iacute /Icircumflex /Idieresis
/Eth /Ntilde /Ograve /Oacute /Ocircumflex /Otilde /Odieresis /multiply
/Oslash /Ugrave /Uacute /Ucircumflex /Udieresis /Yacute /Thorn /germandbls
/agrave /aacute /acircumflex /atilde /adieresis /aring /ae /ccedilla
/egrave /eacute /ecircumflex /edieresis /igrave /iacute /icircumflex /idieresis
/eth /ntilde /ograve /oacute /ocircumflex /otilde /odieresis /divide
/oslash /ugrave /uacute /ucircumflex /udieresis /yacute /thorn /ydieresis
] def
/ISOArial ISO-8859-1Encoding /Arial reencode_font
/labelclip {
	newpath
	1.000000 1.000000 moveto
	251.000000 1.000000 lineto
	251.000000 159.000000 lineto
	1.000000 159.000000 lineto
	closepath
	clip

} def

% end prologue

% set font type and size
ISOArial 16 scalefont setfont

EOD;

$startpage=<<<EOD
%%Page: BadgeBack

%%BeginPageSetup
54.000000 77.000000 translate
%%EndPageSetup

EOD;

//Set up where things are going to be written.
$positional_array[1]['row']=0;
$positional_array[2]['row']=160;
$positional_array[3]['row']=320;
$positional_array[4]['row']=480;
$positional_array[1]['col']=0;
$positional_array[2]['col']=252;

/* This query grabs all the schedule elements to be rated, for the selected time period. */
$query=<<<EOD
SELECT
    DISTINCT badgeid,
    if ((name_good_badge IS NOT NULL),name_good_badge,pubsname) AS "Name",
    CONCAT(title,
      if((moderator in ("1","YES")),' (moderating)',''),
      if((aidedecamp in ("1","YES")),' (assisting)',''),
      if((volunteer in ("1","YES")),' (outside wristband checker)',''),
      if((introducer in ("1","YES")),' (announcer/inside room attendant)','')) AS "Title",
     CONCAT(DATE_FORMAT(ADDTIME(constartdate,starttime),'%a %l:%i %p'),
	' - ',
        CASE
          WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')
          WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')
          ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
          END,
        ' - ',
	roomname) AS "Info",
     sessionid
  FROM
      Sessions
    JOIN Schedule USING (sessionid,conid)
    JOIN Rooms USING (roomid)
    JOIN ParticipantOnSession USING (sessionid,conid)
    JOIN Participants USING (badgeid)
    JOIN UserHasPermissionRole UHPR USING (badgeid,conid)
    JOIN PermissionRoles USING (permroleid)
    JOIN ConInfo USING (conid)
    LEFT JOIN (SELECT
        badgeid,
	biotext as name_good_badge
      FROM
          Bios
	JOIN BioTypes USING (biotypeid)
        JOIN BioStates USING (biostateid)
        JOIN BioDests USING (biodestid)
      WHERE
	  biotypename in ('name') AND
	  biostatename in ('good') AND
	  biodestname in ('badge') AND
	  biolang='en-us') NGB USING (badgeid)
  WHERE
    permrolename IN ('Participant','General','Programming','Events','Lounge','Watch') AND
    $whichparticipants
    conid=$conid
  ORDER BY
    pubsname,
    starttime
EOD;

// Retrive query
list($rows,$header_array,$participant_array)=queryreport($query,$link,$title,$description,0);

/* $startpos is where to start
   $name_indent is how far to indent the name (title) in X
   $info_indent is how far to indent the info in X
   $fontsize is the size of the font
   $offset is how large in Y a newline offset is
   $i is the iteration of the count of rows from the participant array query above.
   $new_participant_array is the collection per card (per person)
   $j is each participant/pubsname array element (for each person/badge-back)
   $k is (half) the line count for each (pair of) line(s).
   $first is the Y offset for the first line of each pair.
   $second is the Y offset for the second line of each pair.
   $importantinfo0/1 are established above, from the respective files. 
*/
$startpos=140;
$name_indent=6;
$info_indent=9;
$fontsize=6;
$offset=$fontsize+2;
$i=0;
$j=0;
$new_participant_array[$j]['badgeid']="";
while ($i <= $rows) {
  $i++;
  if ($new_participant_array[$j]['badgeid'] != $participant_array[$i]['badgeid']) {
    $k=1;
    $new_participant_array[$j]['Schedule'].="\nstroke\ngrestore\n\n";
    $j++;
    $new_participant_array[$j]['badgeid']=$participant_array[$i]['badgeid'];
    $new_participant_array[$j]['Name']=$participant_array[$i]['Name'];
    $new_participant_array[$j]['Schedule']=" ) show\n";
    if ((!empty($importantinfo0)) or (!empty($importantinfo1))) {
      $first=$startpos+$offset-($k*2*$offset);
      $second=$first-$offset;
      $new_participant_array[$j]['Schedule'].=$name_indent." ".$first." moveto\n( ".$importantinfo0." ) show\n";
      $new_participant_array[$j]['Schedule'].=$name_indent." ".$second." moveto\n( ".$importantinfo1." ) show\n";
      $k++;
    }
  }
  $first=$startpos+$offset-($k*2*$offset);
  $second=$first-$offset;
  $new_participant_array[$j]['Schedule'].=$name_indent." ".$first." moveto\n( ".$participant_array[$i]['Title']." ) show\n".$info_indent." ".$second." moveto\n( ".$participant_array[$i]['Info']." ) show\n";
  $k++;
}
$new_participant_array[$j]['Schedule'].="\nstroke\ngrestore\n\n";
$new_rows=$j;

/* Printing body.  */
header('Content-type: application/postscript');

/* This builds the cards, with:
   $k being each person
   $i being the column switch
   $j being the row-switch
   $startpage is the page-header, set above
   $positional_array of the rows and columns set above
   A gsave is issued, each card is moved to and written, and then a
   grestore is done.
 */
echo $header;
$k=1;
while ($k <= $new_rows) {
  echo $startpage;
  for ($i=2; $i>=1; $i--) {
    for ($j=1; $j<=4; $j++) {
      echo "gsave\n";
      echo $positional_array[$i]['col'];
      echo " ";
      echo $positional_array[$j]['row'];
      echo "\ntranslate\nlabelclip\nnewpath\nISOArial ".$fontsize." scalefont setfont\n".$name_indent." ".$startpos." moveto\n( ";
      echo $new_participant_array[$k]['Name'];
      if (isset($new_participant_array[$k]['Schedule'])) {
	echo $new_participant_array[$k++]['Schedule'];
       } else {
	echo ") show\nstroke\ngrestore\n\n";
       }
     }
   }
  echo "showpage\n\n";
 }

?>
