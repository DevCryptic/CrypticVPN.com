<?php
define("_VALID_PHP", true);
  function debugPP($string)
	{
	  $file = "debug.txt";
	  $open = fopen($file, "a");
	  fwrite($open, $string);
	}
  if (isset($_POST['payment_status'])) {

     require_once('config.php');
	 require_once('global.php');
	require_once('mail/PHPMailerAutoload.php');

      set_time_limit(0);
      function verifyTxnId($txn_id, $odb)
      {
          $sql = $odb->prepare("SELECT COUNT(id) FROM `payment_logs` WHERE tid = :tid LIMIT 1");
		$sql -> execute(array(":tid" => $txn_id));
          if ($sql -> fetchColumn(0) > 0)
              return false;
          else
              return true;

      }

      $req = 'cmd=_notify-validate';

      foreach ($_POST as $key => $value) {
          $value = urlencode(stripslashes($value));
          $req .= '&' . $key . '=' . $value;

      }
      $demo = false;
      $url = 'www.paypal.com';

      $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
      $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
      $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
      $fp = fsockopen($url, 80, $errno, $errstr, 30);

      $payment_status = $_POST['payment_status'];
      $receiver_email = $_POST['business'];
      list($membership_id, $user_id) = explode("_", $_POST['item_number']);
      $mc_gross = $_POST['mc_gross'];
      $txn_id = $_POST['txn_id'];

	  debugPP("Selecting Price\n");

      $getxn_id = verifyTxnId($txn_id, $odb);
      $pricesql = $odb -> prepare("SELECT `price` FROM `packages` WHERE id = :id");
      $pricesql -> execute(array(":id" => (int)$membership_id));
      $price = $pricesql -> fetchColumn(0);

	  debugPP("Selecting Client's PayPal Email\n");

      $pp_emailsql = $odb -> query("SELECT `paypal` FROM `settings` LIMIT 1");
      $pp_email = $pp_emailsql->fetchColumn(0);

      if (!$fp) {
          echo $errstr . ' (' . $errno . ')';
      } else {
          fputs($fp, $header . $req);

      debugPP("Phase 1\n");

          while (!feof($fp)) {
        $res = fgets($fp, 1024);
		debugPP("Phase 2\n");
              if (strcmp($res, "VERIFIED") == 0) {
                  if (preg_match('/Completed/', $payment_status)) {
					   debugPP("Phase 3\n");
					   if($mc_gross == $price){
						   debugPP("price check success!");
			$SQLGetUser = $odb -> prepare("SELECT `username` FROM `accounts` WHERE `id` = :id");
			$SQLGetUser -> execute(array(':id' => $user_id));
			$payerusername = $SQLGetUser -> fetchColumn(0);
			debugPP("Phase 6\n");
							$SQLGetUserInfo = $odb -> prepare("SELECT `username`,`email`,`rVPN`,`expire` FROM `accounts` WHERE `id` = :id LIMIT 1");
							$SQLGetUserInfo -> execute(array(':id' => $user_id));
							$userInfo = $SQLGetUserInfo -> fetch(PDO::FETCH_ASSOC);
							$userMail = $userInfo['email'];
							$userName = $userInfo['username'];
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
								$mailcontent =  '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
									$mail->SMTPAuth = $smtpauthstat;
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
								debugPP("Phase 7\n");
								$update1 = $odb -> prepare("UPDATE `accounts` SET `expire` = :expire WHERE `id` = :id");
								$update1 -> execute(array(':expire' => $newExpire, ':id' => $user_id));
								$update2 = $odb -> prepare("UPDATE `accounts` SET `pfoption` = :pfoption WHERE `id` = :id");
								$update2 -> execute(array(':pfoption' => $portforward, ':id' => $user_id));
								debugPP("Phase 8\n");
								$vpnPass = $rvpn -> genPass();
								//$update3 = $odb -> prepare("UPDATE `accounts` SET `rVPN` = :rvpn WHERE `id` = :id");
								//$update3 -> execute(array(':rvpn' => $vpnPass, ':id' => $user_id));
								debugPP("Phase 9\n");
								//Check if there an offline server and save action for it
								//$rvpn -> saveAction($odb, 'create', $user_id, $vpnPass, 0, 0);
								//debugPP("Phase 10\n");
								//Create VPN Account
								$rvpn -> createVPNAccount($rad, $user_id, $vpnPass, $encryptionKey);
								debugPP("Phase 11\n");
								//Mail VPN Information

								//$mailcontent = 'Hello, <br><br> Below you can find your vpn client login information: <br>Username: '.$user_id.'<br>Password: '.$vpnPass.'<br><br>Thank you!';
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
										<strong>VPN Subscription Length:</strong> '.$plength.' '.$displayUnit.'<br>
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
								debugPP("Phase 12\n");
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
					$data = array(
							':tid' => $txn_id,
							':plan' => $membership_id,
							':proccessor' => 'Paypal',
							':user' => $user_id,
							':paid' => $mc_gross,
						  );
						  $odb -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
						  $insertsql = $odb -> prepare("INSERT INTO `payment_logs` VALUES(NULL, :tid, :user, :proccessor, :paid, :plan, UNIX_TIMESTAMP(), 1)");
						  $insertsql -> execute($data);
						  debugPP("Phase 14\n");
                  }
				  }else{
					  debugPP("Price Check Failed");
				  }

              }
          }
          fclose($fp);
      }
  }
?>