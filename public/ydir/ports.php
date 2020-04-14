<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)))
{
	header('Location: https://crypticvpn.com/ydir');
	die();
}


?>

<!DOCTYPE html>
<html lang="en">
  <title><?php echo $site_title; ?> - Port Management</title>
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
                
                <?
                if (isset($_GET['close']))
                        {
                            if (is_numeric($_GET['close'])){
                                $checkIfExists = $odb -> prepare("SELECT * FROM `ports` WHERE `id` AND status = 1");
                                $checkIfExists -> execute(array(':port' => $_GET['close']));
                                if($checkIfExists -> rowCount() == 0)
                                {
                                    header('location: dashboard.php');
                                    die();
                                } else {

                                    $id = $_GET['close'];
                                    $SQLGetInfo = $odb -> prepare("SELECT * FROM `ports` WHERE `id` = :id  AND status = 1 LIMIT 1");
                                    $SQLGetInfo -> execute(array(':id' => $id));
                                    $portInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
                                    $portnumber = $portInfo['port'];
                                    $internal_ip = $portInfo['internal_ip'];
                                    $server_id = $portInfo['server_id'];

                                    $rvpn -> saveAction($odb, 'closeport', $_SESSION['aID'], 0, $internal_ip, $portnumber);
                                    $rvpn -> closeVPNPort($odb, $server_id, $portnumber, $internal_ip, $encryptionKey);
                                    $SQL = $odb -> prepare("UPDATE `ports` SET  status = 0, dateClosed = UNIX_TIMESTAMP() WHERE `id` = :id");
                                    $SQL -> execute(array(':id' => $id));
                                    echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have closed the port successfully!</div>';
                                }
                            } else {
                                echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Invalid delete. </div>';
                            }
                        }
                    ?>

                        <div class="table-responsive">
                            <button style="float:right;" class="btn btn-medium btn-default" id="toggleClosed">Show Closed Ports</button>

                            <table id="datatable" class="table table-hover tickets">
                                <thead>
                                    <th> Owner </th> <th>Port</th> <th>Protocol</th> <th>Internal IP</th>  <th> Server</th>  <th>Date Opened </th> <th>Date Closed</th> <th>Status</th> </tr> 
                                </thead>
                                <tbody>
									
							<?php
								
									$GetUsers = $odb -> prepare("SELECT * FROM `ports` ORDER BY `id` DESC");
									$GetUsers -> execute();
									while ($getInfo = $GetUsers -> fetch(PDO::FETCH_ASSOC))
									{
										$id = $getInfo['id'];
                                        $port = $getInfo['port'];
                                        $portType = $getInfo['portType'];
                                        $userID = $getInfo['owner'];
                                        $ip = $getInfo['internal_ip'];
										$server_id = $getInfo['server_id'];
                                        $status = $getInfo['status'];
                                        $dateOpened = date("m-d-Y h:i:s A", $getInfo['dateOpened']);
                                        $closed = $getInfo['dateClosed'];

                                        if ($status = 0){
                                          $dateClosed = date("m-d-Y h:i:s A", $getInfo['closed']);
                                        }
                                        else{
                                            $dateClosed = "N/A";
                                        }

                                        switch($getInfo['status'])
										{
											case 1:
											$status = '<a class="btn btn-primary" href="ports.php?close='.$id.'">Close</a>';
											break;
											case 0:
											$status = 'Closed';
											break;
									   }

                                     //get server
                                        $getServer = $odb -> prepare("SELECT `name` FROM `servers` WHERE id = :id LIMIT 1");
                                        $getServer -> execute(array(':id' => $server_id));
                                        $serverName = $getServer ->  fetchColumn ( 0 );
                                    //get userName
                                        $getUsername = $odb -> prepare("SELECT `username` FROM `accounts` WHERE id = :id LIMIT 1");
                                        $getUsername -> execute(array(':id' => $userID));
                                        $userName = $getUsername ->  fetchColumn ( 0 );

									echo '
									<tr>
                                    <td> <a href="users.php?id=' . $userID . '"> ' . $userName. '</a> </td>
									<td>'.$port.'</td>
									<td>'.$portType.'</td>
									<td>'.$ip.'</td>
                                     <td>'.$serverName.'</td>
                                     <td>'.$dateOpened.'</td>
                                     <td>'.$dateClosed.'</td>
									 <td>'.$status.'</td>
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
        
        window.ordersTable.fnFilter('^((?!Closed).)*$', 6, true); 
        var hidingTimedOut = true;
        
        $('#toggleClosed').click(function() { 
            if(hidingTimedOut) {
                window.ordersTable.fnFilter('', 6, true); 
                hidingTimedOut = false;
                $('#toggleClosed').text('Hide Closed Ports');
            } else {
                window.ordersTable.fnFilter('^((?!Closed).)*$', 6, true); 
                hidingTimedOut = true;
                $('#toggleClosed').text('Show Closed Ports');
            }
           
        });
    })
</script>