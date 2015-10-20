<?php
require_once('StaffCommonCode.php');
global $link;

$title="Renumbering Table";

/* This takes a table as renumber_table a key as renumber_key, the
   title, and the description (the last two for passing to error
   conditions, if necessary), the key should be an auto-increment key
   for this to work, and will renumber the table on the particular
   key. */
function straight_renumber_with_key($renumber_table,$renumber_key,$title,$description) {
  global $link;

  // Get everything from the database, and put into useable arrays.
  $query = "SELECT * from $renumber_table";
  list($rows,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

  // Build the start of the table information insert, including
  // clearing the table, and make sure the headers are in the same
  // order.
  $header_rows=count($header_array);
  $clearquery="TRUNCATE TABLE $renumber_table";
  $query="INSERT INTO $renumber_table (";
  for ($j=0; $j<$header_rows; $j++) {
    if ($header_array[$j] != $renumber_key) {
      $query.=$header_array[$j].", ";
    }
  }   
  $query=rtrim($query,', ');
  $query.=") VALUES ";

  // Build the rest of the table information insert, row by row, using
  // the ordered headers.
  for ($i=1; $i<=$rows; $i++) {
    $query.="(";
    for ($j=0; $j<$header_rows; $j++) {
      if ($header_array[$j] != $renumber_key) {
	$addtoquery="'".$element_array[$i][$header_array[$j]]."',";
	if ($addtoquery=="'',") {
	  $addtoquery="NULL,";
	}
	$query.=$addtoquery;
      }
    }
    $query=rtrim($query,',');
    $query.="), ";
  }
  $query=rtrim($query,', ');

  // Clear the database.
  if (($result=mysql_query($clearquery,$link))===false) {
    $message="Error clearing data from database.<BR>";
    $message.=$clearquery;
    $message.="<BR>";
    $message.= mysql_error();
    RenderError($title,$message);
    exit ();
  }

  // Insert the digested information back into the database.
  if (($result=mysql_query($query,$link))===false) {
    $message="Error restoring data from database.<BR>";
    $message.=$query;
    $message.="<BR>";
    $message.= mysql_error();
    RenderError($title,$message);
    exit ();
  }
}

function renumber_with_key_plus_conid($renumber_table,$renumber_key,$conid_array,$title,$description) {
  global $link;

  // Get everything from the database, and put into useable arrays.
  $query = "SELECT * from $renumber_table";
  list($rows,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

  // Build the start of the table information insert, including
  // clearing the table, and make sure the headers are in the same
  // order.
  $header_rows=count($header_array);
  $clearquery="TRUNCATE TABLE $renumber_table";
  $query="INSERT INTO $renumber_table (";
  for ($j=0; $j<$header_rows; $j++) {
    if ($header_array[$j] != $renumber_key) {
      $query.=$header_array[$j].", ";
    }
  }   
  $query=rtrim($query,', ');
  $query.=") VALUES ";

// Build the rest of the table information insert, row by row, using
  // the ordered headers.
  for ($i=1; $i<=$rows; $i++) {
    $query.="(";
    for ($j=0; $j<$header_rows; $j++) {
      if ($header_array[$j] != $renumber_key) {
	$addtoquery="'".$element_array[$i][$header_array[$j]]."',";
	if ($addtoquery=="'',") {
	  $addtoquery="NULL,";
	}
	$query.=$addtoquery;
      }
    }
    $query=rtrim($query,',');
    $query.="), ";
  }
  $query=rtrim($query,', ');

  // Clear the database.
  if (($result=mysql_query($clearquery,$link))===false) {
    $message="Error clearing data from database.<BR>";
    $message.=$clearquery;
    $message.="<BR>";
    $message.= mysql_error();
    RenderError($title,$message);
    exit ();
  }

  // Insert the digested information back into the database.
  if (($result=mysql_query($query,$link))===false) {
    $message="Error restoring data from database.<BR>";
    $message.=$query;
    $message.="<BR>";
    $message.= mysql_error();
    RenderError($title,$message);
    exit ();
  }
}

// Key to not use (pflowid) in the re-buiding of the table (PersonalFlow).
$renumber_table="PersonalFlow";
$renumber_key="pflowid";

// straight_renumber_with_key($renumber_table,$renumber_key,$title,$renumber_table);

// Key to not use (featureid) in the re-building of the table (Features) with
// the conid attached.
$renumber_table="Features";
$renumber_key="featureid";
$conid_array=array("26","28","30","32","34","36","38","39","40");

//renumber_with_key_plus_conid($renumber_table,$renumber_key,$conid_array,$title,$renumber_table);



topofpagereport($title,$description,$additionalinfo,$message,$message_error);
echo "<P>$query</P>\n";
echo "<P>";
print_r ($header_array);
echo "</P>\n<P>";
print_r ($element_array);
echo "</P>\n";
correct_footer();
?>

