<?php
/* This file is a short-cut to the hard-coded brainstorm login.

   It allows a simple click-through, as opposed to the form
   click-through that used to be the hack to get the login before.

   It will take some testing, but hopefully it will work everywhere for everyone.
 */

//create array of data to be posted
$post_data["badgeid"] = "100";
$post_data["passwd"] = "submit";
$post_data["target"] = "brainstorm";
$post_data["newconid"] = "46";

// Build the string to be posted out of the array of information above
/* This is the old fashioned way 
//traverse array and prepare data for posting (key1=value1)
foreach ( $post_data as $key => $value) {
    $post_items[] = $key . '=' . $value;
}

//create the final string to be posted using implode()
$post_string = implode ('&', $post_items);

//we also need to add a question mark at the beginning of the string
$post_string = '?' . $post_string;
*/

$post_string=http_build_query($post_data);

// Build the target from the information in the _SERVER variable for this page.
$targethost=$_SERVER['HTTP_HOST'];
$targetpath=strstr($_SERVER['REQUEST_URI'],"BrainstormRedirect",true);
if ($_SERVER['SERVER_PORT'] == 80) {
  $target="http";
} else {
  $target="https";
}
$target.="://$targethost".$targetpath."doLogin.php";

// Command Line test
//echo `curl -d "$post_string" $target`

// In PHP curl execution

// Init curl connection
$curl = curl_init();

// Set the target
curl_setopt($curl, CURLOPT_URL, $target);

// POST param
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $post_string);

// Cookie Param
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Retrieving session ID 
$strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';    

// We pass the sessionid of the browser within the curl request
curl_setopt($curl, CURLOPT_COOKIE, $strCookie ); 

// We receive the answer as if we were the browser
$curl_response = curl_exec($curl);

echo $curl_response;

?>