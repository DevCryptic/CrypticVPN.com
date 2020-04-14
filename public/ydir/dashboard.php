<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)) && !($user -> IsStaff($odb)))
{
	header('Location: https://crypticvpn.com/ydir');
	die();
}

if ($user -> IsStaff($odb))
{
    header('Location: https://crypticvpn.com/ydir/tickets.php');
    die();
}


?>
<!DOCTYPE html>
<html lang="en">
<title><?php echo $site_title; ?> - Dashboard</title>   
<?php include('../templates/head.php'); ?>
<body>
    <?php include('../templates/header.php'); ?>
    <div class="container">
      <div class="wrap-main">
        <div class="main-contain col-sm-11">
            <div class="left-content col-sm-3">
                <?php include('../templates/sidebar_left_adm.php'); ?>
            </div>
            <div class="right-content col-sm-9">
                <ul class="nav nav-tabs responsive" id="mysetting">
                    <li class="test-class <?php if(empty($_GET['tab']) || $_GET['tab']=='dashboard') echo 'active'; ?>"><a class="deco-none misc-class" href="#dashboard"> Dashboard</a></li>
                    <li class="test-class <?php if(isset($_GET['tab']) && $_GET['tab']=='stats') echo 'active'; ?>"><a href="#stats">Statistics</a></li>
                    <li class="test-class <?php if(isset($_GET['tab']) && $_GET['tab']=='paymentstats') echo 'active'; ?>"><a href="#paymentstats">Payment Stats</a></li>
                    <li class="test-class <?php if(isset($_GET['tab']) && $_GET['tab']=='other') echo 'active'; ?>"><a href="#other">Other</a></li>
                </ul>
                <div class="tab-content responsive">
		
                  <div class="tab-pane <?php if(empty($_GET['tab']) || $_GET['tab']=='dashboard') echo 'active'; ?>" id="dashboard">       
				
                        <div class="dash-content">
                            <div class="col-sm-5">
                                <div class="item-dash">
                                    <div class="left-item">
                                        <div class="item-icon"><span class="glyphicon glyphicon-globe"></span></div>
                                    </div>
                                    <div class="right-item">
                                        <div class="item-title">Pending Tickets</div>
                                        <div class="item-value"><a href="viewticket.php"><?php echo $gsetting -> pendingTickets($odb); ?></a></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="item-dash">
                                    <div class="left-item">
                                        <div class="item-icon"><span class="glyphicon glyphicon-calendar"></span></div>
                                    </div>
                                    <div class="right-item">
                                        <div class="item-title">Sales Today</div>
                                        <div class="item-value">$
                                        <?php if (!($user -> IsAdmin($odb))) { echo "N/A";} else echo $gsetting -> earnedToday($odb);?> 
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
                                        <div class="item-title">Sales Yesterday</div>
                                        <div class="item-value">$
                                        <?php if (!($user -> IsAdmin($odb))) { echo "N/A";} else echo $gsetting -> earnedYesterday($odb);?> 
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
                                        <div class="item-title">Sales Overall</div>
                                        <div class="item-value">$
                                        <?php if (!($user -> IsAdmin($odb))) { echo "N/A";} else echo $gsetting -> earnedOverall($odb);?> 
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>  
                        
                    </div>
                    <div class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab']=='stats') echo 'active'; ?>" id="stats">
                    x
					</div>


                    <div class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab']=='paymentstats') echo 'active'; ?>" id="paymentstats">                         
                        <div class="table-responsive">

                                <? if (!($user->IsAdmin($odb))) {
									echo "You must have be an admin to access this page.";
								}
								else
								{
								?>
								
                                <table class="table table-hover">
                                    <thead>
                                        <tr> <th>Month</th> <th>Total</th> <th>PayPal</th> <th>BTC</th> <th>BCC</th> <th>PM</th> <th>ETH</th> <th>LTC</th> <th>Rocketr Fees 2%</th> <TH> Rocketr Fees 5%</TH></tr> 
                                    </thead>
                                    <tbody>
                                    <?php
                                    $getTickets = $odb -> prepare("SELECT DISTINCT FROM_UNIXTIME(date,'%M, %Y') AS month, ROUND(SUM(amount),2) AS Total, ROUND(SUM(CASE WHEN processor = 'BTC' THEN amount ELSE 0 END),2) AS BTC, ROUND(SUM(CASE WHEN processor = 'BCC' THEN amount ELSE 0 END),2) AS BCC,ROUND(SUM(CASE WHEN processor = 'ETH' THEN amount ELSE 0 END),2) AS ETH,  ROUND(SUM(CASE WHEN processor = 'LTC' THEN amount ELSE 0 END),2) AS LTC,ROUND(SUM(CASE WHEN processor = 'PP' THEN amount ELSE 0 END),2) AS PP, ROUND(SUM(CASE WHEN processor = 'PM' THEN amount ELSE 0 END),2) AS PM, ROUND(SUM(CASE WHEN processor !='PP' AND id<=3824 THEN amount  ELSE 0 END),2)*0.05 AS RocketrFees, ROUND(SUM(CASE WHEN processor !='PP' AND id>3824  THEN amount END),2)*0.02+10 AS RocketrFeesAt2Percent from payment_logs WHERE valid = 1 group by month order by (date) ASC;");
                                    $getTickets -> execute();
                                    while ($getInfo = $getTickets -> fetch(PDO::FETCH_ASSOC))
                                    {
                                        $month = $getInfo['month'];
                                        $total = $getInfo['Total'];
                                        $btc = $getInfo['BTC'];
                                        $bcc = $getInfo['BCC'];
                                        $eth = $getInfo['ETH'];
                                        $ltc = $getInfo['LTC'];
                                        $paypal = $getInfo['PP'];
                                        $pm = $getInfo['PM'];
                                        $fees = $getInfo['RocketrFees'];
                                        $fees2 = $getInfo['RocketrFeesAt2Percent'];

                                        echo '
                                        <tr>
                                            <td>'.$month.'</td>
                                            <td>$'.$total.' </td>
                                            <td>$'.$paypal.' </td>
                                            <td>$'.$btc.' </td>
                                            <td>$'.$bcc.' </td>
                                            <td>$'.$pm.' </td>
                                            <td>$'.$eth.' </td>
                                            <td>$'.$ltc.' </td>
                                            <td>$'.$fees2.' </td>
                                            <td>$'.$fees.' </td>
                                        </tr>';
                                    }
                                ?>
                                    </tbody>
                                </table>  


                                <?}?>
                        </div>  
                    </div>  

                    <div class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab']=='other') echo 'active'; ?>" id="other">                         
                       yyy
                    </div> 
                                  
                </div>                          
            </div>
        </div>
      </div>
    </div><!-- /.container -->


    <?php include('../templates/footer.php'); ?>
	<?php include('../templates/scriptjs.php'); ?>
</body>
</html>