<?php
// This is an example file.  Please copy to ../Local/db_name.php and edit as needed.
//
// Newly added, because, apparently, important.
date_default_timezone_set('US/Eastern');
//
// Importat for garbage collection and timing on our sessions
define("BASESESSIONDIR","/var/lib/php5");
//
// This is the database access stuff. Probably should be more secure than this.
define("DBHOSTNAME","localhost");
define("DBUSERID","zambiademo");
define("DBPASSWORD","4fandom");
define("DBDB","zambiademo");
//
// This is the access for the PyroCMS Vendor tool.  See above comment about secure.
define("VENDORHOSTNAME","localhost");
define("VENDORUSERID","zambiademo");
define("VENDORPASSWORD","4fandom");
define("VENDORDB","vendordemo");
//
// Additional Database definitions
// This might be the same as the main DB,  unique, or connected to Congo
define("CONGODB","zambiademo");
//
// This might get set dynamically at some point.  Localisms (everywhere).
define("CON_KEY","44");
//
// Define the Language List (this should probably go somewhere else)
 define("LANGUAGE_LIST","('en-us','en-uk','fr-ca')");
//
// Striping out the non-English characters to something in the English char set.
define("stripfancy_from","ÀÁÂÃÄÅÆÇÈÉÊË®");
define("stripfancy_to","AAAAAAECEEEE ");
?>
