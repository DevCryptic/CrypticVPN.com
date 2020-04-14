<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)) && !($user -> IsStaff($odb)))
{
	header('Location: https://crypticvpn.com/ydir');
	die();
}


?>

<!DOCTYPE html>
<html lang="en">
 <title><?php echo $site_title; ?> - Support Desk</title>   
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
                                           		     <button style="float:right;" class="btn btn-medium btn-default" id="toggleClosed">Show Closed Tickets</button>

                            <table id="datatable" class="table table-hover tickets">
                                <thead>
                                    <tr> <th> ID </th> <th> Username</th> <th>Subject</th> <th>Status</th> <th>Manage</th> </tr> 
                                </thead>
                                <tbody>
									
									<?php
										$GetTickets = $odb -> prepare("SELECT * FROM `tickets` ORDER BY `id` DESC");
										$GetTickets -> execute();
										while ($getInfo = $GetTickets -> fetch(PDO::FETCH_ASSOC))
										{
											$id = $getInfo['id'];
											$senderid = $getInfo['senderid'];
											$department = $getInfo['department'];
											$getTitle = $getInfo['title'];
											if (strlen($getTitle) > 15) {
													substr($getTitle ,0,15).'...';
												}
											$title = $getTitle;
											switch($getInfo['status'])
											{
											case 1:
											$status = '<span class="badge btn-warning">Pending</span>';
											break;
											case 2:
											$status = '<span class="badge btn-success">Answered</span>';
											break;
											case 3:
											$status = '<span class="badge btn-danger">Closed</span>';
											break;
											}

										//get userName
										$getUsername = $odb -> prepare("SELECT `username` FROM `accounts` WHERE id = :id LIMIT 1");
										$getUsername -> execute(array(':id' => $senderid));
										$userName = $getUsername ->  fetchColumn ( 0 );

											echo '
											<tr>
												<td>'.$id.'</td>
                                  			    <td> <a href="users.php?id=' . $senderid . '"> ' . $userName. '</a> </td>
												<td>'.$title.' </td>
												<td>'.$status.'</td>
												<td><a class="btn btn-primary" href="viewticket.php?id='.$id.'">View</a></td>
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
            "order": [[ 3, "Pending" ]],
        });
        
        window.ordersTable.fnFilter('^((?!Closed).)*$', 3, true); 
        var hidingTimedOut = true;
        
        $('#toggleClosed').click(function() { 
            if(hidingTimedOut) {
                window.ordersTable.fnFilter('', 3, true); 
                hidingTimedOut = false;
                $('#toggleClosed').text('Hide Closed Tickets');
            } else {
                window.ordersTable.fnFilter('^((?!Closed).)*$', 3, true); 
                hidingTimedOut = true;
                $('#toggleClosed').text('Show Closed Tickets');
            }
           
        });
    })
</script>