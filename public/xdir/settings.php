<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)))
{
	header('Location: login.php');
	die();
}

$SQLGetSettings = $odb -> prepare("SELECT * FROM `settings` WHERE `id` = 1 LIMIT 1");
$SQLGetSettings -> execute();
$getInfo = $SQLGetSettings -> fetch(PDO::FETCH_ASSOC);
$siteurl = $getInfo['siteurl'];
$sitetitle = $getInfo['sitetitle'];
$sitemail = $getInfo['sitemail'];
$paypal = $getInfo['paypal'];
$cpsecret = $getInfo['cpsecret'];
$cpmerchant = $getInfo['cpmerchant'];
$vpndownload = $getInfo['vpndownload'];
$tapdownload = $getInfo['tapdownload'];
$configdownload = $getInfo['configdownload'];
$vpnnotes = $getInfo['vpnnotes'];
$require_confirmation = $getInfo['require_confirmation'];
$mailingtype = $getInfo['mailingtype'];
$smtpport = $getInfo['smtpport'];
$smtpuser = $getInfo['smtpuser'];
$smtppass = $getInfo['smtppass'];
$smtphost = $getInfo['smtphost'];

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

    <title><?php echo $site_title; ?> - General Settings</title>

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
				if (isset($_POST['updateGeneral']))
				{
					$update = false;
					$errors = array();

					if ($siteurl != $_POST['siteurl'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `siteurl` = :siteurl WHERE `id` = 1");
						$SQL -> execute(array(':siteurl' => $_POST['siteurl']));
						$update = true;
						$siteurl = $_POST['siteurl'];
					}
					if ($sitetitle != $_POST['sitetitle'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `sitetitle` = :sitetitle WHERE `id` = 1");
						$SQL -> execute(array(':sitetitle' => $_POST['sitetitle']));
						$update = true;
						$sitetitle = $_POST['sitetitle'];
					}
					if ($sitemail != $_POST['sitemail'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `sitemail` = :sitemail WHERE `id` = 1");
						$SQL -> execute(array(':sitemail' => $_POST['sitemail']));
						$update = true;
						$sitemail = $_POST['sitemail'];
					}
					if ($vpndownload != $_POST['vpndownload'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `vpndownload` = :vpndownload WHERE `id` = 1");
						$SQL -> execute(array(':vpndownload' => $_POST['vpndownload']));
						$update = true;
						$vpndownload = $_POST['vpndownload'];
					}
					if ($configdownload != $_POST['configdownload'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `configdownload` = :configdownload WHERE `id` = 1");
						$SQL -> execute(array(':configdownload' => $_POST['configdownload']));
						$update = true;
						$configdownload = $_POST['configdownload'];
					}
					if ($tapdownload != $_POST['tapdownload'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `tapdownload` = :tapdownload WHERE `id` = 1");
						$SQL -> execute(array(':tapdownload' => $_POST['tapdownload']));
						$update = true;
						$tapdownload = $_POST['tapdownload'];
					}
					if ($vpnnotes != $_POST['vpnnotes'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `vpnnotes` = :vpnnotes WHERE `id` = 1");
						$SQL -> execute(array(':vpnnotes' => $_POST['vpnnotes']));
						$update = true;
						$vpnnotes = $_POST['vpnnotes'];
					}
					if ($mailingtype != $_POST['mailingtype'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `mailingtype` = :mailingtype WHERE `id` = 1");
						$SQL -> execute(array(':mailingtype' => $_POST['mailingtype']));
						$update = true;
						$mailingtype = $_POST['mailingtype'];
					}
					if ($smtpport != $_POST['smtpport'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `smtpport` = :smtpport WHERE `id` = 1");
						$SQL -> execute(array(':smtpport' => $_POST['smtpport']));
						$update = true;
						$smtpport = $_POST['smtpport'];
					}
					if ($smtphost != $_POST['smtphost'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `smtphost` = :smtphost WHERE `id` = 1");
						$SQL -> execute(array(':smtphost' => $_POST['smtphost']));
						$update = true;
						$smtphost = $_POST['smtphost'];
					}
					if ($smtpuser != $_POST['smtpuser'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `smtpuser` = :smtpuser WHERE `id` = 1");
						$SQL -> execute(array(':smtpuser' => $_POST['smtpuser']));
						$update = true;
						$smtpuser = $_POST['smtpuser'];
					}
					if ($smtppass != $_POST['smtppass'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `smtppass` = :smtppass WHERE `id` = 1");
						$SQL -> execute(array(':smtppass' => $_POST['smtppass']));
						$update = true;
						$smtppass = $_POST['smtppass'];
					}
					if ($require_confirmation == 1 && !isset($_POST['require_confirmation'])) {
						$SQL = $odb -> prepare("UPDATE `settings` SET `require_confirmation` = 0 WHERE `id` = 1");
						$SQL -> execute();
						$require_confirmation = 0;
						$update = true;
					}
					if ($require_confirmation == 0 && isset($_POST['require_confirmation'])) {
						$SQL = $odb -> prepare("UPDATE `settings` SET `require_confirmation` = 1 WHERE `id` = 1");
						$SQL -> execute();
						$require_confirmation = 1;
						$update = true;
					}
					if ($update == true)
						{
							echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have updated general settings successfully!</div>';
						}
						else
						{
							echo '<div class="uk-alert uk-alert-info" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> No changes made.</div>';
						}
						if (!empty($errors))
						{
							echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a>';
							foreach($errors as $error)
							{
								echo '- '.$error.'<br />';
							}
							echo '</div>';
						}
				}
				if (isset($_POST['updatePayment']))
				{
					$update = false;
					$errors = array();

					if ($paypal != $_POST['paypal'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `paypal` = :ppmail WHERE `id` = 1");
						$SQL -> execute(array(':ppmail' => $_POST['paypal']));
						$update = true;
						$paypal = $_POST['paypal'];
					}
					if ($cpmerchant != $_POST['cpmerchant'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `cpmerchant` = :cpmerchant WHERE `id` = 1");
						$SQL -> execute(array(':cpmerchant' => $_POST['cpmerchant']));
						$update = true;
						$cpmerchant = $_POST['cpmerchant'];
					}
					if ($cpsecret != $_POST['cpsecret'])
					{
						$SQL = $odb -> prepare("UPDATE `settings` SET `cpsecret` = :cpipnsecret WHERE `id` = 1");
						$SQL -> execute(array(':cpipnsecret' => $_POST['cpsecret']));
						$update = true;
						$cpsecret = $_POST['cpsecret'];
					}

					if ($update == true)
						{
							echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have updated payment settings successfully!</div>';
						}
						else
						{
							echo '<div class="uk-alert uk-alert-info" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> No changes made.</div>';
						}
						if (!empty($errors))
						{
							echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a>';
							foreach($errors as $error)
							{
								echo '- '.$error.'<br />';
							}
							echo '</div>';
						}
				}
			?>
            <div class="md-card uk-margin-medium-bottom">
                <div class="md-card-content">
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-1-1">
                            <ul class="uk-tab" data-uk-tab="{connect:'#tabs_1'}">
                                <li class="uk-active"><a href="#">General</a></li>
                                <li><a href="#">Payment Settings</a></li>
                            </ul>
                            <ul id="tabs_1" class="uk-switcher uk-margin">
                                <li>
									<div class="md-card">
										<form method="post">
											<div class="md-card-content">
												<div class="uk-grid" data-uk-grid-margin>
													<div class="uk-width-medium-1-2">
														<div class="uk-form-row">
															<div class="uk-grid">
																<div class="uk-width-medium-1-1">
																	<label>Site Title</label>
																	<input type="text" name="sitetitle" value="<?php echo $sitetitle; ?>" class="md-input label-fixed" />
																</div>
															</div>
														</div>
														<div class="uk-form-row">
															<div class="uk-grid">
																<div class="uk-width-medium-1-1">
																	<label>Site Mail</label>
																	<input type="text" name="sitemail" value="<?php echo $sitemail; ?>" placeholder="from@yoursite.com" class="md-input label-fixed" />
																</div>
															</div>
														</div>
														<div class="uk-form-row">
															<div class="uk-grid">
																<div class="uk-width-medium-1-1">
																	<label>TAP9 Download</label>
																	<input type="text" name="tapdownload" value="<?php echo $tapdownload; ?>" class="md-input label-fixed" />
																</div>
															</div>
														</div>
														<div class="uk-form-row">
															<div class="uk-grid">
																<div class="uk-width-medium-1-1">
																	<label>Mailing Type</label>
																	<input type="text" name="mailingtype" value="<?php echo $mailingtype; ?>" placeholder="php / smtp" class="md-input label-fixed" />
																</div>
															</div>
														</div>
														<div class="uk-form-row">
															<div class="uk-grid">
																<div class="uk-width-medium-1-1">
																	<label>SMTP Host</label>
																	<input type="text" name="smtphost" value="<?php echo $smtphost; ?>" placeholder="mail.yoursite.com" class="md-input label-fixed" />
																</div>
															</div>
														</div>
														<div class="uk-form-row">
															<div class="uk-grid">
																<div class="uk-width-medium-1-1">
																	<label>SMTP Password</label>
																	<input type="text" name="smtppass" value="<?php echo $smtppass; ?>" class="md-input label-fixed" />
																</div>
															</div>
														</div>
													</div>
													<div class="uk-width-medium-1-2">
														<div class="uk-form-row">
															<div class="uk-grid">
																<div class="uk-width-medium-1-1">
																	<label>Site URL</label>
																	<input type="text" name="siteurl" value="<?php echo $siteurl; ?>" placeholder="Without http:// or www. Ex. yoursite.com/path" class="md-input label-fixed" />
																</div>
															</div>
														</div>
														<div class="uk-form-row">
															<div class="uk-grid">
																<div class="uk-width-medium-1-1">
																	<label>VPN Client Download</label>
																	<input type="text" name="vpndownload" value="<?php echo $vpndownload; ?>" class="md-input label-fixed" />
																</div>
															</div>
														</div>
														<div class="uk-form-row">
															<div class="uk-grid">
																<div class="uk-width-medium-1-1">
																	<label>VPN Config Download</label>
																	<input type="text" name="configdownload" value="<?php echo $configdownload; ?>" class="md-input label-fixed" />
																</div>
															</div>
														</div>
														<div class="uk-form-row">
															<div class="uk-grid">
																<div class="uk-width-medium-1-1">
																	<label>SMTP Port</label>
																	<input type="text" name="smtpport" value="<?php echo $smtpport; ?>" class="md-input label-fixed" />
																</div>
															</div>
														</div>
														<div class="uk-form-row">
															<div class="uk-grid">
																<div class="uk-width-medium-1-1">
																	<label>SMTP Username</label>
																	<input type="text" name="smtpuser" value="<?php echo $smtpuser; ?>" placeholder="usermail@yoursite.com" class="md-input label-fixed" />
																</div>
															</div>
														</div>
														<div class="uk-form-row">
															<div class="uk-grid">
																<div class="uk-width-medium-1-1">
																	<div class="uk-float-right">
																		<input type="checkbox" name="require_confirmation" <?php if ($require_confirmation == 1) { echo 'checked'; } ?> data-switchery data-switchery-color="#7cb342" id="settings_top_bar" name="settings_top_bar" />
																	</div>
																	<span class="md-list-heading">Require E-Mail Activation</span>
																</div>
															</div>
														</div>
													</div>
													<div class="uk-width-medium-1-1">
														<div class="uk-form-row">
															<label>VPN Client Release Notes</label>
															<textarea cols="30" rows="4" class="md-input label-fixed" name="vpnnotes"><?php echo $vpnnotes; ?></textarea>
														</div>
													</div>
												</div>
												<div class="uk-grid">
													<div class="uk-width-1-1">
														<button type="submit" name="updateGeneral" class="md-btn md-btn-primary">Update</button>
													</div>
												</div>
										</form>
										</div>
									</div>
								</li>
                                <li>
								<div class="md-card">
								<form method="post">
									<div class="md-card-content">
											<div class="uk-grid" data-uk-grid-margin>
												<div class="uk-width-medium-1-2">
													<div class="uk-form-row">
														<div class="uk-grid">
															<div class="uk-width-medium-1-1">
																<label>Coinpayments Merchant</label>
																<input type="text" name="cpmerchant" value="<?php echo $cpmerchant; ?>" class="md-input label-fixed" />
															</div>
														</div>
													</div>
													<div class="uk-form-row">
														<div class="uk-grid">
															<div class="uk-width-medium-1-1">
																<label>Coinpayments Secret</label>
																<input type="text" name="cpsecret" value="<?php echo $cpsecret; ?>" class="md-input label-fixed" />
															</div>
														</div>
													</div>
												</div>
												<div class="uk-width-medium-1-2">
													<div class="uk-form-row">
														<div class="uk-grid">
															<div class="uk-width-medium-1-1">
																<label>Paypal E-Mail</label>
																<input type="text" name="paypal" value="<?php echo $paypal; ?>" class="md-input label-fixed" />
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="uk-grid">
												<div class="uk-width-1-1">
													<button type="submit" name="updatePayment" class="md-btn md-btn-primary">Update</button>
												</div>
											</div>
									</form>
								</div>
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
