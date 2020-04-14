<?php
require '/home/nginx/domains/crypticvpn.com/public/application/config.php';
require '/home/nginx/domains/crypticvpn.com/public/application/global.php';

		$SQLgetTickets = $odb -> prepare("SELECT id, senderid from tickets WHERE date < unix_timestamp(now() - interval 7 day) AND status = 2");
		$SQLgetTickets -> execute();
		while ($ticketInfo = $SQLgetTickets -> fetch(PDO::FETCH_ASSOC))
		{
			$ticketID = $ticketInfo['id'];
			$senderID = $ticketInfo['senderid'];

			$SQLGetUserInfo = $odb -> prepare("SELECT `username`,`email` FROM `accounts` WHERE `id` = :id LIMIT 1");
			$SQLGetUserInfo -> execute(array(':id' => $senderID));
			$userInfo = $SQLGetUserInfo -> fetch(PDO::FETCH_ASSOC);
			$eUsermail = $userInfo['email'];
			$eUsername = $userInfo['username'];
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
								<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:26px; color:#3d3d3d;" class="text-center">Email From Support</td>
								</tr>
								<tr><td height="30"></td></tr>
								<tr>
								<td class="text-center">
								<font style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px;">
								<a href="https://crypticvpn.com/support.php" style="width:138px; border-radius:5px; background:#fff; color:#3d3d3d; font-size:15px; text-decoration:none; display:block; text-align:center; padding:8px 0;" class="center">Contact Us</a>
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
								Hello '.$eUsername.', <br><br> This is a notification to let you know that we are changing the status of your ticket #'.$ticketID.' to Closed as we have not received a response from you in over 7 days.

								</b><br><br>
								If you have any more questions or concerns, feel free to submit a new ticket.<br><br>
								Thanks,<br><br>
								CrypticVPN Support
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
			$mail->addAddress($eUsermail, $eUsername);
			$mail->Subject = 'Ticket #'.$ticketID.' is now Closed';
			$mail->msgHTML($mailcontent);
			$mail->send();
			$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = 3 WHERE `id` = :id ");
			$SQLUpdate -> execute(array(':id' => $ticketID));
		}