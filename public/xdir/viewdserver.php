<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)))
{
	header('Location: login.php');
	die();
}

$serverid = $_GET['id'];
//Check if the user exists.
$checkIfExists = $odb -> prepare("SELECT * FROM `dedicateds` WHERE `id` = :serverid");
$checkIfExists -> execute(array(':serverid' => $serverid));
if($checkIfExists -> rowCount() == 0)
{
	header('location: dedicated.php');
	die();
}

$SQLGetInfo = $odb -> prepare("SELECT * FROM `dedicateds` WHERE `id` = :id LIMIT 1");
$SQLGetInfo -> execute(array(':id' => $serverid));
$serverInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
$ipaddr = $serverInfo['ipaddress'];
$owner = $serverInfo['owner'];
$purchasedate = $serverInfo['purchasedate'];
$name = $serverInfo['name'];
$username = $serverInfo['username'];
$nextpayment = $serverInfo['nextpayment'];
$nextpaymentdate = gmdate("d-m-Y", $nextpayment);
$spots = $serverInfo['vpnspots'];
$status = $serverInfo['status'];
$config_dl = $serverInfo['config_dl'];
$profile_dl = $serverInfo['profile_dl'];
$tid = $serverInfo['tid'];
$ping = $serverInfo['ping'];
$down_reason = $serverInfo['dt_notes'];
$packageid = $serverInfo['package'];

if ($ping == 'offline') {
	$sstatus = 'Offline';
} else {
	$sstatus = $ping.' MS';
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
				if (isset($_POST['updateServer'])) {
					$update = false;
					$errors = array();
					if (!empty($_POST['password']))
					{
						$SQL = $odb -> prepare("UPDATE `dedicateds` SET `password` = :password WHERE `id` = :id");
						$SQL -> execute(array(':password' => encryptData($_POST['password'], $encryptionKey), ':id' => $serverid));
						$update = true;
					}
					if ($ipaddr != $_POST['ipaddr'])
					{
						$SQL = $odb -> prepare("UPDATE `dedicateds` SET `ipaddress` = :ipaddr WHERE `id` = :id");
						$SQL -> execute(array(':ipaddr' => $_POST['ipaddr'], ':id' => $serverid));
						$update = true;
						$ipaddr = $_POST['ipaddr'];
					}
					if ($spots != $_POST['spots'])
					{
						$SQL = $odb -> prepare("UPDATE `dedicateds` SET `vpnspots` = :spots WHERE `id` = :id");
						$SQL -> execute(array(':spots' => $_POST['spots'], ':id' => $serverid));
						$update = true;
						$spots = $_POST['spots'];
					}
					if ($packageid != $_POST['packageid'])
					{
						$SQL = $odb -> prepare("UPDATE `dedicateds` SET `package` = :packageid WHERE `id` = :id");
						$SQL -> execute(array(':packageid' => $_POST['packageid'], ':id' => $serverid));
						$update = true;
						$packageid = $_POST['packageid'];
					}
					if ($owner != $_POST['owner'])
					{
						$SQL = $odb -> prepare("UPDATE `dedicateds` SET `owner` = :owner WHERE `id` = :id");
						$SQL -> execute(array(':owner' => $_POST['owner'], ':id' => $serverid));
						$update = true;
						$owner = $_POST['owner'];
					}
					if ($name != $_POST['name'])
					{
						$SQL = $odb -> prepare("UPDATE `dedicateds` SET `name` = :name WHERE `id` = :id");
						$SQL -> execute(array(':name' => $_POST['name'], ':id' => $serverid));
						$update = true;
						$name = $_POST['name'];
					}
					if ($nextpaymentdate != $_POST['nextpayment'])
					{
						$nextpaymentunix = strtotime($_POST['nextpayment']);
						$SQL = $odb -> prepare("UPDATE `dedicateds` SET `nextpayment` = :nextpayment WHERE `id` = :id");
						$SQL -> execute(array(':nextpayment' => $nextpaymentunix, ':id' => $serverid));
						$update = true;
						$nextpayment = $nextpaymentunix;
					}
					if ($config_dl != $_POST['configdl'])
					{
						$SQL = $odb -> prepare("UPDATE `dedicateds` SET `config_dl` = :config_dl WHERE `id` = :id");
						$SQL -> execute(array(':config_dl' => $_POST['configdl'], ':id' => $serverid));
						$update = true;
						$config_dl = $_POST['configdl'];
					}
					if ($profile_dl != $_POST['profiledl'])
					{
						$SQL = $odb -> prepare("UPDATE `dedicateds` SET `profile_dl` = :profile_dl WHERE `id` = :id");
						$SQL -> execute(array(':profile_dl' => $_POST['profiledl'], ':id' => $serverid));
						$update = true;
						$profile_dl = $_POST['profiledl'];
					}
					if ($username != $_POST['username'])
					{
						$SQL = $odb -> prepare("UPDATE `dedicateds` SET `username` = :username WHERE `id` = :id");
						$SQL -> execute(array(':username' => $_POST['username'], ':id' => $serverid));
						$update = true;
						$username = $_POST['username'];
					}
					if ($down_reason != $_POST['dtreason'])
					{
						$SQL = $odb -> prepare("UPDATE `dedicateds` SET `dt_notes` = :dtreason WHERE `id` = :id");
						$SQL -> execute(array(':dtreason' => $_POST['dtreason'], ':id' => $serverid));
						$update = true;
						$down_reason = $_POST['dtreason'];
					}

					if ($update == true)
					{
						$SQL = $odb -> prepare("UPDATE `dedicateds` SET `status` = 1 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $serverid));
						echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have updated the server information!</div>';
					}
					else
					{
						echo '<div class="uk-alert uk-alert-info" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> No changes were made. </div>';
					}
				}
				if (isset($_POST['deleteServer'])) {
					$sql = $odb -> prepare("DELETE FROM `dedicateds` WHERE `id` = :id");
					$sql -> execute(array(':id' => $serverid));

					echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have deleted the server!</div>';
				}
			?>
			<div class="md-card">
			<form method="post">
                <div class="md-card-content">
                    <h3 class="heading_a">View & Manage Dedicated Server - <?php echo $ipaddr.' ('.$sstatus.')'; ?></h3>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-2">
                            <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
                                        <label>Server IP Address</label>
                                        <input type="text" name="ipaddr" value="<?php echo $ipaddr; ?>" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
                                        <label>Server / Package Name</label>
                                        <input type="text" name="name" value="<?php echo $name; ?>" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
														<div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
																			<label for="uk_dp_1">Next Payment Date</label>
																			<input class="md-input label-fixed" name="nextpayment" value="<?php echo gmdate("d-m-Y", $nextpayment); ?>" type="text" id="uk_dp_1" data-uk-datepicker="{format:'DD-MM-YYYY'}">
                                    </div>
                                </div>
                            </div>
														<div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-3">
                                        <label>Alt VPN Spots</label>
                                        <input type="text" name="spots" value="<?php echo $spots; ?>" class="md-input label-fixed" />
                                    </div>
																		<div class="uk-width-medium-1-3">
                                        <label>Package ID</label>
                                        <input type="text" name="packageid" value="<?php echo $packageid; ?>" class="md-input label-fixed" />
                                    </div>
																		<div class="uk-width-medium-1-3">
                                        <label>Server Owner</label>
                                        <input type="text" name="owner" value="<?php echo $owner; ?>" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
														<div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
																			<label for="uk_dp_1">Configuration Download</label>
																			<input type="text" name="configdl" value="<?php echo $config_dl; ?>" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
														<div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
																			<label for="uk_dp_1">OpenVPN Profile File</label>
																			<input type="text" name="profiledl" value="<?php echo $profile_dl; ?>" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
                        </div>
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
																				<input type="password" class="md-input label-fixed" name="password" />
																				<a href="" class="uk-form-password-toggle" data-uk-form-password>show</a>
                                    </div>
                                </div>
                            </div>
														<div class="uk-form-row">
                                <label>Downtime Reason</label>
                                <textarea cols="30" rows="4" class="md-input label-fixed" name="dtreason"><?php echo $down_reason; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid">
						<div class="uk-width-1-1">
							<button type="submit" name="updateServer" class="md-btn md-btn-primary">Update Server</button>
							<button type="submit" name="deleteServer" class="md-btn md-btn-danger" onclick="return confirm('Are you sure you want to delete this server?');">Remove Server</button>
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
