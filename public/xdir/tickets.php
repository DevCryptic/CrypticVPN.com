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

    <title><?php echo $site_title; ?> - Tickets</title>

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


			<div class="md-card">
				<div class="md-card-content">
					<ul class="uk-tab uk-tab-grid" data-uk-tab="{connect:'#tabs_4'}">
						<li class="uk-width-1-2 uk-active"><a href="#">Tickets Pending Answer</a></li>
						<li class="uk-width-1-2"><a href="#">All Tickets</a></li>
					</ul>
					<ul id="tabs_4" class="uk-switcher uk-margin">
						<li>
							<?php
								$checkIfExists = $odb -> prepare("SELECT * FROM `tickets` WHERE `status` = 1");
								$checkIfExists -> execute();
								if($checkIfExists -> rowCount() == 0)
								{
									echo '<i> - No Pending Tickets - </i>';
								} else {
							?>
								<div class="md-card uk-margin-medium-bottom">
									<div class="md-card-content">
										<div class="uk-overflow-container">
											<table class="uk-table">
												<thead>
												<tr>
													<th>ID</th>
													<th>Department</th>
													<th>User</th>
													<th>Title</th>
													<th>Status</th>
													<th>Manage</th>
												</tr>
												</thead>
												<tbody>
											   <?php
													$GetTickets = $odb -> prepare("SELECT * FROM `tickets` WHERE `status` = 1 ORDER BY `id` DESC LIMIT 100");
													$GetTickets -> execute();
													while ($getInfo = $GetTickets -> fetch(PDO::FETCH_ASSOC))
													{
														$id = $getInfo['id'];
														$senderID = $getInfo['senderid'];
														$department = $getInfo['department'];
														$getTitle = $getInfo['title'];

														$SQLGetTicketOwner = $odb -> prepare("SELECT `username` FROM `accounts` WHERE `id` = :id LIMIT 1");
														$SQLGetTicketOwner -> execute(array(':id' => $senderID));
														$ticketowner = $SQLGetTicketOwner -> fetch(PDO::FETCH_ASSOC);
														$ticketownerusername = $ticketowner['username'];


														if (strlen($getTitle) > 15) {
																substr($getTitle ,0,15).'...';
															}
														$title = $getTitle;
														switch($getInfo['status'])
														{

														case 1:
														$status = '<span class="uk-badge uk-badge-warning">Pending</span>';
														break;
														case 2:
														$status = '<span class="uk-badge uk-badge-success">Answered</span>';
														break;
														case 3:
														$status = '<span class="uk-badge uk-badge-danger">Closed</span>';
														break;
														}

														echo '
														<tr>
															<td>'.$id.'</td>
															<td>'.$department.'</td>
															<td>'.$ticketownerusername.'</td>
															<td>'.$title.' </td>
															<td>'.$status.'</td>
															<td><a class="md-btn md-btn-primary" href="viewticket.php?id='.$id.'">View</a></td>
														</tr>';
													}
												?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							<?php } ?>
						</li>
						<li>
							<table class="uk-table">
								<thead>
								<tr>
									<th>ID</th>
									<th>Department</th>
									<th>User</th>
									<th>Title</th>
									<th>Status</th>
									<th>Manage</th>
								</tr>
								</thead>
								<tbody>
							   <?php
									$GetTickets = $odb -> prepare("SELECT * FROM `tickets` ORDER BY `id` DESC LIMIT 100");
									$GetTickets -> execute(array(':sender' => $_SESSION['rID']));
									while ($getInfo = $GetTickets -> fetch(PDO::FETCH_ASSOC))
									{
										$id = $getInfo['id'];
										$senderID = $getInfo['senderid'];
										$department = $getInfo['department'];
										$getTitle = $getInfo['title'];
										if (strlen($getTitle) > 15) {
												substr($getTitle ,0,15).'...';
											}
										$title = $getTitle;
										$SQLGetTicketOwner = $odb -> prepare("SELECT `username` FROM `accounts` WHERE `id` = :id LIMIT 1");
														$SQLGetTicketOwner -> execute(array(':id' => $senderID));
														$ticketowner = $SQLGetTicketOwner -> fetch(PDO::FETCH_ASSOC);
														$ticketownerusername = $ticketowner['username'];

										switch($getInfo['status'])
										{
										case 1:
										$status = '<span class="uk-badge uk-badge-warning">Pending</span>';
										break;
										case 2:
										$status = '<span class="uk-badge uk-badge-success">Answered</span>';
										break;
										case 3:
										$status = '<span class="uk-badge uk-badge-danger">Closed</span>';
										break;
										}

										echo '
										<tr>
											<td>'.$id.'</td>
											<td>'.$department.'</td>
											<td>'.$ticketownerusername.'</td>
											<td>'.$title.' </td>
											<td>'.$status.'</td>
											<td><a class="md-btn md-btn-primary" href="viewticket.php?id='.$id.'">View</a></td>
										</tr>';
									}
								?>
								</tbody>
							</table>
						</li>
					</ul>
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
