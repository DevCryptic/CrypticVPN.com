<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)))
{
	header('Location: login.php');
	die();
}

$packageid = $_GET['id'];
//Check if the user exists.
$checkIfExists = $odb -> prepare("SELECT * FROM `packages` WHERE `id` = :packageid");
$checkIfExists -> execute(array(':packageid' => $packageid));
if($checkIfExists -> rowCount() == 0)
{
	header('location: packages.php');
	die();
}

$SQLGetInfo = $odb -> prepare("SELECT * FROM `packages` WHERE `id` = :id LIMIT 1");
$SQLGetInfo -> execute(array(':id' => $packageid));
$packageInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
$name = $packageInfo['name'];
$sellNowProductID = $packageInfo['sellNowProductID'];
$length = $packageInfo['length'];
$unit = $packageInfo['unit'];
$pfoption = $packageInfo['pfenabled'];
$price = $packageInfo['price'];

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

    <title><?php echo $site_title; ?> - View Package</title>

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
				if (isset($_POST['updatePackage'])) {
					$update = false;
					$errors = array();
					if ($name != $_POST['name'])
					{
						$SQL = $odb -> prepare("UPDATE `packages` SET `name` = :name WHERE `id` = :id");
						$SQL -> execute(array(':name' => $_POST['name'], ':id' => $packageid));
						$update = true;
						$name = $_POST['name'];
					}
					if ($price != $_POST['price'])
					{
						$SQL = $odb -> prepare("UPDATE `packages` SET `price` = :price WHERE `id` = :id");
						$SQL -> execute(array(':price' => $_POST['price'], ':id' => $packageid));
						$update = true;
						$price = $_POST['price'];
					}
					if ($sellNowProductID != $_POST['sellNowProductID'])
					{
						$SQL = $odb -> prepare("UPDATE `packages` SET `sellNowProductID` = :sellNowProductID WHERE `id` = :id");
						$SQL -> execute(array(':sellNowProductID' => $_POST['sellNowProductID'], ':id' => $packageid));
						$update = true;
						$sellNowProductID = $_POST['sellNowProductID'];
					}
					if ($length != $_POST['length'])
					{
						$SQL = $odb -> prepare("UPDATE `packages` SET `length` = :length WHERE `id` = :id");
						$SQL -> execute(array(':length' => $_POST['length'], ':id' => $packageid));
						$update = true;
						$length = $_POST['length'];
					}
					if ($unit != $_POST['unit'])
					{
						$SQL = $odb -> prepare("UPDATE `packages` SET `unit` = :unit WHERE `id` = :id");
						$SQL -> execute(array(':unit' => $_POST['unit'], ':id' => $packageid));
						$update = true;
						$unit = $_POST['unit'];
					}
					if (isset($_POST['pfoption']))
					{
						$SQL = $odb -> prepare("UPDATE `packages` SET `pfenabled` = 1 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $packageid));
						$update = true;
						$pfoption = 1;
					} else {
						$SQL = $odb -> prepare("UPDATE `packages` SET `pfenabled` = 0 WHERE `id` = :id");
						$SQL -> execute(array(':id' => $packageid));
						$update = true;
						$pfoption = 0;
					}

					if ($update == true)
					{
						echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have updated the package information!</div>';
					}
					else
					{
						echo '<div class="uk-alert uk-alert-info" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> No changes were made. </div>';
					}
				}
				if (isset($_POST['deletePackage'])) {
					$sql = $odb -> prepare("DELETE FROM `packages` WHERE `id` = :id");
					$sql -> execute(array(':id' => $packageid));

					echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have deleted the package!</div>';
				}
			?>
			<div class="md-card">
			<form method="post">
                <div class="md-card-content">
                    <h3 class="heading_a">View & Manage Package - <?php echo $name; ?></h3>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-2">
                            <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-2">
                                        <label>Package Name</label>
                                        <input type="text" name="name" value="<?php echo $name; ?>" class="md-input label-fixed" />
                                    </div>
																		<div class="uk-width-medium-1-2">
                                        <label>Price</label>
                                        <input type="text" name="price" value="<?php echo $price; ?>" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-2">
                                        <label>Length</label>
                                        <input type="text" name="length" value="<?php echo $length; ?>" class="md-input label-fixed" />
                                    </div>
                                    <div class="uk-width-medium-1-2">
                                        <select id="select_demo_1" name="unit" data-md-selectize>
											<?php
												function selectedR($check, $unit)
												{
													if ($check == $unit)
													{
														return 'selected="selected"';
													}
												}
											?>
											<option value="">Select Unit..</option>
												<option value="Days" <?php echo selectedR('Days', $unit); ?>>Day(s)</option>
												<option value="Weeks" <?php echo selectedR('Weeks', $unit); ?>>Week(s)</option>
												<option value="Months" <?php echo selectedR('Months', $unit); ?>>Month(s)</option>
												<option value="Years"<?php echo selectedR('Years', $unit); ?>>Year(s)</option>
										</select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-medium-1-2">
							<div class="uk-form-row">
                               <input type="checkbox" name="pfoption" <?php if ($pfoption == 1) { echo 'checked'; } ?> data-switchery id="switch_demo_1" />
								<label for="switch_demo_1" class="inline-label">Port Forwarding Enabled</label>
                            </div>
                            <div class="uk-form-row">
                                <label>sellNowProductID</label>
                                <input type="text" name="sellNowProductID" value="<?php echo $sellNowProductID; ?>" class="md-input label-fixed" />
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid">
						<div class="uk-width-1-1">
							<button type="submit" name="updatePackage" class="md-btn md-btn-primary">Update</button>
							<button type="submit" name="deletePackage" class="md-btn md-btn-danger" onclick="return confirm('Are you sure you want to delete this package?');">Remove Package</button>
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
