<div id="sidebar-wrapper" class="profile-sidebar">
	<!-- SIDEBAR USERPIC -->
	<div class="profile-userpic">
		<img src="/assets/img/avatar.png" class="img-responsive side-avatar" alt="">
	</div>
	<!-- END SIDEBAR USERPIC -->
	<!-- SIDEBAR USER TITLE -->
	<div class="profile-usertitle">
		Welcome <a href="#"><?php echo $_SESSION['aUsername']; ?></a>!
	</div>
	<!-- END SIDEBAR USER TITLE -->	
	<!-- SIDEBAR MENU -->
	<div class="profile-usermenu">
		<ul class="nav">
			<!--<i class="glyphicon glyphicon-home"></i> Home </a>
			 <i class="glyphicon glyphicon-th">
			 <i class="glyphicon glyphicon-bookmark"></i> My VPN Server</a></li>
				<i class="glyphicon glyphicon-send"></i> Support Desk</a>
				<i class="glyphicon glyphicon-edit"></i> Account Settings </a>
			</li>-->
			

			<?php if(($user -> IsAdmin($odb)) || ($user -> IsStaff($odb))){?>
			<li class="">
			<a href="dashboard.php"> <i class="glyphicon glyphicon-globe"></i> Dashboard </a>
			<a href="tickets.php"> <i class="glyphicon glyphicon-send"></i> View Tickets </a>
			<?php } ?>

			

			<?php if($user -> IsAdmin($odb)) {?>
				<a href="users.php"> <i class="glyphicon glyphicon-search"></i> Manage Users </a>
				<a href="servers.php"> <i class="glyphicon glyphicon-cloud"></i> Manage Servers </a>
				<a href="ports.php"> <i class="glyphicon glyphicon-random"></i> Manage Ports </a>
				<a href="packages.php"> <i class="glyphicon glyphicon-inbox"></i> Manage Packages </a>
				<a href="payments.php"> <i class="glyphicon glyphicon-usd"></i> Payments </a>
				<a href="blacklist.php"> <i class="glyphicon glyphicon-usd"></i> Port Blacklist </a>
				<a href="giftcodes.php"> <i class="glyphicon glyphicon-usd"></i> Giftcodes </a>

			<?php }?>

			</li>

		</ul>
	</div>	
	<!-- END MENU -->
	<a class="btn btn-primary btn-logout" href="logout.php">Logout</a>
</div>