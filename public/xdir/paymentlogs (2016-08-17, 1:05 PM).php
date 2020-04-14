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

    <title><?php echo $site_title; ?> - Payment Logs</title>

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

			<h4 class="heading_a uk-margin-bottom">Last 50 Payments</h4>
			<div class="md-card uk-margin-medium-bottom">
                <div class="md-card-content">
                    <div class="uk-overflow-container">
                        <table class="uk-table">
                            <thead>
                            <tr>
                                <th>Transaction ID</th>
								<th>Payment By</th>
								<th>Method / Amount</th>
								<th>Date</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                        
                           <?php
								$GetPayments = $odb -> prepare("SELECT * FROM `payment_logs` ORDER BY `id` DESC limit 50");
								$GetPayments -> execute();
								while ($getInfo = $GetPayments -> fetch(PDO::FETCH_ASSOC))
								{
									$id = $getInfo['id'];
									$tid = $getInfo['tid'];
									$payerid = $getInfo['payer'];
									$method = $getInfo['processor'];
									$amount = $getInfo['amount'];
									$date = date("m-d-Y h:i:s A", $getInfo['date']);
									$GetUsername = $odb -> prepare("SELECT `username` FROM `accounts` WHERE `id` = :id LIMIT 1");
									$GetUsername -> execute(array(':id' => $payerid));
									$paymentowner = $GetUsername -> fetch(PDO::FETCH_ASSOC);
									$username = $paymentowner['username'];
                                    
                                    if (($getInfo['valid']==1))
                                    {
                                    $valid = '<input type="hidden" class="validId" value="'.$getInfo['id'].'"><span class="setValidLink">Valid</span>';
                                    }
                                    elseif ($getInfo['valid']==0)
                                    {
                                    $valid = '<input type="hidden" class="validId" value="'.$getInfo['id'].'"><span class="setValidLink">Invalid</span>';                                    
                                    }
                                    else
                                    {
                                        $valid = "Unknown";
                                    }

									echo '
									<tr>
										<td>'.$tid.'</td>
										<td>'.$username.' ('.$payerid.') </td>
										<td>'.$method.' / '.$amount.'</td>
										<td>'.$date.'</td>
                                        <td>'.$valid.'</td>
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

    <script>
        $('.setValidLink').on('click', function() {
            var span = $(this);
            var valid = 0;

            if(span.text() == 'Invalid')
                valid = 1;

            var id = span.parent().find('input.validId').val();
            $.get( "managepayments.php", { minv: id, valid: valid } ).done(function(data) {
                console.log(data);

                if(span.text() == 'Valid')
                    span.text('Invalid');
                else
                    span.text('Valid');
            });
        });
    </script>

</body>
</html>

<?
if (isset($_GET['minv']))
                    {
                    if (is_numeric($_GET['minv'])){

                        $minv = $_GET['minv'];

                        $SQL = $odb -> prepare("UPDATE `payment_logs` SET `valid` = 0 WHERE `id` = :minv");
                        $SQL -> execute(array(':minv' => $minv));

                        echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have marked the payment as invalid!</div>';
                        }
                     else {
                        echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> Error. </div>';
                    }
                }
                
?>