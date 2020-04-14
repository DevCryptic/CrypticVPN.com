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

    <title><?php echo $site_title; ?> - Manage Packages</title>

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
				if (isset($_POST['addPackage']))
				{
					$name = $_POST['name'];
					$price = $_POST['price'];
					$length = $_POST['length'];
					$unit = $_POST['unit'];
					$details = $_POST['details'];
					$errors = array();

						if (empty($name) || empty($price) || empty($length) || empty($unit) || empty($details))
						{
							$errors[] = 'Please fill in all fields.';
						}

						if(!is_numeric($length)) {
							$errors[] = 'Invalid package length.';
						}

						if (isset($_POST['pfoption'])) {
							$portforward = 1;
						} else {
							$portforward = 0;
						}

						if(empty($errors)) {
							$insertPackage = $odb -> prepare("INSERT INTO `packages` VALUES (NULL, :name, :details, :length, :unit, :port, :price)");
							$insertPackage -> execute(array(':name' => $name, ':price' => $price, ':unit' => $unit, ':length' => $length, ':details' => $details, ':port' => $portforward));
							echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have added a new package successfully!</div>';
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
                    <h3 class="heading_a">Add New Package</h3>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-2">
                            <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-2">
                                        <label>Package Name</label>
                                        <input type="text" name="name" class="md-input label-fixed" />
                                    </div>
									<div class="uk-width-medium-1-2">
                                        <label>Price</label>
                                        <input type="text" name="price" class="md-input label-fixed" />
                                    </div>
                                </div>
                            </div>
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
                        </div>
                        <div class="uk-width-medium-1-2">
							<div class="uk-form-row">
                               <input type="checkbox" name="pfoption" data-switchery id="switch_demo_1" />
								<label for="switch_demo_1" class="inline-label">Port Forwarding Enabled</label>
                            </div>
                            <div class="uk-form-row">
                                <label>Details</label>
                                <textarea cols="30" rows="4" class="md-input label-fixed" name="details" placeholder="<li><strong>Unmetered</strong> Bandwidth</li>"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="uk-grid">
						<div class="uk-width-1-1">
							<button type="submit" name="addPackage" class="md-btn md-btn-primary">Submit</button>
						</div>
					</div>
					</form>
                </div>
            </div>
			<br>
			<h4 class="heading_a uk-margin-bottom">Manage Packages</h4>
			<div class="md-card uk-margin-medium-bottom">
                <div class="md-card-content">
                    <div class="uk-overflow-container">
                        <table class="uk-table">
                            <thead>
                            <tr>
                                <th>Package</th>
								<th>Length</th>
								<th>Price</th>
								<th>Manage</th>
                            </tr>
                            </thead>
                            <tbody>
                           <?php
								$GetPackages = $odb -> prepare("SELECT * FROM `packages` ORDER BY `id` DESC LIMIT 100");
								$GetPackages -> execute();
								while ($getInfo = $GetPackages -> fetch(PDO::FETCH_ASSOC))
								{
									$id = $getInfo['id'];
									$name = $getInfo['name'];
									$length = $getInfo['length'];
									$unit = $getInfo['unit'];
									$price = $getInfo['price'];
									echo '
									<tr>
										<td>'.$name.'</td>
										<td>'.$length.' '.$unit.' </td>
										<td>'.$price.'</td>
										<td><a class="md-btn md-btn-primary" href="viewpackage.php?id='.$id.'">View</a></td>
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
