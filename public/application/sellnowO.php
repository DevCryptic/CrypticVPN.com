<?php
error_reporting(0);
include 'config.php';
include 'global.php';
require_once('mail/PHPMailerAutoload.php');

function errorAndDie($error_msg) { 
        $report = 'Error: '.$error_msg."\n\n"; 
        $report .= "POST Data\n\n"; 
        foreach ($_POST as $k => $v) { 
            $report .= "|$k| = |$v|\n"; 
        } 
		$file = "SellNowDebug.txt";
		$open = fopen($file, "a");
		fwrite ($open, $report);$file = "SellNowDebug.txt";
		$open = fopen($file, "a");
		fwrite ($open, $report);
} 

try {
	//sellnow stuff
	abstract class rocketrPaymentMethods
	{
	    const PAYPAL = 0;
	    const BITCOIN = 1;
	    const ETHEREUM = 2;
	    const PERFECT_MONEY = 3;
	    const STRIPE = 4;
	    const BCC = 6;

	}

	abstract class rocketrOrderStatus {
	    const TIMED_OUT = -1; //This means the buyer did not pay
	    const NEW_ORDER = 0; //Order was just created, the buyer may or may not pay
	    const WAITING_FOR_PAYMENT = 1; //This is exclusive for cryptocurrency payments, this means we are waiting for confirmations
	    const ERROR_PARTIAL_PAYMENT_RECEIVED = 2; //the buyer only paid a partial amount
	    const FULL_PAYMENT_RECEIVED = 3; //this order status signifies that the product delivery failed (e.g. b/c the buyers email was incorrect or out of stock)
	    const PRODUCT_DELIVERED = 4; // AKA success. This signifies product email delivery
	    const REFUNDED = 5; //The order was refunded
	        
	    const UNKNOWN_ERROR = 6;
	    
	    const PAYPAL_PENDING = 8;
	    const PAYPAL_OTHER = 9; //if a paypal dispute is favored to the seller, this is the order status.
	    const PAYPAL_REVERSED = 10; //buyer disputed via paypal
	    
	    const STRIPE_AUTO_REFUND = 20;
	    const STRIPE_DECLINED = 21;
	    const STRIPE_DISPUTED = 22;
	    
	}

	if(!isset($_POST) || sizeof($_POST) === 0 || !isset($_SERVER['HTTP_IPN_HASH'])) {    
	    die('Received Invalid IPN ');
	}

	$IPN_SECRET = 'ASLK1N24124'; /* Please enter your IPN secret here*/
	$hmac = hash_hmac("sha512", json_encode($_POST), trim($IPN_SECRET));
	if ($hmac != $_SERVER['HTTP_IPN_HASH']) { 
	    die('IPN Hash does not match'); 
	}

	$order_id = $_POST['order_id'];
	$product_title = $_POST['product_title'];
	$product_id =($_POST['product_id']);
	$buyer_email = $_POST['buyer_email'];
	$buyer_ip = $_POST['buyer_ip'];
	$payment_method = intval($_POST['payment_method']);
	$invoice_amount_usd = floatval($_POST['invoice_amount_usd']);
	$quantity = intval($_POST['quantity']);
	$purchased_at = $_POST['purchased_at'];
	$txn_id = $_POST['txn_id'];
	$status = intval($_POST['status']);

	//procesor name
	if ($payment_method == '0')
		$processorName = "Paypal";
	else if ($payment_method == '1')
		$processorName = "BTC";
	else if ($payment_method == '2')
		$processorName = "Ethereum";
	else if ($payment_method == '3')
		$processorName = "PerfectMoney";
	else if ($payment_method == '6')
		$processorName = "BCC";

    //processor name end

	//get userid
	$checksql1 = $odb -> prepare("SELECT `id` FROM `accounts` WHERE email = :email LIMIT 1");
	$checksql1 -> execute(array(':email' => $buyer_email));
	$user_id = $checksql1 ->  fetchColumn ( 0 );

	if($status === rocketrOrderStatus::PRODUCT_DELIVERED) {

			//sell now stuff end
			$txnexists = 0;

			// check if exists
			$checksql = $odb -> prepare("SELECT `tid` FROM `payment_logs` WHERE tid = :txn_id LIMIT 1");
			$checksql -> execute(array(':txn_id' => $order_id));
			if ($checksql->fetchColumn()) {
				$txnexists = 1;
				errorAndDie("Transaction already exists\n");
			}
			else {
				$txnexists = 2;
			}

			if ($txnexists == 2) {
				$data = array(
					':tid' => $order_id,
					':proccessor' => $processorName, 
					':user' => $user_id,
					':packageid' => $product_id,
					':paid' => $invoice_amount_usd,
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
					$SQLGetPackages = $odb -> prepare("SELECT * FROM `packages` WHERE `sellNowProductID` = :id LIMIT 1");
					$SQLGetPackages -> execute(array(':id' => $product_id));
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

					//if ($currexpire != 0) {
					if (intval($currexpire) !== 0) {
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
			}
		}
		else if($status === rocketrOrderStatus::PAYPAL_REVERSED) {
					errorAndDie("CB IPN received\n");
					errorAndDie("#1\n");

					$SQLGetUserInfo = $odb -> prepare("SELECT `username`,`email`, `isbanned` FROM `accounts` WHERE `id` = :id LIMIT 1");
					$SQLGetUserInfo -> execute(array(':id' => $user_id));
					$userInfo = $SQLGetUserInfo -> fetch(PDO::FETCH_ASSOC);
					$userMail = $userInfo['email'];
					$userName = $userInfo['username'];
					$isBanned = $userInfo['isbanned'];
					if ($isBanned == 1){
						errorAndDie("Account Already Banned\n");
					}
					else
					{
						errorAndDie("Credentials Obtained\n");
						errorAndDie($userName); 
						errorAndDie(json_encode($userMail));
						errorAndDie($userMail);
						errorAndDie("#2\n");
						errorAndDie($userName); 
						errorAndDie("#3\n");

						$rvpn -> deleteVPNAccount($rad, $user_id, $encryptionKey);
						errorAndDie("VPN Deleted\n");

						$SQL = $odb -> prepare("UPDATE `accounts` SET `isbanned` = 1, `rVPN` = 0, `expire` = 0 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $user_id));
						errorAndDie("Banned #1\n");

						$mailcontent = 'Hello '.$userName.', <br><br> Your account has been banned due to opening a PayPal dispute/chargeback. <br><br>You can appeal this decision by closing the dispute immediately and emailing admin@crypticvpn.com<br><br> -CrypticVPN';
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
							$mail->SMTPAuth = $smtpauthstat;
							$mail->Username = $rsuser;
							$mail->Password = $rspass;
						}
						$mail->setFrom($from, $fromname);
						$mail->addReplyTo($from, $fromname);
						$mail->addAddress($userMail, $userName);
						$mail->addAddress('admin@crypticvpn.com', 'cryptic');
						$mail->Subject = 'Your CrypticVPN account has been banned!';
						$mail->msgHTML($mailcontent);
						$mail->send();
						errorAndDie("Email sent\n");
					}

		}

	die('IPN OK');
}catch(Exception $e) { errorAndDie($e); }
?>
