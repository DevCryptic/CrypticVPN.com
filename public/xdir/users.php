<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)))
{
	header('Location: login.php');
	die();
}

if (isset($_GET['id'])) {
	if (is_numeric($_GET['id'])) {
		$uid = $_GET['id'];
		//Get required user info to edit
		$SQLGetInfo = $odb -> prepare("SELECT * FROM `accounts` WHERE `id` = :userid LIMIT 1");
		$SQLGetInfo -> execute(array(':userid' => $_GET['id']));
		$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
		$userid = $userInfo['id'];
		$username = $userInfo['username'];
		$vpnpass = $userInfo['rVPN'];
		$emailaddr = $userInfo['email'];
		$isactive = $userInfo['isactive'];
		$isbanned = $userInfo['isbanned'];
		$downtimenotification = $userInfo['receive_dtinfo'];
		$specialoffers = $userInfo['special_offers'];
		$portfwoption = $userInfo['pfoption'];
		$expire = $userInfo['expire'];
		$creation_date = $userInfo['acdate'];
		$isstaff = $userInfo['isstaff'];
	} else {
		header('Location: users.php');
		die();
	}
}
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

    <title><?php echo $site_title; ?> - User Management</title>

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
				if (isset($_POST['findUser']))
				{
					$username = $_POST['username'];
					$email = $_POST['email'];
					if (!(empty($username)) || !(empty($email))) {
						if (empty($username)) {
							if (!filter_var($email, FILTER_VALIDATE_EMAIL))
							{
								echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> Invalid e-mail address entered. </div>';
							} else {
								$checkIfExists = $odb -> prepare("SELECT * FROM `accounts` WHERE `email` = :email");
								$checkIfExists -> execute(array(':email' => $email));
								if($checkIfExists -> rowCount() == 0)
								{
									echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> No matching user found for the e-mail: '.$email.' </div>';
								} else {
									$SQLGetInfo = $odb -> prepare("SELECT id FROM `accounts` WHERE `email` = :email LIMIT 1");
									$SQLGetInfo -> execute(array(':email' => $email));
									$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
									$userid = $userInfo['id'];
									header('Location: users.php?id='.$userid.'');
									die();
								}
							}
						} else {
							if (!ctype_alnum($username))
							{
								echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> Invalid username entered. </div>';
							} else {
								$checkIfExists = $odb -> prepare("SELECT * FROM `accounts` WHERE `username` = :username");
								$checkIfExists -> execute(array(':username' => $username));
								if($checkIfExists -> rowCount() == 0)
								{
									echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> No matching user found for the e-mail: '.$email.' </div>';
								} else {
									$SQLGetInfo = $odb -> prepare("SELECT id FROM `accounts` WHERE `username` = :username LIMIT 1");
									$SQLGetInfo -> execute(array(':username' => $username));
									$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
									$userid = $userInfo['id'];
									header('Location: users.php?id='.$userid.'');
									die();
								}
							}
						}
					} else {
						echo '<div class="uk-alert uk-alert-warning" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> Please enter atleast one field to continue.. </div>';
					}
				}

				if (isset($_POST['updateUser'])) {
					$update = false;
					$errors = array();
					$length = $_POST['length'];
					$unit = $_POST['unit'];
					if (!empty($_POST['password']))
					{
						$SQL = $odb -> prepare("UPDATE `accounts` SET `password` = :password WHERE `id` = :id");
						$SQL -> execute(array(':password' => MD5($_POST['password']), ':id' => $uid));
						$update = true;
					}
					if ($username != $_POST['username'])
					{
						$SQL = $odb -> prepare("UPDATE `accounts` SET `username` = :username WHERE `id` = :id");
						$SQL -> execute(array(':username' => $_POST['username'], ':id' => $uid));
						$update = true;
						$username = $_POST['username'];
					}
					if ($emailaddr != $_POST['email'])
					{
						$SQL = $odb -> prepare("UPDATE `accounts` SET `email` = :email WHERE `id` = :id");
						$SQL -> execute(array(':email' => $_POST['email'], ':id' => $uid));
						$update = true;
						$emailaddr = $_POST['email'];
					}
					if (isset($_POST['isbanned']))
					{
						//Check if there an offline server and save action for it
						//$rvpn -> saveAction($odb, 'delete', $uid, 0, 0, 0);
						//delete VPN Account
						$rvpn -> deleteVPNAccount($rad, $uid, $encryptionKey);

						$mailcontent = 'Hello, <br><br> Your account was banned from our service due to violating our TOS.<br><br>Thank you!';
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
						$mail->addAddress($emailaddr, $username);
						$mail->Subject = 'Your Have Been Banned';
						$mail->msgHTML($mailcontent);
						$mail->send();
						$SQL = $odb -> prepare("UPDATE `accounts` SET `isbanned` = 1, `rVPN` = 0, `expire` = 0 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $uid));
						$update = true;
						$isbanned = 1;
					} else {
						$SQL = $odb -> prepare("UPDATE `accounts` SET `isbanned` = 0 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $uid));
						$update = true;
						$isbanned = 0;
					}
					if (isset($_POST['portfwoption']))
					{
						$SQL = $odb -> prepare("UPDATE `accounts` SET `pfoption` = 1 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $uid));
						$update = true;
						$portfwoption = 1;
					} else {
						$SQL = $odb -> prepare("UPDATE `accounts` SET `pfoption` = 0 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $uid));
						$update = true;
						$portfwoption = 0;
					}
					if (isset($_POST['isstaff']))
					{
						$SQL = $odb -> prepare("UPDATE `accounts` SET `isstaff` = 1 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $uid));
						$update = true;
						$isstaff = 1;
					} else {
						$SQL = $odb -> prepare("UPDATE `accounts` SET `isstaff` = 0 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $uid));
						$update = true;
						$isstaff = 0;
					}
					if (isset($_POST['isactive']))
					{
						$SQL = $odb -> prepare("UPDATE `accounts` SET `isactive` = 1 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $uid));
						$update = true;
						$isactive = 1;
					} else {
						$SQL = $odb -> prepare("UPDATE `accounts` SET `isactive` = 0 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $uid));
						$update = true;
						$isactive = 0;
					}
					if (isset($_POST['dtnotification']))
					{
						$SQL = $odb -> prepare("UPDATE `accounts` SET `receive_dtinfo` = 1 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $uid));
						$update = true;
						$downtimenotification = 1;
					} else {
						$SQL = $odb -> prepare("UPDATE `accounts` SET `receive_dtinfo` = 0 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $uid));
						$update = true;
						$downtimenotification = 0;
					}
					if (isset($_POST['specialoffers']))
					{
						$SQL = $odb -> prepare("UPDATE `accounts` SET `special_offers` = 1 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $uid));
						$update = true;
						$specialoffers = 1;
					} else {
						$SQL = $odb -> prepare("UPDATE `accounts` SET `special_offers` = 0 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $uid));
						$update = true;
						$specialoffers = 0;
					}
					if (isset($length))
					{
						$newExpire = strtotime('+'.$length.' '.$unit.'');
						$newvpnpass = $rvpn -> genPass();
						$SQL = $odb -> prepare("UPDATE `accounts` SET `expire` = :expire WHERE `id` = :id");
						$SQL -> execute(array(':expire' => $newExpire, ':id' => $uid));
						if ($vpnpass != 0) {
							//Check if there an offline server and save action for it
							$rvpn -> saveAction($odb, 'create', $uid, $newvpnpass, 0, 0);
							//Create VPN Account
							$rvpn -> createVPNAccount($rad, $uid, $newvpnpass, $encryptionKey);
						}
						$update = true;
						//$expire = $_POST['expire'];
					}

					if ($update == true)
					{
						echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have updated the user information!</div>';
					}
					else
					{
						echo '<div class="uk-alert uk-alert-info" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> No changes were made. </div>';
					}
				}
				if (isset($_POST['syncUser']))
				{
					$syncUser = $_POST['syncUser'];
					$syncserver = $_POST['syncserver'];
					if (empty($syncserver))
					{
						echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> Please select the server you want to synchronize user. </div>';
					} else {
						if ($syncserver == 'all')
						{
							$rvpn -> syncUserToAll($odb, $uid, $vpnpass, $encryptionKey);
							echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have synchronized the user to all servers successfully!</div>';
						} else {
							$checkIfExists = $odb -> prepare("SELECT * FROM `servers` WHERE `id` = :serverid AND NOT `status` = :status");
							$checkIfExists -> execute(array(':serverid' => $syncserver, ':status' => 'offline'));
							if($checkIfExists -> rowCount() == 0)
							{
								echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> Please select a server from list. </div>';
							} else {
								$rvpn -> syncUser($odb, $syncserver, $uid, $vpnpass, $encryptionKey);
								echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have synchronized the user to server successfully!</div>';
							}
						}
					}
				}
			?>
			<div class="md-card">
			<form method="post">
                <div class="md-card-content">
                    <h3 class="heading_a">Find users</h3>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-2">
                            <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
                                        <label>By username</label>
                                        <input type="text" name="username" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-medium-1-2">
							<div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
                                        <label>By e-mail</label>
                                        <input type="text" name="email" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid">
						<div class="uk-width-1-1">
							<button type="submit" name="findUser" class="md-btn md-btn-primary">Find</button>
						</div>
					</div>
					</form>
                </div>
            </div>

			<?php if (isset($_GET['id'])) { ?>
				<div class="md-card">
				<form method="post">
					<div class="md-card-content">
						<h3 class="heading_a">Edit User</h3>
						<div class="uk-grid" data-uk-grid-margin>
							<div class="uk-width-medium-1-2">
								<div class="uk-form-row">
									<div class="uk-grid">
										<div class="uk-width-medium-1-1">
											<h3 class="heading_a">Username</h3>
											<input type="text" name="username" value="<?php echo $username; ?>" class="md-input label-fixed" />
										</div>
									</div>
								</div>
								<div class="uk-form-row">
									<div class="uk-grid">
										<div class="uk-width-medium-1-1">
											<h3 class="heading_a">Password</h3>
											<input type="password" class="md-input" name="password" placeholder="Leave blank if you don't want to change" />
											<a href="" class="uk-form-password-toggle" data-uk-form-password>show</a>
										</div>
									</div>
								</div>
								<div class="uk-form-row">
									<div class="uk-grid">
										<div class="uk-width-medium-1-1">
											<h3 class="heading_a">E-Mail Address</h3>
											<input type="text" name="email" value="<?php echo $emailaddr; ?>" class="md-input label-fixed" />
										</div>
									</div>
								</div>
								<div class="uk-form-row">
									<div class="uk-grid">
										<div class="uk-width-medium-1-1">
											<h3 class="heading_a">VPN Password</h3>
											<input type="password" class="md-input" disabled value="<?php if ($vpnpass == 0) { echo '- none -'; } else { echo $vpnpass; } ?>" placeholder="Leave blank if you don't want to change" />
											<a href="" class="uk-form-password-toggle" data-uk-form-password>show</a>
										</div>
									</div>
								</div>
							</div>
							<div class="uk-width-medium-1-2">
								<div class="uk-form-row">
									<div class="uk-form-row">
										<div class="uk-grid">
											<div class="uk-width-medium-1-2">
												<label>Length</label>
												<input type="text" name="length" class="md-input label-fixed" />
											</div>
											<div class="uk-width-medium-1-2">
												<select id="select_demo_1" name="unit" data-md-selectize>
													<option value="">Select Unit..</option>
														<option value="Days">Day(s)</option>
														<option value="Weeks">Week(s)</option>
														<option value="Months">Month(s)</option>
														<option value="Years">Year(s)</option>
												</select>
											</div>
										</div>
									</div>
									<div class="uk-grid">
										<div class="uk-width-medium-1-2">
											<input type="checkbox" name="isbanned" data-switchery <?php if ($isbanned == 1) { echo 'checked'; } ?> value="1" id="switch_demo_1" />
											<label for="switch_demo_1" class="inline-label">Account Suspended</label>
										</div>
										<div class="uk-width-medium-1-2">
											<input type="checkbox" name="portfwoption" data-switchery <?php if ($portfwoption == 1) { echo 'checked'; } ?> id="switch_demo_2" />
											<label for="switch_demo_2" class="inline-label">Port Forwarding</label>
										</div>
									</div>
									<div class="uk-grid">
										<div class="uk-width-medium-1-2">
											<input type="checkbox" name="isactive" data-switchery <?php if ($isactive == 1) { echo 'checked'; } ?> id="switch_demo_3" />
											<label for="switch_demo_3" class="inline-label">E-Mail Confirmed</label>
										</div>
										<div class="uk-width-medium-1-2">
											<input type="checkbox" name="dtnotification" data-switchery <?php if ($downtimenotification == 1) { echo 'checked'; } ?> value="1" id="switch_demo_4" />
											<label for="switch_demo_4" class="inline-label">Downtime Notifications</label>
										</div>
									</div>
									<div class="uk-grid">
										<div class="uk-width-medium-1-2">
											<input type="checkbox" name="specialoffers" data-switchery <?php if ($specialoffers == 1) { echo 'checked'; } ?> id="switch_demo_5" />
											<label for="switch_demo_5" class="inline-label">Special Offers</label>
										</div>
										<div class="uk-width-medium-1-2">
											<input type="checkbox" name="isstaff" data-switchery <?php if ($isstaff == 1) { echo 'checked'; } ?> id="switch_demo_6" />
											<label for="switch_demo_6" class="inline-label">Support Staff</label>
										</div>
									</div>
									<hr>
									<p> <strong>Account Creation Date:</strong> <?php echo date("m-d-Y h:i:s A", $creation_date); ?>
									<br>
										<strong>Package Expire:</strong> <?php if($expire != 0) { echo date("m-d-Y h:i:s A", $expire); } else { echo '-'; } ?>
									<?php if ($vpnpass != '0' && $expire > time()) { ?>
									<br>
									<div class="uk-form-row">
										 <select id="select_demo_1" name="syncserver" data-md-selectize>
											<option value="">Select Server</option>
											<option value="all">Synchronize to All Servers</option>
											<?php
												$ServerList = $odb -> prepare("SELECT * FROM `servers` WHERE NOT `status` = :status ORDER BY `id` DESC LIMIT 100");
												$ServerList -> execute(array(':status' => 'offline'));
												while ($getInfo = $ServerList -> fetch(PDO::FETCH_ASSOC))
												{
													$serverid = $getInfo['id'];
													$servername = $getInfo['name'];
													$country = $getInfo['country'];
													$city = $getInfo['city'];

													echo '<option value="'.$serverid.'">'.$country.' - '.$city.' ('.$servername.')</option>';
												}
											?>
										</select>
									</div>
										<button type="submit" name="syncUser" class="md-btn md-btn-primary">Sync User</button>
									<?php } ?>
									</p>
								</div>
							</div>
						</div>
						<div class="uk-grid">
							<div class="uk-width-1-1">
								<button type="submit" name="updateUser" class="md-btn md-btn-primary">Update</button>
							</div>
						</div>
						</form>
					</div>
				</div>
				<?php } else { ?>
				<div class="md-card uk-margin-medium-bottom">
                <div class="md-card-content">
                    <div class="uk-overflow-container">
                        <table class="uk-table">
                            <thead>
                            <tr>
                                <th>Username</th>
																<th>E-Mail</th>
																<th>Status</th>
																<th>Manage</th>
																<th>Expiration Date</th>
                            </tr>
                            </thead>
                            <tbody>
                           <?php
															$GetUsers = $odb -> prepare("SELECT * FROM `accounts` ORDER BY `id` DESC LIMIT 100");
															$GetUsers -> execute();
															while ($getInfo = $GetUsers -> fetch(PDO::FETCH_ASSOC))
															{
																$id = $getInfo['id'];
																$username = $getInfo['username'];
																$email = $getInfo['email'];
																$expire = $getInfo['expire'];

																switch($getInfo['isbanned'])
																{
																	case 1:
																	$status = '<span class="uk-badge uk-badge-danger">Banned</span>';
																	break;
																	case 0:
																	$status = '<span class="uk-badge uk-badge-success">Active</span>';
																	break;
																	}

															echo '
															<tr>
															<td>'.$username.'</td>
															<td>'.$email.' </td>
															<td>'.$status.'</td>
															<td><a class="md-btn md-btn-primary" href="users.php?id='.$id.'">View</a></td>
															<td>'; if($expire != 0) { echo date("m-d-Y h:i:s A", $expire); } else { echo 'N/A'; } '</td>
															</tr>'; 
															}
														?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
				<?php } ?>

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
