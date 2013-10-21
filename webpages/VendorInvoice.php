<?php
require_once('VendorCommonCode.php');
$_SESSION['return_to_page']='VendorWelcome.php';
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

$title="Vendor Invoice";
$description="<P>Your invoice is below.  Please check it for correctness, and then click on the \"Submit Order\" button below to initiate the transaction.</P>\n";

// Drop them if they aren't a vendor, just to be sure.
if (!may_I("Vendor")) {
  $message="You are not authorized to access this page.";
  require ('login.php');
  exit();
};

$badgeid=$_SESSION['badgeid'];

$query= <<<EOD
SELECT
    statusname,
    secondtitle,
    vendorspaceprice,
    if (vendorfeaturetotal,vendorfeaturetotal,0) AS vendorfeaturetotal,
    vendorspaceprice + if (vendorfeaturetotal,vendorfeaturetotal,0) AS "total",
    vendorspacename,
    vendfeaturename,
    progguiddesc,
    pubsname
  FROM
      Sessions S
    JOIN $ReportDB.SessionStatuses USING (statusid)
    JOIN (SELECT
	      sessionid,
	      vendorspaceid,
	      vendorspacename,
	      vendorspaceprice
	    FROM
	        Sessions
	      JOIN $ReportDB.SessionHasVendorSpace USING (sessionid)
	      JOIN $ReportDB.VendorSpaces USING (vendorspaceid)) X USING (sessionid)
    LEFT JOIN (SELECT
	           sessionid,
	           SUM(vendorfeatureprice) AS 'vendorfeaturetotal',
	           GROUP_CONCAT(DISTINCT vendorfeaturename SEPARATOR ', ') as 'vendfeaturename',
	           GROUP_CONCAT(DISTINCT vendorfeatureid SEPARATOR ', ') as 'vendfeatureid'
	         FROM
	             Sessions
	           JOIN $ReportDB.SessionHasVendorFeature USING (sessionid)
	           JOIN $ReportDB.VendorFeature USING (vendorfeatureid)
                 GROUP BY
 	           sessionid) Y USING (sessionid),
    $ReportDB.Participants
  WHERE
    title='$badgeid' AND
    badgeid='$badgeid'
EOD;

if (!$result=mysql_query($query,$link)) {
  $message_error.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message_error);
  exit();
 }
$rows=mysql_num_rows($result);
if ($rows==0) {
  $message="You have not applied to be at this event, yet.";
  require ('VendorWelcome.php');
  exit();
} elseif ($rows>1) {
  /* More than one result, so fail */
  $message_error.=$query."<BR>Too many results: $rows.<BR>";
  RenderError($title,$message_error);
} else {
  /* Presume the right info */
  $session=mysql_fetch_assoc($result);
  if ($session['statusname']!="Vendor Approved") {
    $message="Your status is ".$session['statusname']." for ".CON_NAME.".";
    require ('VendorWelcome.php');
    exit();
  }

}

vendor_header($title);
    
if (strlen($message)>0) {
  echo "<P id=\"message\"><font color=green>".$message."</font></P>\n";
}
if (strlen($message_error)>0) {
  echo "<P id=\"message2\"><font color=red>".$message_error."</font></P>\n";
  exit(); // If there is a message2, then there is a fatal error.
}

/* Standard form setup for our particular payment service.  Should be,
   somehow changed/generalized for any payment service.  Not sure how
   at the moment, but open to suggestions/comments. */
?>

<FORM method="post" action="https://pay1.plugnpay.com/payment/pay.cgi">
  <INPUT type="hidden" name="publisher-email" value="weborders@nelaonline.org">
  <INPUT type="hidden" name="cc-mail" value="weborders.nela@gmail.com">
  <INPUT type="hidden" name="publisher-name" value="nela">
  <INPUT type="hidden" name="order-id" value="website_order">
  <INPUT type="hidden" name="card-allowed" value="Visa,Mastercard">
  <INPUT type="hidden" name="shipinfo" value="1">
  <INPUT type="hidden" name="easycart" value="1">
  <INPUT type="hidden" name="currency" value="usd">
  <INPUT type="hidden" name="currency_symbol" value="$">

<?php

$purchase="Vendor Space for ".$session['pubsname']." at ".CON_NAME." Space: ".$session['vendorspacename'];
if ($session['vendfeaturename']!="") {
  $purchase.=" with ".$session['vendfeaturename'].".";
}

/* Specific values for this particular transaction */
echo "<INPUT type=\"hidden\" name=\"item1\" value=\"VendorReg\">\n";
echo "<INPUT type=\"hidden\" name=\"description1\" value=\"$purchase\">\n";
echo "<INPUT type=\"hidden\" name=\"cost1\" value=\"".$session['total']."\">\n";
echo "<INPUT type=\"hidden\" name=\"quantity1\" size=\"2\" value=\"1\">\n";

/* Return link should update them from "Vendor Approved" to "Vendor
   Paid" and probably email someone to let them know.*/

echo "<INPUT type=\"hidden\" name=\"success-link\" value=\"http://www.neleatheralliance.org/c/thankyou.html\">\n";

/* Pretty formatting for the information. */

echo "<P>Welcome to ".CON_NAME.".  Please, check over the below, and\n";
echo " if it meets with your expectations, click on the Submit Order\n";
echo " button, to be taken to the payment page.</P>\n\n";

echo "<P>You selected: $purchase<br>\n";
echo " For a total of: $".$session['total']."</P>\n\n";

echo "<P>Your notes indicate:<br>\n";
echo $session['progguiddesc']."</P>\n\n";

if ($session['secondtitle']!="") {
  echo "<P>You are currently location is:<br>\n";
  echo $session['secondtitle']."</P>\n\n";
}

?>

  <P><INPUT type="submit" name="return" value="Submit Order"></P>
  <P>(Note that when you press 'Submit Order', you will be directed to a payment page at plugnpay.com.)</P>
  <P><STRONG>Once payment has been submitted, you should keep a copy of your payment confirmation email.</STRONG></P>
</FORM>

<?php correct_footer(); ?>