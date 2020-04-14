<?php
error_reporting(0);
include 'config.php';
include 'global.php';
require_once('mail/PHPMailerAutoload.php');
// Fill these in with the information from your CoinPayments.net account.
$cp_merchant_id = $gsetting -> getMerchant($odb);
$cp_ipn_secret = $gsetting -> getIPNSecret($odb);

//Which currency you want to accept
$order_currency = 'USD';

/*function errorAndDie($error_msg) {
		$file = "debugcoinpayments.txt";
		$open = fopen($file, "a");
		fwrite ($open, $error_msg);
}*/

function errorAndDie($error_msg) { 
        $report = 'Error: '.$error_msg."\n\n"; 
        $report .= "POST Data\n\n"; 
        foreach ($_POST as $k => $v) { 
            $report .= "|$k| = |$v|\n"; 
        } 
		$file = "coinpaymentsdebug.txt";
		$open = fopen($file, "a");
		fwrite ($open, $report);$file = "coinpaymentsdebug.txt";
		$open = fopen($file, "a");
		fwrite ($open, $report);
} 

if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
	errorAndDie("IPN Mode is not HMAC\n");
}

if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
	errorAndDie("No HMAC signature sent.\n");
}

$request = file_get_contents('php://input');
if ($request === FALSE || empty($request)) {
	errorAndDie("Error reading POST data\n");
}

if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($cp_merchant_id)) {
	errorAndDie("No or incorrect Merchant ID passed\n");
}

$hmac = hash_hmac("sha512", $request, trim($cp_ipn_secret));
if ($hmac != $_SERVER['HTTP_HMAC']) {
	errorAndDie("HMAC signature does not match\n");
}

// HMAC Signature verified at this point, load some variables.

$txn_id = $_POST['txn_id']; //Needed to log
$item_name = $_POST['item_name'];  //Not really needed but let's get this
$item_number = $_POST['item_number'];  //We are going to check this and give the membership.
$custom = $_POST['custom']; //Here we will have the user and package id
$amount1 = floatval($_POST['amount1']); // The total amount of the payment in your original currency/coin.
$amount2 = floatval($_POST['amount2']); // The total amount of the payment in the buyer's selected coin.
$currency1 = $_POST['currency1']; // The original currency/coin submitted in your button.
$currency2 = $_POST['currency2']; // The coin the buyer chose to pay with.
$status = intval($_POST['status']); // The status of the payment.
$status_text = $_POST['status_text']; //A text string describing the status of the payment.
$txnexists = 0;
// Check the original currency to make sure the buyer didn't change it.
if ($currency1 != $order_currency) {
	errorAndDie("Original currency mismatch!\n");
}

//Get Package ID and User ID
list($membership_id, $user_id) = explode("_", $custom);

//Get Package Price
$pricesql = $odb -> prepare("SELECT `price` FROM `packages` WHERE id = :id");
$pricesql -> execute(array(":id" => (int)$membership_id));
$price = $pricesql -> fetchColumn(0);

// Check amount against order total
if ($amount1 < $price) {
	errorAndDie("Amount is less than order total!\n");
}

// check if exists
$checksql = $odb -> prepare("SELECT `tid` FROM `payment_logs` WHERE tid = :txn_id LIMIT 1");
$checksql -> execute(array(':txn_id' => $txn_id));
if ($checksql->fetchColumn()) {
	$txnexists = 1;
	errorAndDie("Transaction already exists\n");
}
else {
	$txnexists = 2;
}


if (($status >= 100 || $status == 2) && $txnexists == 2) {
	$data = array(
		':tid' => $txn_id,
		':proccessor' => 'BTC',
		':user' => (int)$user_id,
		':packageid' => (int)$membership_id,
		':paid' => $amount1,
	  );
	  $odb -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
	  $insertsql = $odb -> prepare("INSERT INTO `payment_logs` VALUES(NULL, :tid, :user, :proccessor, :paid, :packageid, UNIX_TIMESTAMP(), 1)");
	  $insertsql -> execute($data);
		$SQLGetUserInfo = $odb -> prepare("SELECT `username`,`email`,`rVPN`,`expire` FROM `accounts` WHERE `id` = :id LIMIT 1");
		$SQLGetUserInfo -> execute(array(':id' => $user_id));
		$userInfo = $SQLGetUserInfo -> fetch(PDO::FETCH_ASSOC);
		$userMail = $userInfo['email'];
		$userName = $userInfo['username'];
		$currpass = $userInfo['rVPN'];
		$currexpire = $userInfo['expire'];

		//GET PACKAGE DETAILS
		$SQLGetPackages = $odb -> prepare("SELECT * FROM `packages` WHERE `id` = :id LIMIT 1");
		$SQLGetPackages -> execute(array(':id' => $membership_id));
		$getInfo = $SQLGetPackages -> fetch(PDO::FETCH_ASSOC);
		$portforward = $getInfo['pfenabled'];
		$plength = $getInfo['length'];
		$unit = $getInfo['unit'];

		//email fix
		if (($plength == '1') && ($unit == 'Days'))
			$displayUnit = "Day";
		else $displayUnit = $unit;
		
		if (($plength == '1') && ($unit == 'Months'))
			$displayUnit = "Month";

		if ($currexpire != 0) {
			$newExpire = strtotime("+{$plength} {$unit}", $currexpire);

			$update1 = $odb -> prepare("UPDATE `accounts` SET `expire` = :expire WHERE `id` = :id");
			$update1 -> execute(array(':expire' => $newExpire, ':id' => $user_id));

			$maildate = date("m-d-Y h:i:s A", $newExpire);
			//Mail VPN Information
			$mailcontent = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
										<html xmlns="http://www.w3.org/1999/xhtml">
										<head>
										<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
										<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
										<title>Email template</title>
										<link href=\'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600\' rel=\'stylesheet\' type=\'text/css\'>
										<style type="text/css">
										/*Reset*/
										*{margin-top:0px; margin-bottom:0px; padding:0px; border:none; line-height:normal; outline:none; list-style:none; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; font-weight:300;}
										table{border-collapse:collapse !important; padding:0px !important; border:none !important; border-bottom-width:0px !important; mso-table-lspace:0pt; mso-table-rspace:0pt;}
										table td {border-collapse: collapse; }
										body {margin:0px; padding:0px; background-color:#fff;}
										.ExternalClass *{line-height:100%;}
										html, body{height:100%;}
										@media(max-width:660px){
										  table[class=scale]{width:100% !important;}
										  td[class=scale]{width:100% !important;}
										  td[class=text-center]{text-align:center !important;}
										  a[class=center]{margin:0 auto !important;}
										  img[class=center]{margin:0 auto !important;}
										  table[class=no-display], tr[class=no-display], td[class=no-display]{display:none !important;}
										  td[class=paddingTop]{padding-top:15px !important;}
										  td[class=paddingLeftRight]{padding-left:15px !important; padding-right:15px !important;}
										}
										</style>
										</head>
										<body marginwidth="0" marginheight="0" style="margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;" offset="0" topmargin="0" leftmargin="0">

										<!-- Main Wrapper -->
										<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
										<tr>
										<td valign="top" align="center" bgcolor="#ebebeb">

										<!--Top Space-->
										<table width="100%" border="0" cellspacing="0" cellpadding="0" class="no-display">
										<tr><td height="57"></td></tr>
										</table>

										<!-- Page Wrapper -->
										<table width="660" border="0" cellspacing="0" cellpadding="0" class="scale">

										<!--Logo-->
										<tr>
										<td bgcolor="#fff">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
										<td bgcolor="#383838">
										<table width="600" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr><td height="10"></td></tr>
										<tr><td class="paddingLeftRight"><a href="https://crypticvpn.com"><img src="https://crypticvpn.com/templates/email/logo.png" border="0" style="display: block;" /></a></td></tr>
										<tr><td height="10"></td></tr>
										</table>
										</td>
										</tr>
										</table>
										</td>
										</tr>

										<!--Banner-->
										<tr>
										<td bgcolor="#36beec">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr class="no-display">
										<td height="45"></td>
										</tr>
										<tr>
										<td>
										<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr>
										<td>
										<table width="260" border="0" cellspacing="0" cellpadding="0" align="left" class="scale">
										<tr><td height="25"></td></tr>
										<tr>
										<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:26px; color:#3d3d3d;" class="text-center">Your Order Details</td>
										</tr>
										<tr><td height="30"></td></tr>
										<tr>
										<td class="text-center">
										<font style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px;">
										<a href="https://crypticvpn.com/dashboard.php#download" style="width:138px; border-radius:5px; background:#fff; color:#3d3d3d; font-size:15px; text-decoration:none; display:block; text-align:center; padding:8px 0;" class="center">Download Area</a>
										</font>
										</td>
										</tr>
										</table>
										<table width="294" border="0" cellspacing="0" cellpadding="0" align="right" class="scale">
										<tr>
										<td class="paddingTop"><img src="https://crypticvpn.com/templates/email/display.png" border="0" style="display: block;" class="center" /></td>
										</tr>
										</table>
										</td>
										</tr>
										</table>
										</td>
										</tr>
										</table>
										</td>
										</tr>

										<!--Contents-->
										<tr>
										<td bgcolor="#fff" class="paddingLeftRight">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr><td height="40"></td></tr>
										<tr>
										<td>

										<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr>
										<td width="40" style="width:40px" class="no-display">&nbsp;</td>
										<td width="580" style="width:580px; word-wrap:break-word;" class="scale">
										<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr>
										<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
										Hello '.$userName.', <br><br> Your VPN subscription has been extended until <b>'.$maildate.'.</b><br>
										</td>
										</tr>
										<tr><td height="20"></td></tr>
										<tr>
										<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
										Your order details are as follows:
										<br>
										<strong>VPN Subscription Length:</strong> '.$plength.' '.$displayUnit.'<br>
										<strong>Expiration Date/Time:</strong> '.date('m/d/Y H:i:s', $newExpire).'<br>
										</td>
										</tr>
										<tr><td height="20"></td></tr>
										<tr>
										<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
										Login to <a href="https://crypticvpn.com">CrypticVPN.com</a> and go to the "Downloads" tab to download our client or VPN configuration files.
										</td>
										</tr>
										<tr><td height="20"></td></tr>
										<tr>
										<td class="text-center" style="word-wrap:break-wordstyle="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#003caa; line-height:25px; font-weight:300; word-wrap:break-word;"> Have questions or need help setting up the VPN? Check out our <a href="https://crypticvpn.com/faq.php">Frequently Asked Questions.</a> page. If you are unable to find the answer to your question there feel free to submit a <a href="https://crypticvpn.com/support.php">support ticket</a> and we will get back to you as soon as possible.
										</td>
										</tr>
										<tr><td height="35"></td></tr>
										<tr>
										<td align="right" style="font-family:\'Source Sans Pro\', sans-serif; font-size:14px; color:#a4a4a4; font-weight:300;" class="text-center">You are recieving this email because you are part of the Cryptic VPN Mailing List.</td>
										</tr>
										</table>
										</td>
										<td width="40" style="width:40px;" class="no-display">&nbsp;</td>
										</tr>
										</table>

										</td>
										</tr>
										<tr><td height="40"></td></tr>
										</table>
										</td>
										</tr>

										<!--Footer-->
										<tr>
										<td>
										<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr><td height="15"></td></tr>
										<tr>
										<td class="paddingLeftRight">
										<table width="390" border="0" cellspacing="0" cellpadding="0" align="left" class="scale">
										<tr>
										<td style="font-family:\'Source Sans Pro\', sans-serif; font-size:13px; color:#aaaaaa; line-height:28px; font-weight:300;" class="text-center">&copy; Copyright &copy; 2016 CrypticVPN.com. All Rights Reserved.</td>
										</tr>
										</table>
										<table width="64" border="0" cellspacing="0" cellpadding="0" align="right" class="scale">
										<tr>
										<td class="text-center">
										<a href="https://www.facebook.com/CrypticVPN/" style="display:inline-block"><img src="https://crypticvpn.com/templates/email/facebook.jpg" border="0" style="display: block;"/></a> 
										<a href="https://twitter.com/CrypticVPN" style="display:inline-block"><img src="https://crypticvpn.com/templates/email/twitter.jpg" border="0" style="display: block;"/></a>
										</td>
										</tr>
										</table>
										</td>
										</tr>
										<tr><td height="15"></td></tr>
										</table>
										</td>
										</tr>

										</table>

										</td>
										</tr>
										</table>

										</body>
										</html>';

			$from = $gsetting -> getSiteMail($odb);
			$fromname = $gsetting -> getSiteTitle($odb);
			$mail = new PHPMailer;
			if ($rvpnmailer == 'php') {
				$mail->isSendmail();
			} else {
				$mail->isSMTP();
        		$mail->SMTPSecure = $smtpauthtype;
				$mail->Host = $rshost;
				$mail->Port = $rsport;
				$mail->SMTPAuth = true;
				$mail->Username = $rsuser;
				$mail->Password = $rspass;
			}
			$mail->setFrom($from, $fromname);
			$mail->addReplyTo($from, $fromname);
			$mail->addAddress($userMail, $userName);
			$mail->Subject = 'CrypticVPN Subscription Extended';
			$mail->msgHTML($mailcontent);
			$mail->send();
		} else {
			$newExpire = strtotime("+{$plength} {$unit}");

			$update1 = $odb -> prepare("UPDATE `accounts` SET `expire` = :expire WHERE `id` = :id");
			$update1 -> execute(array(':expire' => $newExpire, ':id' => $user_id));
			$update2 = $odb -> prepare("UPDATE `accounts` SET `pfoption` = :pfoption WHERE `id` = :id");
			$update2 -> execute(array(':pfoption' => $portforward, ':id' => $user_id));

			$vpnPass = $rvpn -> genPass();

			//Create VPN Account
			$rvpn -> createVPNAccount($rad, $user_id, $vpnPass, $encryptionKey);

			//Mail VPN Information
			$mailcontent = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
			<title>Email template</title>
			<link href=\'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600\' rel=\'stylesheet\' type=\'text/css\'>
			<style type="text/css">
			/*Reset*/
			*{margin-top:0px; margin-bottom:0px; padding:0px; border:none; line-height:normal; outline:none; list-style:none; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; font-weight:300;}
			table{border-collapse:collapse !important; padding:0px !important; border:none !important; border-bottom-width:0px !important; mso-table-lspace:0pt; mso-table-rspace:0pt;}
			table td {border-collapse: collapse; }
			body {margin:0px; padding:0px; background-color:#fff;}
			.ExternalClass *{line-height:100%;}
			html, body{height:100%;}
			@media(max-width:660px){
			table[class=scale]{width:100% !important;}
			td[class=scale]{width:100% !important;}
			td[class=text-center]{text-align:center !important;}
			a[class=center]{margin:0 auto !important;}
			img[class=center]{margin:0 auto !important;}
			table[class=no-display], tr[class=no-display], td[class=no-display]{display:none !important;}
			td[class=paddingTop]{padding-top:15px !important;}
			td[class=paddingLeftRight]{padding-left:15px !important; padding-right:15px !important;}
			}
			</style>
			</head>
			<body marginwidth="0" marginheight="0" style="margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;" offset="0" topmargin="0" leftmargin="0">

			<!-- Main Wrapper -->
			<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>
			<td valign="top" align="center" bgcolor="#ebebeb">

			<!--Top Space-->
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="no-display">
			<tr><td height="57"></td></tr>
			</table>

			<!-- Page Wrapper -->
			<table width="660" border="0" cellspacing="0" cellpadding="0" class="scale">

			<!--Logo-->
			<tr>
			<td bgcolor="#fff">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td bgcolor="#383838">
			<table width="600" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
			<tr><td height="10"></td></tr>
			<tr><td class="paddingLeftRight"><a href="https://crypticvpn.com"><img src="https://crypticvpn.com/templates/email/logo.png" border="0" style="display: block;" /></a></td></tr>
			<tr><td height="10"></td></tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>

			<!--Banner-->
			<tr>
			<td bgcolor="#36beec">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr class="no-display">
			<td height="45"></td>
			</tr>
			<tr>
			<td>
			<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
			<tr>
			<td>
			<table width="260" border="0" cellspacing="0" cellpadding="0" align="left" class="scale">
			<tr><td height="25"></td></tr>
			<tr>
			<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:26px; color:#3d3d3d;" class="text-center">Your Order Details</td>
			</tr>
			<tr><td height="30"></td></tr>
			<tr>
			<td class="text-center">
			<font style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px;">
			<a href="https://crypticvpn.com/dashboard.php#download" style="width:138px; border-radius:5px; background:#fff; color:#3d3d3d; font-size:15px; text-decoration:none; display:block; text-align:center; padding:8px 0;" class="center">Download Area</a>
			</font>
			</td>
			</tr>
			</table>
			<table width="294" border="0" cellspacing="0" cellpadding="0" align="right" class="scale">
			<tr>
			<td class="paddingTop"><img src="https://crypticvpn.com/templates/email/display.png" border="0" style="display: block;" class="center" /></td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			</table>
			</td>
			</tr>

			<!--Contents-->
			<tr>
			<td bgcolor="#fff" class="paddingLeftRight">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr><td height="40"></td></tr>
			<tr>
			<td>

			<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
			<tr>
			<td width="40" style="width:40px" class="no-display">&nbsp;</td>
			<td width="580" style="width:580px; word-wrap:break-word;" class="scale">
			<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
			<tr>
			<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
			Thanks for purchasing CrypticVPN!
			</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
			<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
			Your order details are as follows:
			<br>
			<strong>VPN Subscription Length:</strong> '.$plength.' '.$unit.'<br>
			<strong>Expiration Date/Time:</strong> '.date('m/d/Y H:i:s', $newExpire).'<br>
			<strong>VPN Username:</strong> '.$user_id.'<br>
			<strong>VPN Password:</strong> '.$vpnPass.'<br>
			</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
			<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
			Login to <a href="https://crypticvpn.com">CrypticVPN.com</a> and go to the "Downloads" tab to download our client or VPN configuration files.
			</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
			<td class="text-center" style="word-wrap:break-wordstyle="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#003caa; line-height:25px; font-weight:300; word-wrap:break-word;"> Have questions or need help setting up the VPN? Check out our <a href="https://crypticvpn.com/faq.php">Frequently Asked Questions.</a> page. If you are unable to find the answer to your question there feel free to submit a <a href="https://crypticvpn.com/support.php">support ticket</a> and we will get back to you as soon as possible.
			</td>
			</tr>
			<tr><td height="35"></td></tr>
			<tr>
			<td align="right" style="font-family:\'Source Sans Pro\', sans-serif; font-size:14px; color:#a4a4a4; font-weight:300;" class="text-center">You are recieving this email because you are part of the Cryptic VPN Mailing List.</td>
			</tr>
			</table>
			</td>
			<td width="40" style="width:40px;" class="no-display">&nbsp;</td>
			</tr>
			</table>

			</td>
			</tr>
			<tr><td height="40"></td></tr>
			</table>
			</td>
			</tr>

			<!--Footer-->
			<tr>
			<td>
			<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
			<tr><td height="15"></td></tr>
			<tr>
			<td class="paddingLeftRight">
			<table width="390" border="0" cellspacing="0" cellpadding="0" align="left" class="scale">
			<tr>
			<td style="font-family:\'Source Sans Pro\', sans-serif; font-size:13px; color:#aaaaaa; line-height:28px; font-weight:300;" class="text-center">&copy; Copyright &copy; 2016 CrypticVPN.com. All Rights Reserved.</td>
			</tr>
			</table>
			<table width="64" border="0" cellspacing="0" cellpadding="0" align="right" class="scale">
			<tr>
			<td class="text-center">
			<a href="https://www.facebook.com/CrypticVPN/" style="display:inline-block"><img src="https://crypticvpn.com/templates/email/facebook.jpg" border="0" style="display: block;"/></a> 
			<a href="https://twitter.com/CrypticVPN" style="display:inline-block"><img src="https://crypticvpn.com/templates/email/twitter.jpg" border="0" style="display: block;"/></a>
			</td>
			</tr>
			</table>
			</td>
			</tr>
			<tr><td height="15"></td></tr>
			</table>
			</td>
			</tr>

			</table>

			</td>
			</tr>
			</table>

			</body>
			</html>';
									
			$from = $gsetting -> getSiteMail($odb);
			$fromname = $gsetting -> getSiteTitle($odb);
			$mail = new PHPMailer;
			if ($rvpnmailer == 'php') {
				$mail->isSendmail();
			} else {
				$mail->isSMTP();
        $mail->SMTPSecure = $smtpauthtype;
				$mail->Host = $rshost;
				$mail->Port = $rsport;
				$mail->SMTPAuth = true;
				$mail->Username = $rsuser;
				$mail->Password = $rspass;
			}
			$mail->setFrom($from, $fromname);
			$mail->addReplyTo($from, $fromname);
			$mail->addAddress($userMail, $userName);
			$mail->Subject = 'CrypticVPN Order Confirmation';
			$mail->msgHTML($mailcontent);
			$mail->send();
		}

} else if ($status < 0) {
	echo $status_text;
} else {
	echo $status_text;
}
die('IPN OK');

?>
