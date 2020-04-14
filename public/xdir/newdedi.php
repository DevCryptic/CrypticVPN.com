<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)))
{
	header('Location: login.php');
	die();
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

    <title><?php echo $site_title; ?> - Add New Dedicated</title>

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
			<?php
				if (isset($_POST['addServer'])) {
					$ipaddr = $_POST['ipaddr'];
					$name = $_POST['name'];
					$owner = $_POST['owner'];
					$spots = $_POST['spots'];
					$configdl = $_POST['configdl'];
					$profiledl = $_POST['profiledl'];
					$nextpayment = $_POST['nextpayment'];
					$username = $_POST['username'];
					$password = $_POST['password'];
					$dtreason = $_POST['dtreason'];
					$package = $_POST['packageid'];
					$errors = array();

						if (empty($ipaddr) || empty($name) || empty($owner) || empty($price) || empty($spots) || empty($username) || empty($password) || empty($dtreason) || empty($nextpayment) || empty($configdl) || empty($profiledl))
						{
							$errors[] = 'Please fill in all fields.';
						}

						if (!filter_var($ipaddr, FILTER_VALIDATE_IP))
						{
							$errors[] = 'Please enter a valid ip address.';
						}

						if (is_numeric($owner)) {
							$checkIfExists = $odb -> prepare("SELECT * FROM `accounts` WHERE `id` = :id");
							$checkIfExists -> execute(array(':id' => $owner));
							if($checkIfExists -> rowCount() == 0)
							{
								$errors[] = 'The id you have entered is not belongs to any customer. Please try again!';
							}
						} else {
							$checkIfExists = $odb -> prepare("SELECT * FROM `accounts` WHERE `username` = :username");
							$checkIfExists -> execute(array(':username' => $owner));
							if($checkIfExists -> rowCount() == 0)
							{
								$errors[] = 'The username you have entered is not belongs to any customer. Please try again!';
							} else {
								$SQLGetInfo = $odb -> prepare("SELECT id FROM `accounts` WHERE `username` = :username LIMIT 1");
								$SQLGetInfo -> execute(array(':username' => $username));
								$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
								$owner = $userInfo['id'];
							}
						}

						if(empty($errors)) {
							$nextpaymentunix = strtotime($nextpayment);
							$encryptedPass = encryptData($password, $encryptionKey);
							$insertServer = $odb -> prepare("INSERT INTO `dedicateds` VALUES (NULL, :owner, :name, :host, :username, :password, UNIX_TIMESTAMP(), :nextpayment, :spots, 1, :config, :profile, :dtreason, 0, 0, :package)");
							$insertServer -> execute(array(':owner' => $owner, ':name' => $name,':host' => $ipaddr, ':username' => $username, ':password' => encryptData($password, $encryptionKey), ':nextpayment' => $nextpaymentunix, ':spots' => $spots, ':config' => $configdl, ':profile' => $profiledl, ':dtreason' => $dtreason, ':package' => $package));
							//Add owner to the server
							$SQLGetInfo = $odb -> prepare("SELECT rVPN FROM `accounts` WHERE `id` = :id LIMIT 1");
							$SQLGetInfo -> execute(array(':id' => $owner));
							$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
							$vpnpass = $userInfo['rVPN'];
							if ($vpnpass == '0')
							{
								$genPass = $rvpn -> genPass();
								$update3 = $odb -> prepare("UPDATE `accounts` SET `rVPN` = :rvpn WHERE `id` = :id");
								$update3 -> execute(array(':rvpn' => $genPass, ':id' => $owner));
								$rvpn -> addDediOwner($odb, $ipaddr, $username, $encryptedPass, $owner, $encryptionKey);
							} else {
								$rvpn -> addDediOwner($odb, $ipaddr, $username, $encryptedPass, $owner, $encryptionKey);
							}

							echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have added a new vpn server successfully!</div>';
						} else {
							echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a>';
							foreach($errors as $error)
							{
								echo '- '.$error.'<br />';
							}
							echo '</div>';
						}
				}
			?>
			<div class="md-card">
			<form method="post">
                <div class="md-card-content">
                    <h3 class="heading_a">Add New Dedicated VPN Server</h3>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-2">
                            <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
                                        <label>Server IP Address</label>
                                        <input type="text" name="ipaddr" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
                                        <label>Server / Package Name</label>
                                        <input type="text" name="name" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
														<div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
																			<label for="uk_dp_1">Next Payment Date</label>
																			<input class="md-input label-fixed" name="nextpayment" type="text" id="uk_dp_1" data-uk-datepicker="{format:'DD-MM-YYYY'}">
                                    </div>
                                </div>
                            </div>
														<div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-3">
                                        <label>Alt VPN Spots</label>
                                        <input type="text" name="spots" class="md-input label-fixed" />
                                    </div>
																		<div class="uk-width-medium-1-3">
                                        <label>Package ID</label>
                                        <input type="text" name="packageid" class="md-input label-fixed" />
                                    </div>
																		<div class="uk-width-medium-1-3">
                                        <label>Server Owner</label>
                                        <input type="text" name="owner" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
														<div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
																			<label for="uk_dp_1">Configuration Download</label>
																			<input type="text" name="configdl" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
														<div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
																			<label for="uk_dp_1">OpenVPN Profile File</label>
																			<input type="text" name="profiledl" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-medium-1-2">
                           <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
																			<h3 class="heading_a">Username</h3>
                                        <input type="text" name="username" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
                                        <h3 class="heading_a">Password</h3>
																				<input type="password" class="md-input label-fixed" name="password" />
																				<a href="" class="uk-form-password-toggle" data-uk-form-password>show</a>
                                    </div>
                                </div>
                            </div>
														<div class="uk-form-row">
                                <label>Downtime Reason</label>
                                <textarea cols="30" rows="4" class="md-input label-fixed" name="dtreason">-</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid">
						<div class="uk-width-1-1">
							<button type="submit" name="addServer" class="md-btn md-btn-primary">Add Server</button>
						</div>
					</div>
					</form>
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
