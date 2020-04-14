<?php
ob_start();
require_once 'application/config.php';
require_once 'application/global.php';

if (!($user -> IsLogged($odb)))
{
	header('Location: login.php');
	die();
}
if ($user -> IsBanned($odb))
{
	header('Location: logout.php');
	die();
}
if (!($user -> IsActive($odb)))
{
	header('Location: activate.php');
	die();
}

if (!($user->hasVPN($odb))) {
	header('location: dashboard.php?error=1');
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
 <title><?php echo $site_title; ?> - VPN Control Panel</title>   
<?php include('templates/head.php'); ?>
<body>
    <?php include('templates/header.php'); ?>
    <div class="container">
      <div class="wrap-main">
        <div class="main-contain col-sm-11">
            <div class="left-content col-sm-3">
                <?php include('templates/sidebar_left.php'); ?>
            </div>
            <div class="right-content col-sm-9">
                <ul class="nav nav-tabs responsive" id="mysetting">
                    <li class="test-class <?php if(empty($_GET['tab']) || $_GET['tab']=='servicestats') echo 'active'; ?>"><a class="deco-none misc-class" href="#servicestats"> Service Stats</a></li>
                    <li class="test-class <?php if(isset($_GET['tab']) && $_GET['tab']=='openport') echo 'active'; ?>"><a href="#openport">Open Port</a></li>
					<li class="test-class <?php if(isset($_GET['tab']) && $_GET['tab']=='closeport') echo 'active'; ?>"><a href="#closeport">Close Port</a></li>
					<li class="test-class <?php if(isset($_GET['tab']) && $_GET['tab']=='pass') echo 'active'; ?>"><a href="#pass">Change Password</a></li>
                </ul>
                <div class="tab-content responsive">
                    <?php
						if (isset($_POST['portfw']))
						{
							$port = $_POST['port'];
							$portType = $_POST['portType'];
							$internal = $_POST['internal'];
							$serverid = $_POST['server'];
							$exploded = explode(".", $internal);

							if (empty($port) || empty($internal) || empty($serverid) || empty($portType)){
								$errors[] = 'Please fill in all fields';
							}
							if (!is_numeric($serverid)){
								$errors[] = 'Please select a server from the list.';
							}
							if (!is_numeric($port)){
								$errors[] = 'Please enter a valid port number.';
							}
							if (($portType!="tcp") && ($portType!="udp"))
								$errors[] = 'Please enter a valid port type.';

							if (($port<1) || ($port>65535)){
								$errors[] = 'You have entered an invalid port number. Only ports 1-65535 are permissible.';
							}

							if (!filter_var($internal, FILTER_VALIDATE_IP) || !in_array(implode(".", [$exploded[0], $exploded[1], $exploded[2]]), ["10.8.0", "10.8.1"])) 
							{
								$errors[] = 'You have entered an invalid IP.';
							}
							$checkBlacklist = $odb->prepare("SELECT COUNT(*) FROM `blacklist` WHERE `port` = :port");
							$checkBlacklist -> execute(array(':port' => $port));
							$IsBlacklisted = $checkBlacklist->fetchColumn(0);
							if(!($IsBlacklisted == 0))
							{
								$errors[] = 'This port is black listed.';
							}

							$checkIfExists = $odb -> prepare("SELECT * FROM `servers` WHERE `id` = :serverid AND NOT `status` = :status");
							$checkIfExists -> execute(array(':serverid' => $serverid, ':status' => 'offline'));
							if($checkIfExists -> rowCount() == 0)
							{
								$errors[] = 'Please select a server from the list.';
							}

							$checkPort = $odb -> prepare("SELECT * FROM `ports` WHERE `server_id` = :serverid AND `port` = :port AND `portType` = :portType AND `status` = 1");
							$checkPort -> execute(array(':serverid' => $serverid, ':port' => $port, ':portType' => $portType));
							if($checkPort -> rowCount() > 0)
							{
								$errors[] = 'The port you have entered is already opened by someone else. Please try another one.';
							}

							if (empty($errors))
							{
								//$rvpn -> saveAction($odb, 'openport', $_SESSION['rID'], 0, $internal, $port);
								$rvpn -> openVPNPort($odb, $serverid, $port, $portType, $internal, $encryptionKey);
								$SQL = $odb -> prepare("INSERT INTO `ports` VALUES(NULL, :owner, :port, :internal, :serverid, UNIX_TIMESTAMP(), 0, 1, :portType)");
								$SQL -> execute(array(':owner' => $_SESSION['rID'], ':port' => $port, ':internal' => $internal, ':serverid' => $serverid, ':portType' => $portType));
								echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have opened the port successfully.</div>';
							}
							else
							{
								echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
								foreach($errors as $error)
								{
									echo '- '.$error.'<br />';
								}
								echo '</div>';
							}
						}

						if (isset($_GET['close']))
						{
							if (is_numeric($_GET['close'])){
								$checkIfExists = $odb -> prepare("SELECT * FROM `ports` WHERE `id` = :port AND `owner` = :owner");
								$checkIfExists -> execute(array(':port' => $_GET['close'], ':owner' => $_SESSION['rID']));
								if($checkIfExists -> rowCount() == 0)
								{
									header('location: dashboard.php');
									die();
								} else {

									$delete = $_GET['close'];

									$SQLGetInfo = $odb -> prepare("SELECT * FROM `ports` WHERE `id` = :id AND `owner` = :owner LIMIT 1");
									$SQLGetInfo -> execute(array(':id' => $delete, ':owner' => $_SESSION['rID']));
									$portInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
									$portnumber = $portInfo['port'];
									$portType = $portInfo['portType'];
									$internal_ip = $portInfo['internal_ip'];
									$server_id = $portInfo['server_id'];

									$rvpn -> saveAction($odb, 'closeport', $_SESSION['rID'], 0, $internal_ip, $portnumber);
									$rvpn -> closeVPNPort($odb, $server_id, $portnumber, $internal_ip, $encryptionKey, $portType);
									$SQL = $odb -> prepare("UPDATE `ports` SET  status = 0, dateClosed = UNIX_TIMESTAMP() WHERE `id` = :id AND `owner` = :owner");
									$SQL -> execute(array(':id' => $delete, ':owner' => $_SESSION['rID']));
									echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have closed the port successfully!</div>';
								}
							} else {
								echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Invalid delete. </div>';
							}
						}
						if (isset($_POST['changeVPass']))
						{
							$newPassword = $_POST['npassword'];

							if (preg_match('/^\p{Xan}+$/', $newPassword)) {
							    echo "Looks good\n";
							    $checkReset = $odb -> prepare("SELECT * FROM `vpnresets` WHERE `userid` = :userid");
										$checkReset -> execute(array(':userid' => $_SESSION['rID']));
										if($checkReset -> rowCount() == 0)
										{
											$SQL = $odb -> prepare("INSERT INTO `vpnresets` VALUES(NULL, :userid, UNIX_TIMESTAMP())");
											$SQL -> execute(array(':userid' => $_SESSION['rID']));
											$rvpn -> changeVPNPass($rad, $_SESSION['rID'], $newPassword, $encryptionKey);
										} else {
											$SQLGetDate = $odb -> prepare("SELECT * FROM `vpnresets` WHERE `userid` = :userid LIMIT 1");
											$SQLGetDate -> execute(array(':userid' => $_SESSION['rID']));
											$dateInfo = $SQLGetDate -> fetch(PDO::FETCH_ASSOC);
											$lastUpdateCheck = strtotime('+30 minutes', $dateInfo['date']);
											$currentDate = time();

											if ($lastUpdateCheck > $currentDate)
											{
												echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>You can change your password once every 30 minutes (This will be changed to 1 hour when the site is live). The last time you changed your password was '.date('m/d/Y H:i:s', $lastUpdateCheck).'</div>';
											} else {
												$updateDate = $odb -> prepare("UPDATE `vpnresets` SET `date` = UNIX_TIMESTAMP() WHERE `userid` = :userid");
												$updateDate -> execute(array(':userid' => $_SESSION['rID']));
												$rvpn -> saveAction($odb, 'changepass', $_SESSION['rID'], $newPassword, 0, 0);
												$rvpn -> changeVPNPass($rad, $_SESSION['rID'], $newPassword, $encryptionKey);
												echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have changed your vpn password successfully!</div>';
											}
										}
							} else {
							    echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Invalid Characters. Your VPN password can only consist of letters and numbers</div>';
							}

										
						}
					?>
				<div class="tab-pane <?php if(empty($_GET['tab']) || $_GET['tab']=='servicestats') echo 'active'; ?>" id="servicestats">                                            
                   <div class="table-responsive">
					<table class="table table-hover tickets">
						<thead>
							<tr> <th>Country</th> <th>City</th> <th>Status</th> </tr> 
						</thead>
						<tbody>
							
							<?php
								$GetServerStats = $odb -> prepare("SELECT * FROM `servers` ORDER BY `country` LIMIT 100");
								$GetServerStats -> execute();
								while ($getInfo = $GetServerStats -> fetch(PDO::FETCH_ASSOC))
								{
									$id = $getInfo['id'];
									$country = $getInfo['country'];
									$city = $getInfo['city'];
									$status = $getInfo['status'];

									if($status == 'offline'){
										$status = '<span class="badge btn-danger">Offline</span>';
									} elseif ($status > 0) {
										$status = '<span class="badge btn-success">Online</span>';
									} else {
										$status = '<span class="badge btn-warning">Unknown</span>';
									}
									echo '
										<tr>
											<td class="text-center">'.$country.'</i></td>
											<td>'.$city.'</td>
											<td class="text-center">'.$status.'</td>
										</tr>
									';
								}
							?>
							
						</tbody>
					</table>  
				</div>  
                </div>
				<div class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab']=='openport') echo 'active'; ?>" id="openport"> 
					If you require assistance with port forwarding <a href="https://www.youtube.com/watch?v=NkK98bEwFhk">click here</a> to watch a step-by-step tutorial. TCP is default, use UDP only if the application you want to use it with specifically requires you to use UDP ports.<p>
					<form method="post">
					<div class="form-group">
						<label for="portType">Protocol <span class="text-red">*</span></label>
						<select class="form-control" name="portType" id="portType" required>
							<option value="tcp" selected="selected">TCP (Default)</option>
							<option value="udp">UDP</option>
						</select>
					  </div>   	
					  <div class="form-group">
						<label for="subject">Port <span class="text-red">*</span></label>
						<input type="text" class="form-control" id="subject" name="port" required>
					  </div>
					  <div class="form-group">
						<label for="internal">Internal IP <span class="text-red">(Your IP should be 10.8.0.x if you are using UDP or 10.8.1.x if you are using TCP.)</span></label>
						<input type="text" class="form-control" id="internal" name="internal" value="10.8." required>
					  </div>
					  <div class="form-group">
						<label for="location">Select Location <span class="text-red">*</span></label>
						<select class="form-control" name="server" id="location" required>
							<?php
								$ServerList = $odb -> prepare("SELECT * FROM `servers` WHERE pfEnabled =1 AND NOT `status` = :status ORDER BY `country` LIMIT 100");
								$ServerList -> execute(array(':status' => 'offline'));
								while ($getInfo = $ServerList -> fetch(PDO::FETCH_ASSOC))
								{
									$serverid = $getInfo['id'];
									$country = $getInfo['country'];
									$city = $getInfo['city'];

									echo '<option value="'.$serverid.'">'.$country.' - '.$city.'</option>';
								}
							?>
						</select>
					  </div>                        
					  <button type="submit" name="portfw" value="1" class="btn btn-primary full-width">Submit</button>
					</form> 
				</div>                    

				<div class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab']=='closeport') echo 'active'; ?>" id="closeport"> 
					<div class="table-responsive">
						<table class="table table-hover tickets">
							<thead>
								<tr> <th>Server </th> <th>Internal IP</th> <th>Port Number</th> <th>Protocol</th> <th>Manage</th> </tr> 
							</thead>
							<tbody>
								
								<?php
									$GetPorts = $odb -> prepare("SELECT * FROM `ports` WHERE `owner` = :owner AND `status` = 1 ORDER BY `id` LIMIT 100");
									$GetPorts -> execute(array(':owner' => $_SESSION['rID']));
									while ($getInfo = $GetPorts -> fetch(PDO::FETCH_ASSOC))
									{
										$id = $getInfo['id'];
										$serverid = $getInfo['server_id'];
										$internal = $getInfo['internal_ip'];
										$portType = $getInfo['portType'];
										$port = $getInfo['port'];
											$SQLGetServer = $odb -> prepare("SELECT * FROM `servers` WHERE `id` = :id");
											$SQLGetServer -> execute(array(':id' => $serverid));
											$sInfo = $SQLGetServer -> fetch(PDO::FETCH_ASSOC);
											$servercountry = $sInfo['country'];
											$servercity = $sInfo['city'];
											$status = $sInfo['status'];
											if ($status == 'offline') {
												$status = '<a class="btn btn-primary" disabled >Try Later</a>';
											} else {
												$status = '<a class="btn btn-danger" href="vpn.php?close='.$id.'">Close Port</a>';
											}
										echo '
										<tr>
											<td>'.$servercountry.' - '.$servercity.'</td>
											<td>'.$internal.' </td>
											<td>'.$port.'</td>
											<td>'.strtoupper($portType).'</td>
											<td>'.$status.'</td>
										</tr>';
									}
								?>
								
							</tbody>
						</table>  
					</div>  
				</div>      
				<div class="tab-pane <?php if(isset($_GET['tab']) && $_GET['tab']=='pass') echo 'active'; ?>" id="pass"> 
					<form method="post">
					<p><strong>You can change your VPN password once every 30 minutes. Please click the button below to change your password:</strong></p>
					<p class="text-danger">IMPORTANT: Your VPN password can only contain letters and numbers.</p>
						<div class="form-group">
						<label for="subject">New Password</label>
						<input type="text" class="form-control" id="password" name="npassword" value="" required>
					  </div> 
					<button type="submit" name="changeVPass" class="btn btn-primary full-width">Submit</button>
					</form>
				</div> 
                </div>                          
            </div>
        </div>
      </div>
    </div><!-- /.container -->

    <?php include('templates/footer.php'); ?>
	<?php include('templates/scriptjs.php'); ?>
</body>
</html>