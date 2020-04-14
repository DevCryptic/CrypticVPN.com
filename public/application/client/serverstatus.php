<?php

	require_once '../config.php';
	require_once '../global.php';	
	
	$GetServerStats = $odb -> prepare("SELECT * FROM `servers` ORDER BY `id` DESC LIMIT 100");
	$GetServerStats -> execute();
?>
<center><table><tbody><tr><th>Status</th><th>City</th><th>Country</th><th>IP</th></tr>
<?php
while ($getInfo = $GetServerStats -> fetch(PDO::FETCH_ASSOC))
{	
	$status = $getInfo['status'];


	if($status == 'offline'){
		$status = 'Offline';
	} elseif ($status > 0) {
		$status = 'Online';
	} else {
		$status = 'Unknown';
	}
?>	

<tr><td><?php echo $status; ?></td><td><?php echo $getInfo['city']; ?></td><td><?php echo $getInfo['country']; ?></td><td><?php echo $getInfo['ipaddress']; ?></td></tr>
<?php } ?>
	
</tbody></table></center>
