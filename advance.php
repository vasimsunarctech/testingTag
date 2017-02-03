<?php
session_start();
global $environment;
$environment = "pilot";

require_once('PayflowNVPAPI.php');


////////
////
////  First, handle any return/responses
////

//Check if we just returned inside the iframe.  If so, store payflow response and redirect parent window with javascript.
if (isset($_POST['RESULT']) || isset($_GET['RESULT']) ) {
  $_SESSION['payflowresponse'] = array_merge($_GET, $_POST);
  echo '<script type="text/javascript">window.top.location.href = "' . script_url() .  '";</script>';
  exit(0);
}

/*echo "<span style='font-family:sans-serif;font-size:160%;font-weight:bold;'>PayPal Payments Advanced - basic demo</span>
<p style='margin-left:1em;font-family:monospace;'>Hosted checkout page (Layout C) embedded in your site as an iframe</p><hr/>";*/

//Check whether we stored a server response.  If so, print it out.
if(!empty($_SESSION['payflowresponse'])) {
  $response= $_SESSION['payflowresponse'];

  unset($_SESSION['payflowresponse']);

  $success = ($response['RESULT'] == 0);
	include_once 'header.php';
  if($success) 
  echo "<span style='font-family:sans-serif;font-weight:bold;'>Transaction approved! Thank you for your order.</span>". $response['TRANSACTIONID'];

  else echo "<span style='font-family:sans-serif;'>Transaction failed! Please try again with another payment method.</span>";
	include_once 'footer.php';
	  //echo "<pre>(server response follows)\n";
     
  //echo "</pre>";
  exit(0);
}

/////////
////
//// Otherwise, begin hosted checkout pages flow
////
    
//Build the Secure Token request
$request = array(
  "PARTNER" => "PayPal",
  "VENDOR" =>"caryinternal", // "palexanderpayflowtest",
  "USER" => "caryinternal",//"palexanderpayflowtestapionly",
  "PWD" => "C@ryInt3rn@l",//"demopass123", 
  "TRXTYPE" => "S",
  "AMT" => "$_POST[amount]",
  "CURRENCY" => "USD",
  "CREATESECURETOKEN" => "Y",
  "SECURETOKENID" => uniqid('MySecTokenID-'), //Should be unique, never used before
  "RETURNURL" => script_url("http://caryinternalmedicine.com/"),
  "CANCELURL" => script_url("http://caryinternalmedicine.com/"),
  "ERRORURL" => script_url("http://caryinternalmedicine.com/"),


// In practice you'd collect billing and shipping information with your own form,
// then request a secure token and display the payment iframe.
// --> See page 7 of https://cms.paypal.com/cms_content/US/en_US/files/developer/Embedded_Checkout_Design_Guide.pdf

	
	
  "BILLTOFIRSTNAME" => "$_POST[firstName]",
  "BILLTOLASTNAME" => "$_POST[lastName]",
  "BILLTOSTREET" => "$_POST[address]",
  "BILLTOCITY" => "$_POST[city]",
  "BILLTOSTATE" => "$_POST[state]",
  "BILLTOZIP" => "$_POST[zip]",
  "BILLTOCOUNTRY" => "US",

  "SHIPTOFIRSTNAME" => "$_POST[firstName]",
  "SHIPTOLASTNAME" => "$_POST[lastName]",
  "SHIPTOSTREET" => "$_POST[address1]",
  "SHIPTOCITY" => "$_POST[city]",
  "SHIPTOSTATE" => "$_POST[state]",
  "SHIPTOZIP" => "$_POST[zip]",
  "SHIPTOCOUNTRY" => "US",
  "EMAIL"	=>	"$_POST[email]",
  "PAN"	=> "$_POST[pan]",
  "PN" => "$_POST[pn]",
  "AMOUNT" => "$_POST[amount]"
);

//Run request and get the secure token response
$response = run_payflow_call($request);

if ($response['RESULT'] != 0) {
 // pre($response, "Payflow call failed");
 header('Location: http://caryinternalmedicine.com/');
  exit(0);
} else { 
  $securetoken = $response['SECURETOKEN'];
  $securetokenid = $response['SECURETOKENID'];
}

// Include database file
include_once 'form.db.php';

if($environment == "sandbox" || $environment == "pilot") $mode='TEST'; else $mode='LIVE';
include_once 'header.php';
//echo '<div style="border: 1px dashed; margin-left:40px; width:492px; height:567px;">'; // wrap iframe in a dashed wireframe for demo purposes

echo "<div> <iframe src='https://payflowlink.paypal.com?SECURETOKEN=$securetoken&SECURETOKENID=$securetokenid&MODE=$mode' width='490' height='565' border='0' frameborder='0' scrolling='no' allowtransparency='true'>\n</iframe>";
echo "</div>";
include_once 'footer.php'

?>

