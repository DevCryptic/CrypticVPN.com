<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)))
{
	header('Location: https://crypticvpn.com/ydir/');
	die();
}


?>

<!DOCTYPE html>
<html lang="en">
 <title><?php echo $site_title; ?> - Payments</title>   
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

                <div class="tab-content responsive">

                        <div class="table-responsive">

                            <table id="datatable" class="table table-hover tickets">
                                <thead>
                                    <tr> <th>Date</th> <th> Transaction ID </th> <th> User </th> <th>Amount</th> <th>Status</th> </tr> 
                                </thead>
                                <tbody>
									
						<?php
								$GetPayments = $odb -> prepare("SELECT * FROM `payment_logs` WHERE date > unix_timestamp(now() - interval 180 day) ORDER BY `id` DESC");
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
                                        <td>'.$date.'</td>
										<td>'.$tid.'</td>
                                        <td> <a href="users.php?id=' . $payerid . '"> ' . $username. '</a> </td>
										<td>'.$method.' / '.$amount.'</td>
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
    </div><!-- /.container -->

    <?php include('../templates/footer.php'); ?>
	<?php include('../templates/scriptjs.php'); ?>
	
<!-- DataTables -->
        <link href="../assets/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/dataTables.colVis.css" rel="stylesheet" type="text/css" />
<!--jQuery-->
	    <script src="../assets/js/jquery.dataTables.min.js"></script>
	    <script src="../assets/js/dataTables.bootstrap.js"></script>
	    <script src="../assets/js/dataTables.buttons.min.js"></script>
	    <script src="../assets/js/buttons.bootstrap.min.js"></script>
	    <script src="../assets/js/jszip.min.js"></script>
	    <script src="../assets/js/pdfmake.min.js"></script>
	    <script src="../assets/js/vfs_fonts.js"></script>
	    <script src="../assets/js/buttons.html5.min.js"></script>
	    <script src="../assets/js/buttons.print.min.js"></script>

	    <script src="../assets/js/dataTables.fixedHeader.min.js"></script>
	    <script src="../assets/js/dataTables.keyTable.min.js"></script>
	    <script src="../assets/js/dataTables.responsive.min.js"></script>
	    <script src="../assets/js/responsive.bootstrap.min.js"></script>
	    <script src="../assets/js/dataTables.scroller.min.js"></script>
	    <script src="../assets/js/dataTables.colVis.js"></script>
</html>

<script type="text/javascript">
    $(document).ready(function(){
        window.ordersTable = $('#datatable').dataTable({
            "order": [[ 0, "desc" ]],
        });
        
       /*  window.ordersTable.fnFilter('^((?!Invalid).)*$', 4, true); 
        var hidingTimedOut = true;
        
       $('#toggleClosed').click(function() { 
            if(hidingTimedOut) {
                window.ordersTable.fnFilter('', 4, true); 
                hidingTimedOut = false;
                $('#toggleClosed').text('Hide Closed Tickets');
            } else {
                window.ordersTable.fnFilter('^((?!Closed).)*$', 4, true); 
                hidingTimedOut = true;
                $('#toggleClosed').text('Show Closed Tickets');
            }
           
        });*/
    })
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

