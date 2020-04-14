<?php
ob_start();
require_once 'application/config.php';
require_once 'application/global.php';

if (!($user -> IsLogged($odb)))
{
	header('Location: login.php');
	die();
}
if ($user -> IsBanned($odb))
{
	header('Location: logout.php');
	die();
}
if (!($user -> IsActive($odb)))
{
	header('Location: activate.php');
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
 <title><?php echo $site_title; ?> - Support Desk</title>   
<?php include('templates/head.php'); ?>
<body>
    <?php include('templates/header.php'); ?>
    <div class="container">
      <div class="wrap-main">
        <div class="main-contain col-sm-11">
            <div class="left-content col-sm-3">
                <?php include('templates/sidebar_left.php'); ?>
            </div>
            <div class="right-content col-sm-9">
                <ul class="nav nav-tabs responsive" id="mysetting">
                    <li class="test-class <?php if(empty($_GET['tab']) || $_GET['tab']=='createticket') echo 'active'; ?>"><a class="deco-none misc-class" href="#createticket"> Create Ticket</a></li>
                    <li class="test-class <?php if(isset($_GET['tab']) && $_GET['tab']=='viewtickets') echo 'active'; ?>"><a href="#viewtickets">View Tickets</a></li>
                </ul>
                <div class="tab-content responsive">
                    <?php
						if (isset($_POST['sendTicket']))
						{
							$title = htmlspecialchars($_POST['subject'], ENT_QUOTES, 'UTF-8');
							$details = htmlspecialchars($_POST['details'], ENT_QUOTES, 'UTF-8');
							$department = htmlspecialchars($_POST['department'], ENT_QUOTES, 'UTF-8');
							$errors = array();

							if(empty($title) || empty($details) || empty($department))
							{
								$errors[] = 'Please fill in all required fields.';
							}
							$departments = array("1", "2", "3", "4");
								if (!in_array($department, $departments)) {
									$errors[] = 'Invalid department.';
								}
							if (strlen($title) > 250 || strlen($details) > 65000)
							{
								$errors[] = 'Title must not exceed 250 characters. Details must not exceed 65000 characters.';
							}

							if ($department == '1')
							{
								$departmentn = 'Support';
							} elseif ($department == '2') {
								$departmentn = 'Billing';
							} elseif ($department == '3') {
								$departmentn = 'Other';
							}

							//check if exist
							$SQLCheckTicketExist = $odb -> prepare("SELECT * FROM `tickets` WHERE `senderid` = :senderid AND `title` = :title AND `details` = :details AND date < DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
							$SQLCheckTicketExist -> execute(array(':senderid' =>  $_SESSION['rID'], ':title' => $title, ':details' => $details));
							if($SQLCheckTicketExist -> rowCount() > 0)
							{
								$errors[] = 'You have already submitted this ticket within the last 30 minutes. Please check your open tickets.';
							}
							//check if exist end

							if (empty($errors))
							{
								$detailsToSTore = nl2br(htmlentities($details, ENT_QUOTES, 'UTF-8'));

								$SQL = $odb -> prepare("INSERT INTO `tickets` VALUES(NULL, :department, :senderid, :title, :details, UNIX_TIMESTAMP(), 1)");
								$SQL -> execute(array(':department' => $departmentn, ':senderid' => $_SESSION['rID'], ':title' => $title, ':details' => $detailsToSTore));
								echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have sent the ticket successfully.</div>';
								$SQLGetUserInfo = $odb -> prepare("SELECT `username`,`email` FROM `accounts` WHERE `id` = :id LIMIT 1");
								$SQLGetUserInfo -> execute(array(':id' => $_SESSION['rID']));
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
										Dear '.$eUsername.', <br><br> We have received your ticket and a staff member will respond to it as soon as possible.</b><br><br>
										In the mean time, please visit our <a href="https://crypticvpn.com/faq.php">FAQ</a> as it may contain the answer to your question. <br><br>
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
										<td style="font-family:\'Source Sans Pro\', sans-serif; font-size:13px; color:#aaaaaa; line-height:28px; font-weight:300;" class="text-center">&copy; Copyright &copy; 2017 CrypticVPN.com. All Rights Reserved.</td>
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
								$mail->Subject = 'New Support Request';
								$mail->msgHTML($mailcontent);
								$mail->send();
								//email admin

								$mailcontent = 'Hello, <br><br> there is a new ticket from '.$eUsername.'<br><br>Respond to it ASAP!';
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
								$mail->addAddress('admin@crypticvpn.com', 'cryptic');
								$mail->addAddress('blackphoex@ub3r.org', 'Blackphoenician');
								$mail->Subject = 'New Support Request';
								$mail->msgHTML($mailcontent);
								$mail->send();
								//email admin end
							}
							else
							{
								echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
								foreach($errors as $error)
								{
									echo '- '.$error.'<br />';
								}
								echo '</div>';
							}
						}
					?>
               <div class="tab-pane <?php if(empty($_GET['tab']) || $_GET['tab']=='createticket') echo 'active'; ?>" id="createticket">  
				<div class="bg-red">Before creating a ticket, please make sure to check out our Frequently Asked Questions page, as many answers can be found there. You can go to the FAQ page by clicking <a href="faq.php">here</a>. You can also join our <a href="https://discord.gg/uwjEeVQ">Discord</a> community groups to get assistance from other members.</div>     

				<!--<div class="alert-danger">NOTICE: If your client is FAILING to update, close it, and re-download it from our website and run it as admin. It will force the update. - Cryptic (Aug 18, 4:50PM EST)</div>   -->
				<p>

				<form method="post">
				  <div class="form-group">
					<label for="subject">Subject <span class="text-red">*</span></label>
					<input type="text" class="form-control" id="subject" name="subject" value="<?=isset($old_data['subject'])?$old_data['subject']:''?>" required>
				  </div>
				  <div class="form-group">
					<label for="department">Department <span class="text-red">*</span></label>
					<select class="form-control" name="department" id="department" required>
						<option value="">-- Select Department --</option>
						<option value="1">Support</option>
						<option value="2">Billing</option>
						<option value="3">Other</option>
					</select>
				  </div>  
				  <div class="form-group">
					<label for="message">Message <span class="text-red">*</span></label>
					<textarea class="form-control" id="message" name="details" required></textarea>
				  </div>                        
				  <button type="submit" name="sendTicket" value="1" class="btn btn-primary full-width">Submit</button>
				</form>    
                </div>
                    <div class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab']=='viewtickets') echo 'active'; ?>" id="viewtickets"> 
                        <div class="table-responsive">
                            <table class="table table-hover tickets">
                                <thead>
                                    <tr> <th>Department # </th> <th>Subject</th> <th>Status</th> <th>Manage</th> </tr> 
                                </thead>
                                <tbody>
									
									<?php
										$GetTickets = $odb -> prepare("SELECT * FROM `tickets` WHERE `senderid` = :sender ORDER BY `id` DESC LIMIT 100");
										$GetTickets -> execute(array(':sender' => $_SESSION['rID']));
										while ($getInfo = $GetTickets -> fetch(PDO::FETCH_ASSOC))
										{
											$id = $getInfo['id'];
											$department = $getInfo['department'];
											$getTitle = $getInfo['title'];
											if (strlen($getTitle) > 15) {
													substr($getTitle ,0,15).'...';
												}
											$title = $getTitle;
											switch($getInfo['status'])
											{
											case 1:
											$status = '<span class="badge btn-warning">Pending</span>';
											break;
											case 2:
											$status = '<span class="badge btn-success">Answered</span>';
											break;
											case 3:
											$status = '<span class="badge btn-danger">Closed</span>';
											break;
											}

											echo '
											<tr>
												<td>'.$department.'</td>
												<td>'.$title.' </td>
												<td>'.$status.'</td>
												<td><a class="btn btn-primary" href="viewticket.php?id='.$id.'">View</a></td>
											</tr>';
										}
									?>
                                    
                                </tbody>
                            </table>  
                        </div>  
                    </div>                    
                </div>                          
            </div>
        </div>
      </div>
    </div><!-- /.container -->

    <?php include('templates/footer.php'); ?>
	<?php include('templates/scriptjs.php'); ?>
</body>
</html>