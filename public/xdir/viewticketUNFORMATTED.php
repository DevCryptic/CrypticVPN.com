<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)))
{
	header('Location: login.php');
	die();
}

$ticketid = $_GET['id'];
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	header('location: pendingtickets.php');
	die();
}
$SQLGetInfo = $odb -> prepare("SELECT * FROM `tickets` WHERE `id` = :id LIMIT 1");
$SQLGetInfo -> execute(array(':id' => $ticketid));
$ticketInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
$title = $ticketInfo['title'];
$details = $ticketInfo['details'];
$category = $ticketInfo['department'];
$status = $ticketInfo['status'];
$author = $ticketInfo['senderid'];
$tdate = date("m-d-Y h:i:s A", $ticketInfo['date']);

if ($status == 1) {
	$statustext = '<span class="uk-badge uk-badge-warning">Pending</span>';
} elseif ($status == 2){
	$statustext = '<span class="uk-badge uk-badge-success">Answered</span>';
} elseif ($status == 3){
	$statustext = '<span class="uk-badge uk-badge-danger">Closed</span>';
} elseif ($status == 4){
	$statustext = '<span class="uk-badge uk-badge-danger">Closed</span>';
}

$SQLGetTicketOwner = $odb -> prepare("SELECT `username` FROM `accounts` WHERE `id` = :id LIMIT 1");
$SQLGetTicketOwner -> execute(array(':id' => (int)$author));
$ticketowner = $SQLGetTicketOwner -> fetch(PDO::FETCH_ASSOC);
$ticketownerusername = $ticketowner['username'];

$SQLGetExpireInfo = $odb -> prepare("SELECT * FROM `accounts` WHERE `id` = :userid LIMIT 1");
$SQLGetExpireInfo -> execute(array(':userid' => $author));
$expireInfo = $SQLGetExpireInfo -> fetch(PDO::FETCH_ASSOC);
$expire = $expireInfo['expire'];


?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Remove Tap Highlight on Windows Phone IE -->
    <meta name="msapplication-tap-highlight" content="no"/>

    <link rel="icon" type="image/png" href="assets/img/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="assets/img/favicon-32x32.png" sizes="32x32">

    <title><?php echo $site_title; ?> - View Ticket (#<?php echo $ticketid; ?>)</title>

    <!-- additional styles for plugins -->
        <!-- weather icons -->
        <link rel="stylesheet" href="bower_components/weather-icons/css/weather-icons.min.css" media="all">
        <!-- metrics graphics (charts) -->
        <link rel="stylesheet" href="bower_components/metrics-graphics/dist/metricsgraphics.css">
        <!-- chartist -->
        <link rel="stylesheet" href="bower_components/chartist/dist/chartist.min.css">

    <!-- uikit -->
    <link rel="stylesheet" href="bower_components/uikit/css/uikit.almost-flat.min.css" media="all">

    <!-- flag icons -->
    <link rel="stylesheet" href="assets/icons/flags/flags.min.css" media="all">

    <!-- altair admin -->
    <link rel="stylesheet" href="assets/css/main.min.css" media="all">

    <!-- matchMedia polyfill for testing media queries in JS -->
    <!--[if lte IE 9]>
        <script type="text/javascript" src="bower_components/matchMedia/matchMedia.js"></script>
        <script type="text/javascript" src="bower_components/matchMedia/matchMedia.addListener.js"></script>
    <![endif]-->

</head>
<body class=" sidebar_main_open sidebar_main_swipe">
    <?php include 'templates/header.php'; ?>

	<?php include 'templates/sidebar.php'; ?>

    <div id="page_content">
        <div id="page_content_inner">

            <?php include 'templates/header_stats.php'; ?>
			<br>
			<?php
				if (isset($_POST['sendReply']))
				{

					$reply = htmlspecialchars($_POST['reply']);
					$errors = array();
					if (empty($reply))
					{
						$errors[] = 'Please enter a reply to send.';
					}
					if (strlen($reply) < 1 || strlen($reply) > 4096)
					{
						$errors[] = 'Your answer must contain between 1 - 4096 characters.';
					}

					if(empty($errors)) {
						$replytoStore = nl2br(htmlentities($reply, ENT_QUOTES, 'UTF-8'));
						$insertReply = $odb -> prepare("INSERT INTO `ticketreplies` (`tid`, `author`, `reply`, `date`) VALUES
																		(:tickedID, :author, :reply, UNIX_TIMESTAMP())");
						$insertReply -> execute(array(':tickedID' => $ticketid, ':author' => $_SESSION['aID'], ':reply' => $replytoStore));
						$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = 2 WHERE `id` = :id ");
						$SQLUpdate -> execute(array(':id' => $ticketid));
						$SQLGetUserInfo = $odb -> prepare("SELECT `username`,`email` FROM `accounts` WHERE `id` = :id LIMIT 1");
						$SQLGetUserInfo -> execute(array(':id' => $author));
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
										Dear '.$eUsername.', <br><br> A staff member has just replied to your ticket.</b><br><br>
										Please login to our <a href="https://crypticvpn.com/support.php">website</a> to view the latest details on the ticket.<br><br>
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
						$mail->Subject = 'Reply to ticket #'.$ticketid.'';
						$mail->msgHTML($mailcontent);
						$mail->send();
						echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have sent your reply successfully!</div><meta http-equiv="refresh" content="0;url='.$_SERVER['REQUEST_URI'].'">';
						} else {
						echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a>';
						foreach($errors as $error)
						{
							echo '- '.$error.'<br />';
						}
						echo '</div>';
						}
				}

				if (isset($_POST['closeTicket']))
				{
					$SQLGetUserInfo = $odb -> prepare("SELECT `username`,`email` FROM `accounts` WHERE `id` = :id LIMIT 1");
					$SQLGetUserInfo -> execute(array(':id' => $author));
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
										Dear '.$eUsername.', <br><br> Your ticket has just been marked closed.</b><br><br>
										If you have any more questions or concerns, or feel that this was closed in error, feel free to submit a new support request on our <a href="https://crypticvpn.com/support.php">website</a>.<br><br>
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
					$mail->Subject = 'Ticket #'.$ticketid.' is now Closed';
					$mail->msgHTML($mailcontent);
					$mail->send();
					$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = 3 WHERE `id` = :id ");
					$SQLUpdate -> execute(array(':id' => $ticketid));
					echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have closed the ticket successfully!</div><meta http-equiv="refresh" content="0;url='.$_SERVER['REQUEST_URI'].'">';
				}
				if (isset($_POST['openTicket']))
				{
					$SQLGetUserInfo = $odb -> prepare("SELECT `username`,`email` FROM `accounts` WHERE `id` = :id LIMIT 1");
					$SQLGetUserInfo -> execute(array(':id' => $author));
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
										Dear '.$eUsername.', <br><br> Your ticket has just been re-opened by a staff member.</b><br><br>
										Please visit the ticket on our <a href="https://crypticvpn.com/support.php">website</a> to see any updates.<br><br>
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
					$mail->Subject = 'Ticket #'.$ticketid.' has been Re-opened';
					$mail->msgHTML($mailcontent);
					$mail->send();
					$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = 1 WHERE `id` = :id ");
					$SQLUpdate -> execute(array(':id' => $ticketid));
					echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have opened the ticket successfully!</div><meta http-equiv="refresh" content="0;url='.$_SERVER['REQUEST_URI'].'">';
				}
			?>
			<div class="md-card">
            <div class="md-card-content">
                <div class="uk-margin-bottom" data-uk-margin>
				<form method="post">
                    <div class="md-btn-group">
						<?php if ($status == 3){ echo '<button type="submit" name="openTicket" class="md-btn">Open Ticket</button>'; } else { echo '<button type="submit" name="closeTicket" class="md-btn">Close Ticket</button>';} ?>

                    </div>
				</form>
                </div>
                <hr/>
                <div class="uk-grid uk-grid-divider" data-uk-grid-margin>
                    <div class="uk-width-medium-3-4">
                        <div class="uk-margin-large-bottom">
                            <h2 class="heading_c uk-margin-small-bottom">Details:</h2>
                            <?php echo $details; ?>
                        </div>
                        <div class="uk-margin-large-bottom">
                            <h2 class="heading_c uk-margin-small-bottom">Replies</h2>
                            <ul class="uk-comment-list">
							<?php
								$checkIfExists = $odb -> prepare("SELECT * FROM `ticketreplies` WHERE `tid` = :ticketID");
								$checkIfExists -> execute(array(':ticketID' => $ticketid));
								if($checkIfExists -> rowCount() == 0)
								{
									echo '<i>No replies found for this ticket yet.</i>';
								} else {
									$SQLGetReplies = $odb -> prepare("SELECT * FROM `ticketreplies` WHERE `tid` = :tid ORDER BY `date`");
									$SQLGetReplies -> execute(array(':tid' => $ticketid));

							
									while($getInfo = $SQLGetReplies -> fetch(PDO::FETCH_ASSOC))
									{
										$author = $getInfo['author'];
										$reply = $getInfo['reply'];
										$date = date("m-d-Y h:i:s A", $getInfo['date']);

							$SQLGetAuthorUserName = $odb -> prepare("SELECT `username` FROM `accounts` WHERE `id` = :id LIMIT 1");
							$SQLGetAuthorUserName -> execute(array(':id' => (int)$author));
							$ReplyAuthor = $SQLGetAuthorUserName -> fetch(PDO::FETCH_ASSOC);
							$ReplyAuthorUserName = $ReplyAuthor['username'];

										echo '
											<li>
												<article class="uk-comment">
													<header class="uk-comment-header">
														<h4 class="uk-comment-title">'.$ReplyAuthorUserName.'</h4>
														<div class="uk-comment-meta">'.$date.'</div>
													</header>
													<div class="uk-comment-body">
														<p>'.nl2br(htmlspecialchars_decode($reply)).'</p>
													</div>
												</article>
											</li>';



										/*if ($author == $_SESSION['rID']){
											echo '
											<li>
												<article class="uk-comment">
													<header class="uk-comment-header">
														<h4 class="uk-comment-title">Administrator</h4>
														<div class="uk-comment-meta">'.$date.'</div>
													</header>
													<div class="uk-comment-body">
														<p>'.$reply.'</p>
													</div>
												</article>
											</li>';
										} else {
											echo '
											<li>
												<article class="uk-comment">
													<header class="uk-comment-header">
														<h4 class="uk-comment-title">'.$ticketownerusername.'</h4>
														<div class="uk-comment-meta">'.$date.'</div>
													</header>
													<div class="uk-comment-body">
														<p>'.$reply.'</p>
													</div>
												</article>
											</li>';
										}*/
									}
								}
							?>
                            </ul>
                        </div>
						<form method="post">
                        <textarea cols="30" rows="4" class="md-input" name="reply"></textarea>

                        <button type="submit" name="sendReply" class="md-btn uk-margin-top">Add Reply</button>
						</form>
                    </div>
                    <div class="uk-width-medium-1-4">
                        <div class="uk-margin-medium-bottom">
                            <p>
                                Status: <?php echo $statustext; ?>
                            </p>
                        </div>
                        <h2 class="heading_c uk-margin-small-bottom">Details</h2>
                        <ul class="md-list md-list-addon">
                            <li>
                                <div class="md-list-addon-element">
                                    <i class="md-list-addon-icon material-icons">&#xE8A6;</i>
                                </div>
                                <div class="md-list-content">
                                    <span class="md-list-heading"><?php echo $ticketownerusername; ?></span>
                                    <span class="uk-text-small uk-text-muted">Ticket Owner</span>
                                </div>
                            </li>
                            <li>
                                <div class="md-list-addon-element">
                                    <i class="md-list-addon-icon material-icons">&#xE8DF;</i>
                                </div>
                                <div class="md-list-content">
                                    <span class="md-list-heading"><?php echo $tdate; ?></span>
                                <br>
                                <strong>VPN Expires On:</strong> <?php if($expire != 0) { echo date("m-d-Y h:i:s A", $expire); } else { echo 'EXPIRED'; } ?>

                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>

    <!-- google web fonts -->
    <script>
        WebFontConfig = {
            google: {
                families: [
                    'Source+Code+Pro:400,700:latin',
                    'Roboto:400,300,500,700,400italic:latin'
                ]
            }
        };
        (function() {
            var wf = document.createElement('script');
            wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'true';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);
        })();
    </script>

    <!-- common functions -->
    <script src="assets/js/common.min.js"></script>
    <!-- uikit functions -->
    <script src="assets/js/uikit_custom.min.js"></script>
    <!-- altair common functions/helpers -->
    <script src="assets/js/altair_admin_common.min.js"></script>

    <!-- page specific plugins -->
        <!-- d3 -->
        <script src="bower_components/d3/d3.min.js"></script>
        <!-- metrics graphics (charts) -->
        <script src="bower_components/metrics-graphics/dist/metricsgraphics.min.js"></script>
        <!-- chartist (charts) -->
        <script src="bower_components/chartist/dist/chartist.min.js"></script>
        <!-- maplace (google maps) -->
        <script src="http://maps.google.com/maps/api/js?sensor=true"></script>
        <script src="bower_components/maplace.js/src/maplace-0.1.3.js"></script>
        <!-- peity (small charts) -->
        <script src="bower_components/peity/jquery.peity.min.js"></script>
        <!-- easy-pie-chart (circular statistics) -->
        <script src="bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
        <!-- countUp -->
        <script src="bower_components/countUp.js/countUp.min.js"></script>
        <!-- handlebars.js -->
        <script src="bower_components/handlebars/handlebars.min.js"></script>
        <script src="assets/js/custom/handlebars_helpers.min.js"></script>
        <!-- CLNDR -->
        <script src="bower_components/clndr/src/clndr.js"></script>
        <!-- fitvids -->
        <script src="bower_components/fitvids/jquery.fitvids.js"></script>

        <!--  dashbord functions -->
        <script src="assets/js/pages/dashboard.min.js"></script>

    <script>
        $(function() {
            // enable hires images
            altair_helpers.retina_images();
            // fastClick (touch devices)
            if(Modernizr.touch) {
                FastClick.attach(document.body);
            }
        });
    </script>
</body>
</html>
