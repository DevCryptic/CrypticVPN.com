<?php
ob_start();
require_once 'application/config.php';
require_once 'application/global.php';

function generateRandomString($length = 20) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if ($user -> IsLogged($odb))
{
	header('Location: dashboard.php');
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $site_title; ?> - Register</title> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./assets/favicon.ico">  
    <link href="./assets/css/font-style.css" rel="stylesheet"/>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="./assets/css/login.css" rel="stylesheet"/> 
    <link href="./assets/css/register.css" rel="stylesheet"/>
	<script src='https://www.google.com/recaptcha/api.js'></script>

</head>
<body class="bg_register">
	<main id="swapper">
		<section class="login register">
			<div class="container">
				<div class="row div_row">
					<div class="div_logo" align="center">
						<a href="index.php"><img src="assets/img/logo.png"></a>
					</div>

					<form action="#" method="POST" name="form-register">
						<?php 
							if (isset($_POST['sm_account']))
							{
								$captcha = $_POST['g-recaptcha-response'];
								$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$rprivatekey."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
								if($response.success==false || !($captcha))
								{
									echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have entered an invalid captcha code.</div>';
								} else {
									$username = $_POST['username'];
									$password = $_POST['passwd'];
									$repeat = $_POST['rpasswd'];
									$email = $_POST['email'];
									$errors = array();
									if (empty($username) || empty($password) || empty($repeat) || empty($email))
									{
										$errors[] = 'Please fill in all required fields.';
									}
									$checkUsername = $odb -> prepare("SELECT * FROM `accounts` WHERE `username`= :username");
									$checkUsername -> execute(array(':username' => $username));
									$countUsername = $checkUsername -> rowCount();
									if ($countUsername != 0)
									{
										$errors[] = 'The username you have entered is already in use.';
									}
									$checkEmail = $odb -> prepare("SELECT * FROM `accounts` WHERE `email`= :email");
									$checkEmail -> execute(array(':email' => $email));
									$countEmail = $checkEmail -> rowCount();
									if ($countEmail != 0)
									{
										$errors[] = 'The email you have entered is already in use.';
									}
										if (strlen($_POST['username']) < 4) {
										$errors[] = 'The username you have entered is too short.';
										}
										if (strlen($_POST['passwd']) < 4) {
										$errors[] = 'The username you have entered is too short.';
										}
										if (strlen($_POST['username']) > 15) {
										$errors[] = 'The username you have entered is too long.';
										}
									if (!filter_var($email, FILTER_VALIDATE_EMAIL))
									{
										$errors[] = 'You have entered an invalid e-mail address.';
									}
									if (!ctype_alnum($username))
									{
										$errors[] = 'The username you have entered is invalid.';
									}
									if ($password != $repeat)
									{
										$errors[] = 'The passwords you have entered does not match.';
									}
									if (!isset($_POST['cb_register']))
									{
										$errors[] = 'You have to agree with our terms of service to register.';
									}
									if (empty($errors))
									{
										if ($rvpnregconfirmation == 1) {
											$sha = hash("sha512", $password);
											$activation = generateRandomString();
											$insertUser = $odb -> prepare("INSERT INTO `accounts` VALUES(NULL, :username, :password, 0, :email, :activation, 0, 0, 0, 1, 0, 0, 0, 0, UNIX_TIMESTAMP())");
											$insertUser -> execute(array(':username' => $username, ':password' => MD5($password), ':email' => $email, ':activation' => $activation));

											$confirmationlink = 'https://'.$gsetting -> getSiteUrl($odb).'activate.php?u='.$username.'&confirm='.$activation.'';
											$content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
												<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:26px; color:#3d3d3d;" class="text-center">Hello '.$username.', please activate your account.</td>
												</tr>
												<tr><td height="30"></td></tr>
												<tr>
												<td class="text-center">
												<font style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px;">
												<a href="'.$confirmationlink.'" style="width:138px; border-radius:5px; background:#fff; color:#3d3d3d; font-size:15px; text-decoration:none; display:block; text-align:center; padding:8px 0;" class="center">ACTIVATE</a>
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
												Please note: In order to use your CrypticVPN account, you must click the activation button above to verify your account. If this is unavailable, please click the link below:
												</td>
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
												<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
												Please <a href="'.$confirmationlink.'" style="color:#36beec; text-decoration:underline;">click here</a> to validate your account.
												</td>
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
												<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
												If the link above doest not work, please copy and paste the following link in your browser:
												</td>
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
												<td class="text-center" style="word-wrap:break-word;">
												<a href="#" style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#003caa; line-height:25px; font-weight:300; word-wrap:break-word;"> <a href="'.$confirmationlink.'" target="_blank">'.$confirmationlink.'</a>
												</td>
												</tr>
												<tr><td height="35"></td></tr>
												<tr>
												<td align="right" style="font-family:\'Source Sans Pro\', sans-serif; font-size:14px; color:#a4a4a4; font-weight:300;" class="text-center">You are recieving this email because you are part of the CrypticVPN Mailing List.</td>
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
											$mail->addAddress($email, $username);
											$mail->Subject = 'CrypticVPN E-Mail Activation';
											$mail->msgHTML($content);
											$mail->send();

							  echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have successfully registered an account. Please check your email for activation instructions. Be sure to check the junk/spam folder.</div><meta http-equiv="refresh" content="10;url=login.php">'; 
										} else {
											$sha = hash("sha512", $password);
											$activation = generateRandomString();
											$insertUser = $odb -> prepare("INSERT INTO `accounts` VALUES(NULL, :username, :password, 0, :email, :activation, 1, 0, 0, 1, 0, 0, 0, 0, UNIX_TIMESTAMP())");
											$insertUser -> execute(array(':username' => $username, ':password' => MD5($password), ':email' => $email, ':activation' => $activation));
							  echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have successfully registered an account. Please check your email for activation instructions. Be sure to check the junk/spam folder.</div><meta http-equiv="refresh" content="10;url=login.php">'; 
							}

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
							}
						?>
						<div class="div_content col-xs-12 col-md-8">
							<p class="p_content">Username</p>
							<input type="text" name="username" id="tb_username" value="<?=isset($old_post['username'])?$old_post['username']:''?>" required />
						</div>
						<div class="div_content col-xs-12 col-md-8">
							<p class="p_content">Email</p>
							<input type="email" name="email" id="tb_email" value="<?=isset($old_post['email'])?$old_post['email']:''?>" required/>
						</div>
						<div class="div_content col-xs-12 col-md-8">
							<p class="p_content">Password</p>
							<input type="password" name="passwd" id="tb_password" required/>
						</div>
						<div class="div_content col-xs-12 col-md-8">
							<p class="p_content">Re-Enter Password</p>
							<input type="password" name="rpasswd" id="tb_repassword" required/>
						</div>
						<div class="div_content col-xs-6">
						<div class="g-recaptcha" data-sitekey="<?php echo $rpublickey; ?>"></div>
						</div>
						<div class="div_content col-xs-12 col-md-8">
							<label class="lb_register"><input type="checkbox" name="cb_register" id="cb_register" value="1" required/>I agree to the <a href="tos.php">Terms of Service</a>.</label>
						</div>
						<div class="div_content col-xs-12 col-md-8">
							<input type="submit" name="sm_account" id="sm_account" value="Create Account" />
						</div>
					</form>

					<div class="div_register">
						<div class="div_register_cont">
							<p class="p_register">Already a member? Click the button below to login</p>
							<a href="login.php"><input type="button" name="bt_register" id="bt_register" value="I'm already a Member" /></a>
						</div>
					</div>

					<?php include('templates/guest_footer.php'); ?>										
				</div>
			</div>
		</section>
	</main>	
    <script src="./assets/js/jquery.min.js"></script>
    <script src="./assets/js/bootstrap.min.js" type="text/javascript"></script>
    <script type="text/javascript">
    	var recaptchaCallback = function(response) {
	        //console.log(response);
	    };

    </script>
</body>
</html>
