<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';

if (!($user -> IsAdmin($odb)))
{
	header('Location: https://crypticvpn.com/ydir');
	die();
}

if (isset($_GET['viewServer'])) {//Check if the server exists.
    $serverid = $_GET['viewServer'];
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
}

?>

?>

<!DOCTYPE html>
<html lang="en">
  <title><?php echo $site_title; ?> - Server Management</title>
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
                        echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Update Success.</div>';
                    }
                    else
                    {
                        echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> No changes were made.</div>';
                    }
                }
                if (isset($_POST['deleteServer'])) {
                    $sql = $odb -> prepare("DELETE FROM `servers` WHERE `id` = :id");
                    $sql -> execute(array(':id' => $serverid));

                    $sql1 = $odb -> prepare("DELETE FROM `ports` WHERE `server_id` = :id");
                    $sql1 -> execute(array(':id' => $serverid));

                    echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>The server has been deleted!</div>';
                }
                ?>

                <?php if (isset($_GET['viewServer'])) { ?>
                
                 <div class="tab-content responsive">
                   <div class="table-responsive">
                    <form method="post">
                        <div class="form-group">
                        <label for="subject">Server IP</label>
                        <input type="text" class="form-control" name="ipaddr" id="ipaddr" value="<?php echo $ipaddr; ?>">
                      </div> 
                      <div class="form-group">
                        <label for="subject">Server Country</label>
                        <input type="text" class="form-control" id="country" name="country" value="<?php echo $country; ?>">
                      </div> 
                      <div class="form-group">
                        <label for="subject">Server City</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?php echo $city; ?>">
                      </div> 
                     <div class="form-group">
                        <label for="subject">Server Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>">
                      </div> 
                      <div class="form-group">
                        <label for="subject">Server Password</label>
                        <input type="text" class="form-control" id="password" name="password">
                      </div>                
                      <div class="form-group">
                        <input id="option2" name="pfEnabled" type="checkbox" <?php if ($pfEnabled == 1) { echo 'checked'; } ?>>
                        <label class="checkbox" for="option2"> Port Forwarding Enabled </label>
                      </div>                  
                        <button type="submit" name="updateServer">Update</button>
                        <button type="submit" name="wipePorts">Wipe Ports</button>
                        <button type="submit" name="deleteServer" onclick="return confirm('Are you sure you want to delete this server?');">Remove Server</button>                    
                    </form>   
                Open Ports
                     <table id="datatable" class="table table-hover tickets">
                                <thead>
                                    <th> Owner </th> <th>Port</th> <th>Internal IP</th>  <th> Server</th>  <th>Date Opened </th> <th>Date Closed</th> <th>Status</th> </tr> 
                                </thead>
                                <tbody>
                                    
                            <?php
                                
                                    $GetUsers = $odb -> prepare("SELECT * FROM `ports` where `server_id` = :server_id AND `status` = 1 ORDER BY `id` DESC");
                                    $GetUsers -> execute(array(':server_id' => $serverid));
                                    while ($getInfo = $GetUsers -> fetch(PDO::FETCH_ASSOC))
                                    {
                                        $id = $getInfo['id'];
                                        $port = $getInfo['port'];
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
                <?php } elseif (isset($_GET['addServer'])) { ?>
                <?
if (isset($_POST['addServer'])) {
    $ipaddr = $_POST['ipaddr'];
    $name = $_POST['name'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $dtreason = $_POST['dtreason'];
    $errors = array();

        if (empty($ipaddr) || empty($name) || empty($country) || empty($city) || empty($username) || empty($password) || empty($dtreason))
        {
            $errors[] = 'Please fill in all fields.';
        }

        if(empty($errors)) {
            $encryptedPass = encryptData($password, $encryptionKey);
            $insertServer = $odb -> prepare("INSERT INTO `servers` VALUES (NULL, :host, :country, :city, :name, :username, :password, 1, :dtreason, 1)");
            $insertServer -> execute(array(':host' => $ipaddr, ':country' => $country, ':city' => $city, ':name' => $name, ':username' => $username, ':password' => encryptData($password, $encryptionKey), ':dtreason' => $dtreason));
            
            
            try { 
                $SQLGetUsers = $odb -> prepare("SELECT * FROM `accounts` WHERE NOT `rVPN` = :rvpn AND `expire` > UNIX_TIMESTAMP()");
                $SQLGetUsers -> execute(array(':rvpn' => '0'));
                $hupdate = new Net_SSH2($ipaddr);
                while($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC))
                {
                    $userid = $getInfo['id'];
                    $userpass = $getInfo['rVPN'];

                    $command = 'useradd [userid] -s /bin/false; echo -e "[password]\n[password]" | passwd [userid]';
                    $command = str_replace("[userid]", $userid, $command);
                    $command = str_replace("[password]", $userpass, $command);
                    $hupdate->login($username, $password);
                    $hupdate->setTimeout(30);
                    $hupdate->exec($command);
                }
            } catch (Exception $e) { 
                echo 'Caught Exception: ',  $e->getMessage(), "\n"; 
            } 
        echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have successfully added a server..</div>';
        } else {
        echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Error</div>';
            foreach($errors as $error)
            {
                echo '- '.$error.'<br />';
            }
            echo '</div>';
        }
}
                ?>
                <div class="tab-content responsive">
                   <div class="table-responsive">
                    <form method="post">
                        <div class="form-group">
                        <label for="subject">Server IP</label>
                        <input type="text" class="form-control" name="ipaddr" id="ipaddr">
                      </div> 
                      <div class="form-group">
                        <label for="subject">Server Country</label>
                        <input type="text" class="form-control" id="country" name="country">
                      </div> 
                      <div class="form-group">
                        <label for="subject">Server City</label>
                        <input type="text" class="form-control" id="city" name="city">
                      </div> 
                      <div class="form-group">
                        <label for="subject">Server Name</label>
                        <input type="text" class="form-control" id="name" name="name">
                      </div> 
                     <div class="form-group">
                        <label for="subject">Server Username</label>
                        <input type="text" class="form-control" id="username" name="username">
                      </div> 
                      <div class="form-group">
                        <label for="subject">Server Password</label>
                        <input type="text" class="form-control" id="password" name="password">
                      </div>   
                    <div class="form-group">
                        <label for="subject">Downtime Reason</label>
                        <input type="text" class="form-control" id="dtreason" name="dtreason" value="-">
                      </div>                             
                        <button type="submit" name="addServer">Submit</button>
               
                    </form>   

                </div>  

                </div>
                </div>   

                
                </div>


                <?php } else { ?>

                        <a href="?addServer">Add Server</a>
                        <div class="table-responsive">

                            <table id="datatable" class="table table-hover tickets">
                                <thead>
                                    <tr>
                                        <th>IP Address</th>
                                        <th>City</th>
                                        <th>Country</th>
                                        <th>Status</th>
                                        <th>Port Forwarding</th>
                                        <th>Manage</th>
                                    </tr>
                                    </thead>
                                <tbody>
									
							<?php
                                $GetServers = $odb -> prepare("SELECT * FROM `servers` ORDER BY `status` DESC");
                                $GetServers -> execute();
                                while ($getInfo = $GetServers -> fetch(PDO::FETCH_ASSOC))
                                {
                                    $id = $getInfo['id'];
                                    $ipaddr = $getInfo['ipaddress'];
                                    $city = $getInfo['city'];
                                    $country = $getInfo['country'];
                                    $status = $getInfo['status'];    
                                    $portForwarding = $getInfo['pfEnabled'];
                                    
                                    if($status == 'offline'){
                                        $status = '<p style="color:red">Offline</p>';
                                    } elseif ($status > 0) {
                                        $status = 'Online';
                                    } else {
                                        $status = 'Unknown';
                                    }

                                    if ($portForwarding == 1)
                                        $portForwarding = "Enabled";
                                    else
                                        $portForwarding = "Disabled";

                                    echo '
                                    <tr>
                                        <td>'.$ipaddr.'</td>
                                        <td>'.$city.' </td>
                                        <td>'.$country.'</td>
                                        <td>'.$status.'</td>
                                        <td>'.$portForwarding.'</td>
                                        <td><a class="md-btn md-btn-primary" href="servers.php?viewServer='.$id.'">View</a></td>
                                    </tr>';
                                }
                            ?>
                            </tbody>
                            </table>  
                        </div>  <?php } ?>
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

       <!-- <script type="text/javascript">
    $(document).ready(function(){
        window.ordersTable = $('#datatable').dataTable({
            "order": [[ 3, "Active" ]],
        });
        
        window.ordersTable.fnFilter('^((?!Banned).)*$', 3, true); 
        var hidingTimedOut = true;
        
        $('#toggleClosed').click(function() { 
            if(hidingTimedOut) {
                window.ordersTable.fnFilter('', 3, true); 
                hidingTimedOut = false;
                $('#toggleClosed').text('Hide Banned Users');
            } else {
                window.ordersTable.fnFilter('^((?!Banned).)*$', 3, true); 
                hidingTimedOut = true;
                $('#toggleClosed').text('Show Banned Users');
            }
           
        });
    })
</script>-->
</html>

