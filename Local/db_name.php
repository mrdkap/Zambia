<?php
// Newly added, because, apparently, important.
date_default_timezone_set('US/Eastern');
// This is an example file.  Please copy to db_name.php and edit as needed.
define("DBHOSTNAME","nelaonline.org");
define("DBUSERID","nelaonli_FFFZ");
define("DBPASSWORD","Zambia-FFF");
define("DBDB","nelaonli_FFF38Z");
define("BIODB","nelaonli_FFFGen"); // This might be the same as DBDB, centralized, or unique
define("REPORTDB","nelaonli_FFFGen"); // This might be the same as DBDB, centralized, or unique
define("CONGODB","nelaonli_FFFGen"); // This might be the same as DBDB, centralized, or unique
define("LIMITDB","nelaonli_FFFGen"); // This might be the same as DBDB, centralized, or unique
// define("TIMECARDDB",nelaonli_FFFGen"); //This might be the same as DBDB, centralized, or unique
define("GOH_BADGE_LIST","('70', '123', '6752', '93571')"); // This should become a field in the Participants table
#
define("CON_KEY","38");  // Key for LOCALDB and LIMITDB, to clean a lot of the below out.
define("CON_NAME","FFF #38");
define("CON_NUM_DAYS",3); // code works for 1 - 8
define("CON_START_DATIM","2012-02-10 00:00:00"); // Intended for use by report scripts
define("CON_URL","nelaonline.org/FFF-NE-38");
define("CON_LOGO","../../images/nelaLogoHeader.gif");
define("DEFAULT_DURATION","1:30"); // How long the default is for a Scheduled Element
define("DURATION_IN_MINUTES","FALSE"); // TRUE: in mmm; False: in hh:mm - affects session edit/create page only, not reports
define("GRID_SPACER",900); // space grid sections by 60 sec/min and 15 min
# Con Email table
define("ADMIN_EMAIL","percy@nelaonline.org");
define("BRAINSTORM_EMAIL","percy@nelaonline.org");
define("PROGRAM_EMAIL","nela-program@nelaonline.org");
define("VENDOR_EMAIL","danny@nelaonline.org");
define("REG_EMAIL","percy@nelaonline.org");
# Limits
define("PREF_TTL_SESNS_LMT",5); // Input data verification limit for preferred total number of sessions
define("PREF_DLY_SESNS_LMT",3); // Input data verification limit for preferred daily limit of sessions
define("AVAILABILITY_ROWS",8); // Number of rows of availability records to render
// define("MIN_WEB_BIO_LEN",10); // Minimum length (in characters) permitted for web-side participant biographies
define("MAX_WEB_BIO_LEN",3000); // Maximum length (in characters) permitted for web-side participant biographies
// define("MIN_BOOK_BIO_LEN",10); // Minimum length (in characters) permitted for program book participant biographies
define("MAX_BOOK_BIO_LEN",450); // Maximum length (in characters) permitted for program book participant biographies
define("MIN_WEB_DESC_LEN",10); // Minimum length (in characters) permitted for web-side program descriptions
define("MAX_WEB_DESC_LEN",3000); // Maximum length (in characters) permitted for web-side program descriptions
define("MIN_BOOK_DESC_LEN",10); // Minimum length (in characters) permitted for program book program descriptions
define("MAX_BOOK_DESC_LEN",450); // Maximum length (in characters) permitted for program book program descriptions
define("MIN_TITLE_LEN",5); // Minimum length (in characters) permitted for class titles
define("MAX_TITLE_LEN",50); // Maximum length (in characters) permitted for class titles
// define("MIN_NAME_LEN",5); // Minimum length (in characters) permitted for any/all participant names
// define("MAX_NAME_LEN",50); // Maximum length (in characters) permitted for any/all participant names
define("MY_AVAIL_KIDS","FALSE"); // Enables questions regarding no. of kids in Fasttrack on "My Availability"
# Language
define("BILINGUAL","FALSE"); // Triggers extra fields in Session and "My General Interests"
define("SECOND_LANG","FRENCH");
define("SECOND_TITLE_CAPTION","Titre en fran&ccedil;ais");
define("SECOND_DESCRIPTION_CAPTION","Description en fran&ccedil;ais");
define("SECOND_BIOGRAPHY_CAPTION","Biographie en fran&ccedil;ais");
// define("LANGUAGE_LIST","('en-us','en-uk','fr-ca')");
define("LANGUAGE_LIST","('en-us')");
define("BASESESSIONDIR","/var/lib/php5");
global $daymap;
for ($i=0;$i<CON_NUM_DAYS;$i++) {
  $today=strtotime(CON_START_DATIM)+(86400 * $i); // 86400 seconds in a day
  $daymap['long'][$i+1]=strftime("%A",$today);
  $daymap['short'][$i+1]=strftime("%a",$today);
 }
define("stripfancy_from","�����������ˮ");
define("stripfancy_to","AAAAAAECEEEE ");
?>
