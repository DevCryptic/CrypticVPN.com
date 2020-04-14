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
$checkIfExists = $odb -> prepare("SELECT * FROM `servers` WHERE `id` = :serverid");
$checkIfExists -> execute(array(':serverid' => $serverid));
if($checkIfExists -> rowCount() == 0)
{
	header('location: servers.php');
	die();
}

$SQLGetInfo = $odb -> prepare("SELECT * FROM `servers` WHERE `id` = :id LIMIT 1");
$SQLGetInfo -> execute(array(':id' => $serverid));
$serverInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
$ipaddr = $serverInfo['ipaddress'];
$country = $serverInfo['country'];
$city = $serverInfo['city'];
$username = $serverInfo['username'];
$pfEnabled = $serverInfo['pfEnabled'];
$status = $serverInfo['status'];
$down_reason = $serverInfo['down_reason'];

if ($status == 'offline') {
	$sstatus = 'Offline';
} else {
	$sstatus = $status.' MS';
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

    <title><?php echo $site_title; ?> - View Server</title>

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
				
                if (isset($_POST['wipePorts'])){

                    $SQL = $odb -> prepare("UPDATE `ports` SET  status = 0, dateClosed = UNIX_TIMESTAMP() WHERE `server_id` = :id AND `status` = 1");
                    $SQL -> execute(array(':id' => $serverid));
                    $update = true;
                }
                
                if (isset($_POST['updateServer'])) {
					$update = false;
					$errors = array();
					if (!empty($_POST['password']))
					{
						$SQL = $odb -> prepare("UPDATE `servers` SET `password` = :password WHERE `id` = :id");
						$SQL -> execute(array(':password' => encryptData($_POST['password'], $encryptionKey), ':id' => $serverid));
						$update = true;
					}
					if ($ipaddr != $_POST['ipaddr'])
					{
						$SQL = $odb -> prepare("UPDATE `servers` SET `ipaddress` = :ipaddr WHERE `id` = :id");
						$SQL -> execute(array(':ipaddr' => $_POST['ipaddr'], ':id' => $serverid));
						$update = true;
						$ipaddr = $_POST['ipaddr'];
					}
					if ($country != $_POST['country'])
					{
						$SQL = $odb -> prepare("UPDATE `servers` SET `country` = :country WHERE `id` = :id");
						$SQL -> execute(array(':country' => $_POST['country'], ':id' => $serverid));
						$update = true;
						$country = $_POST['country'];
					}
					if ($city != $_POST['city'])
					{
						$SQL = $odb -> prepare("UPDATE `servers` SET `city` = :city WHERE `id` = :id");
						$SQL -> execute(array(':city' => $_POST['city'], ':id' => $serverid));
						$update = true;
						$city = $_POST['city'];
					}
					if ($username != $_POST['username'])
					{
						$SQL = $odb -> prepare("UPDATE `servers` SET `username` = :username WHERE `id` = :id");
						$SQL -> execute(array(':username' => $_POST['username'], ':id' => $serverid));
						$update = true;
						$username = $_POST['username'];
					}
                    if (isset($_POST['pfEnabled'])) {
                        $SQL = $odb -> prepare("UPDATE `servers` SET `pfEnabled` = 1 WHERE `id` = :id");
                        $SQL -> execute(array(':id' => $serverid));
                        $update = true;
                        $pfEnabled = 1;
                        } else {
                            $SQL = $odb -> prepare("UPDATE `servers` SET `pfEnabled` = 0 WHERE `id` = :id");
                            $SQL -> execute(array(':id' => $serverid));
                            $update = true;
                            $pfEnabled = 0;
                        }

					if ($update == true)
					{
						echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have updated the server information!</div>';
					}
					else
					{
						echo '<div class="uk-alert uk-alert-info" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> No changes were made. </div>';
					}
				}
				if (isset($_POST['deleteServer'])) {
					$sql = $odb -> prepare("DELETE FROM `servers` WHERE `id` = :id");
					$sql -> execute(array(':id' => $serverid));

                    $sql1 = $odb -> prepare("DELETE FROM `ports` WHERE `server_id` = :id");
                    $sql1 -> execute(array(':id' => $serverid));

					echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have deleted the server!</div>';
				}

			?>
			<div class="md-card">
			<form method="post">
                <div class="md-card-content">
                    <h3 class="heading_a">View & Manage Server - <?php echo $ipaddr.' ('.$sstatus.')'; ?></h3>
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
                                        <label>Server Country</label>
                                        <input type="text" name="country" value="<?php echo $country; ?>" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
							<div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-1">
                                        <label>Server City</label>
                                        <input type="text" name="city" value="<?php echo $city; ?>" class="md-input label-fixed" />
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
										<input type="password" class="md-input" name="password" placeholder="Leave blank if you don't want to change" />
										<a href="" class="uk-form-password-toggle" data-uk-form-password>show</a>
                                    </div>
                                </div>
                            </div>

                            <div class="uk-width-medium-1-2">
                                <input type="checkbox" name="pfEnabled" data-switchery <?php if ($pfEnabled == 1) { echo 'checked'; } ?> value="1" id="switch_demo_1" />
                                <label for="switch_demo_1" class="inline-label">Port Forwarding</label>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid">
						<div class="uk-width-1-1">
							<button type="submit" name="updateServer" class="md-btn md-btn-primary">Update</button>
                            <button type="submit" name="wipePorts" class="md-btn md-btn-primary">Wipe Ports</button>
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
