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


//Get email
$SQLGetUserInfoX = $odb -> prepare("SELECT `id`, `email` FROM `accounts` WHERE `id` = :id LIMIT 1");
$SQLGetUserInfoX -> execute(array(':id' => $_SESSION['rID']));
$userInfoX = $SQLGetUserInfoX -> fetch(PDO::FETCH_ASSOC);
$userMailX = $userInfoX['email'];
//Sell Now stuff
$email  = $userMailX; //THIS COMES FROM SESSION

if(isset($_POST['purchase']) && isset($_POST['paymentMethod']) && isset($_POST['productId'])) {

    $paymentMethod = filter_var($_POST['paymentMethod'], FILTER_SANITIZE_STRING);
    $productId = filter_var($_POST['productId'], FILTER_SANITIZE_STRING);

    if(isset($_POST['coupon']))
    	$coupon = filter_var($_POST['coupon'], FILTER_SANITIZE_STRING);
   	else
   		$coupon = '';

    $postData = [
        'product_id' => $productId, //here so it's hidden and no one can directly go to it
        'purchase' => "1",
        'method' => $paymentMethod,
        'email' => $email,
        'coupon_code' => '',
        'quantity' => '1',
        'applicationId' => 'CRYPTICVPN1597863513',
        'applicationSecret' => 'f0qX9HbpZiKcsbTDcqwCwiJiyoD0EHWwfh9rQxvs0uxLmj5t2GOmNj0eejgA',
        'customerIP' => $_SERVER['REMOTE_ADDR']
    ];
    
    $ch = curl_init('https://rocketr.net/product.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    $response = curl_exec($ch);
    curl_close($ch);

    if($response !== false){
        
        die($response);

    } else {
    	die(json_encode(['error' => 'Error Creating Order']));
    }
}
//Sell Now End

?>
<!DOCTYPE html>
<html lang="en">
<title><?php echo $site_title; ?> - Dashboard</title>   
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
                    <li class="test-class <?php if(empty($_GET['tab']) || $_GET['tab']=='dashboard') echo 'active'; ?>"><a class="deco-none misc-class" href="#dashboard"> Dashboard</a></li>
                    <li class="test-class <?php if(isset($_GET['tab']) && $_GET['tab']=='purchase') echo 'active'; ?>"><a href="#purchase">Purchase</a></li>
					<?php
					$checkDedicated = $odb -> prepare("SELECT * FROM `dpackages`");
					  $checkDedicated -> execute();
					  if($checkDedicated -> rowCount() > 0)
					  {
					?>
					<li class="test-class <?php if(isset($_GET['tab']) && $_GET['tab']=='orderdedi') echo 'active'; ?>"><a href="#orderdedi">Private VPN</a></li>
					<?php
					  }
					?>
                    <li class="test-class <?php if(isset($_GET['tab']) && $_GET['tab']=='download') echo 'active'; ?>"><a href="#download">Downloads</a></li>

                </ul>
                <div class="tab-content responsive">
				<?php
				if (isset($_GET['error']) && $_GET['error'] == '1') {
					echo '<div class="bg-red">You have to purchase a package to be able to access vpn functions. </div>';
				} 
				?>
                  <div class="tab-pane <?php if(empty($_GET['tab']) || $_GET['tab']=='dashboard') echo 'active'; ?>" id="dashboard">       
					<?php
						if (isset($_POST['redeemCode']))
						{
							if ($user->hasVPN($odb)) {
								echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have an active vpn account. You can\'t use any gift codes.</div>';
							} else {
								$raw = $_POST['code'];
								$code = preg_replace('/\s+/', '', $raw); 

								if (empty($code)) {
									echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Please enter the gift code.</div>';
								} else {
									$checkIfExists = $odb -> prepare("SELECT * FROM `giftcodes` WHERE `code` = :code AND `usedby` = :used");
									$checkIfExists -> execute(array(':code' => $code, ':used' => '0'));
									if($checkIfExists -> rowCount() == 0)
									{
										echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Invalid or already used code. </div>';
									} else {
										$SQLGetUserInfo = $odb -> prepare("SELECT `id`,`username`,`email`,`expire`,`rVPN`  FROM `accounts` WHERE `id` = :id LIMIT 1");
										$SQLGetUserInfo -> execute(array(':id' => $_SESSION['rID']));
										$userInfo = $SQLGetUserInfo -> fetch(PDO::FETCH_ASSOC);
										$userMail = $userInfo['email'];
										$userName = $userInfo['username'];
										$user_id = $userInfo['id'];

										$SQLGetInfo = $odb -> prepare("SELECT * FROM `giftcodes` WHERE `code` = :code AND `usedby` = 0 LIMIT 1");
										$SQLGetInfo -> execute(array(':code' => $code));
										$packageInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
										$length = $packageInfo['length'];
										$unit = $packageInfo['unit'];
										//email fix
										if (($length == '1') && ($unit == 'Days'))
											$displayUnit = "Day";
										else $displayUnit = $unit;
										
										if (($length == '1') && ($unit == 'Months'))
											$displayUnit = "Month";
										
										$newExpire = strtotime("+{$length} {$unit}");

										$update1 = $odb -> prepare("UPDATE `accounts` SET `expire` = :expire WHERE `id` = :id");
										$update1 -> execute(array(':expire' => $newExpire, ':id' => $_SESSION['rID']));
										$update2 = $odb -> prepare("UPDATE `accounts` SET `pfoption` = 1 WHERE `id` = :id");
										$update2 -> execute(array(':id' => $_SESSION['rID']));

										$update3 = $odb -> prepare("UPDATE `giftcodes` SET `usedby` = :usedby WHERE `code` = :code");
										$update3 -> execute(array(':code' => $code, ':usedby' => $_SESSION['rUsername']));

										$vpnPass = $rvpn -> genPass();
										//$update4 = $odb -> prepare("UPDATE `accounts` SET `rVPN` = :rvpn WHERE `id` = :id");
										//$update4 -> execute(array(':rvpn' => $vpnPass, ':id' => $_SESSION['rID']));

										//Check if there an offline server and save action for it
										//$rvpn -> saveAction($odb, 'create', $user_id, $vpnPass, 0, 0);
										//Create VPN Account
										$rvpn -> createVPNAccount($rad, $user_id, $vpnPass, $encryptionKey);

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
			<strong>VPN Subscription Length:</strong> '.$length.' '.$unit.'<br>
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
												$mail->SMTPAuth = $smtpauthstat;
												$mail->Username = $rsuser;
												$mail->Password = $rspass;
										}
										$mail->setFrom($from, $fromname);
										$mail->addReplyTo($from, $fromname);
										$mail->addAddress($userMail, $userName);
										$mail->Subject = 'VPN Gift Code Redeemed!';
										$mail->msgHTML($mailcontent);
										$mail->send();

										echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have successfully redeemed the gift code. Your subscription has been activated. </div>';

									}
								}
							}
						}
					?>
                        <div class="dash-content">
                            <div class="col-sm-5">
                                <div class="item-dash">
                                    <div class="left-item">
                                        <div class="item-icon"><span class="glyphicon glyphicon-globe"></span></div>
                                    </div>
                                    <div class="right-item">
                                        <div class="item-title">IP Address</div>
                                        <div class="item-value"><? echo getRealIpAddr(); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="item-dash">
                                    <div class="left-item">
                                        <div class="item-icon"><span class="glyphicon glyphicon-calendar"></span></div>
                                    </div>
                                    <div class="right-item">
                                        <div class="item-title">Account Expires</div>
                                        <div class="item-value">
                                        <?php echo $userstat -> getExpiration($odb); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="item-dash">
                                   <div class="left-item">
                                        <div class="item-icon"><span class="glyphicon glyphicon-user"></div>
                                    </div>
                                    <div class="right-item">
                                        <div class="item-title">VPN Username</div>
                                        <div class="item-value">
                                        <?php if ($user -> hasVPN($odb)) { echo $_SESSION['rID']; } else { echo 'N/A'; } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="item-dash">
                                    <div class="left-item">
                                        <div class="item-icon"><span class="glyphicon glyphicon-certificate"></span></div>
                                    </div>
                                    <div class="right-item">
                                        <div class="item-title">VPN Password</div>
                                        <div class="item-value">
                                        <?php echo $userstat -> getVPNPass($rad); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>  
                        <?php
                        // echo "<pre>freeradius radcheck: ";
                        //  print_r($customers);
                        //  echo "</pre>";
                        ?>   
                    </div>
                    <div class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab']=='purchase') echo 'active'; ?>" id="purchase">
                    <div id="purchaseContainer">
							<span style="display:none; color: red;" id="purchaseContainerError">

							</span>
						<form method="post">
						  <div class="form-group">
							<label for="coupon">Coupon Code</label>
							<input type="text" class="form-control" id="coupon" name="code" required>
						  </div>                      
						  <button type="submit" name="redeemCode" value="1" class="btn btn-primary full-width">Redeem Coupon Code</button>
						</form>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr> <th>Package</th> <th>Access Duration</th> <th>Price</th> <th>Purchase</th> </tr> 
                                </thead>
                                <tbody>
								<?php
								$GetPackages = $odb -> prepare("SELECT * FROM `packages` ORDER BY `price` ASC");
								$GetPackages -> execute();
								while ($getInfo = $GetPackages -> fetch(PDO::FETCH_ASSOC))
								{
									$id = $getInfo['id'];
									$name = $getInfo['name'];
									$length = $getInfo['length'];
									$unit = $getInfo['unit'];
									$price = $getInfo['price'];
									$sellNowProductID = $getInfo['sellNowProductID'];
									
									if ($getInfo['unit'] == 'Months'){
									$unit = 'Month(s)';
									} elseif ($getInfo['unit'] == 'Years'){
									$unit = 'Year(s)';
									} elseif ($getInfo['unit'] == 'Days'){
									$unit = 'Day(s)';
									} elseif ($getInfo['unit'] == 'Weeks'){
									$unit = 'Week(s)';
									}
									
									echo '
									<tr>
										<td>'.$name.'</td>
										<td>'.$length.' '.$unit.' </td>
										<td>$'.$price.'</td>
									
										<td>											
											<button name="purchase" class="btn btn-primary uppercase" onclick="purchase(\''.$sellNowProductID.'\', \'btc\');">BTC</button>
											<button name="purchase" class="btn btn-primary uppercase" onclick="purchase(\''.$sellNowProductID.'\', \'bcc\');">BCH</button>
											<button name="purchase" class="btn btn-primary uppercase" onclick="purchase(\''.$sellNowProductID.'\', \'eth\');">ETH</button>
											<button name="purchase" class="btn btn-primary uppercase" onclick="purchase(\''.$sellNowProductID.'\', \'ltc\');">LTC</button>
											<button name="purchase" class="btn btn-primary uppercase" onclick="purchase(\''.$sellNowProductID.'\', \'pm\');">Perfect Money</button>&nbsp';
											/*if ($id == 6$id != 2 /*&& $id != 3){ 
												echo '<button name="purchase" class="btn btn-primary uppercase" onclick="purchase(\''.$sellNowProductID.'\', \'paypal\');">PayPal</button>
';
											}*/
											if ($id != 2){ 
												echo '<button name="purchase" class="btn btn-primary uppercase" onclick="purchase(\''.$sellNowProductID.'\', \'paypal\');">PayPal</button>
												';
											}
										echo '</td>';
								}
								?>
                                </tbody>
                            </table>  
                        </div>           
                    </div>
                    <font color="red">If your account is not automatically activated after purchasing with <u>PayPal</u>, open a support ticket. DO NOT open a chargeback/dispute.</font>
					</div>
                    <div id="purchaseCompletedContainer" style="display:none">
						</div>

                    <div class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab']=='download') echo 'active'; ?>" id="download">                         
                        <div class="table-responsive">

                                <? if (!($user->hasVPN($odb))) {
									echo "You must have an active subscription before you can access this page.";
								}
								else
								{
								?>
								<table class="table table-hover tickets">

								<thead>
                                    <tr> <th>File </th> <th>Download</th></tr> 
                                </thead>
                                <tbody>                                    
                                    <tr>                                        
                                        <td>Cryptic VPN Client (Windows Only)</td>
                                        <td><a href="http://downloads.crypticvpn.com/CrypticVPN%20Installer.exe" class="btn btn-primary btn-green"><span class="glyphicon glyphicon-download-alt"></span> Download</a></td>                              
                                    </tr> 
                                    <tr>                                        
                                        <td>OpenVPN Config Files</td>
                                        <td><a href="http://downloads.crypticvpn.com/configs.zip" class="btn btn-primary btn-green"><span class="glyphicon glyphicon-download-alt"></span> Download</a></td>                              
                                    </tr> 
                                    <tr>                                        
                                        <td>Port Listener Tool (Windows Only)</td>
                                        <td><a href="http://downloads.crypticvpn.com/listen.exe" class="btn btn-primary btn-green"><span class="glyphicon glyphicon-download-alt"></span> Download</a></td>                              
                                    </tr> 
                                </tbody>
                                <?}?>
                            </table>  
                        </div>  
                    </div>  
                
                </div>                          
            </div>
        </div>
      </div>
    </div><!-- /.container -->
    	<script>function purchase(productId, paymentMethod) {
		let coupon = $('#coupon').val();
		$('#purchaseContainer').hide();
		$('#purchaseContainerError').text('').hide();

		$.post('',
            {   purchase: "1", 
                paymentMethod: paymentMethod,
                productId: productId,
                coupon: coupon
            }
        ).done(function(data) {
            json = JSON.parse(data);
            console.log(json)
            
            if(json.error){
            	$('#purchaseContainer').show();
            	$('#purchaseContainerError').text(json.error).show();
            	return;
            }else{
            	
                if(json.urlToRedirect){
					$('#purchaseCompletedContainer').html('Redirecting you to paypal').show();
					window.location.href = json.urlToRedirect;
                }else if(json.form) { 
                    $('#purchaseCompletedContainer').html(json.form).show();
                }else {
                    let html = 'To complete the purchase, please send <code>'+ json.amount +'</code> '+paymentMethod.toUpperCase()+' to <code>'+ json.address +'</code>' + '<br> Your subscription will be activated after 3 confirmations.' ;  
                	$('#purchaseCompletedContainer').html(html).show();
                }
            }
        });
	}</script>


    <?php include('templates/footer.php'); ?>
	<?php include('templates/scriptjs.php'); ?>
</body>
</html>