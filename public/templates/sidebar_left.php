<div id="sidebar-wrapper" class="profile-sidebar">
	<!-- SIDEBAR USERPIC -->
	<div class="profile-userpic">
		<img src="/assets/img/avatar.png" class="img-responsive side-avatar" alt="">
	</div>
	<!-- END SIDEBAR USERPIC -->
	<!-- SIDEBAR USER TITLE -->
	<div class="profile-usertitle">
		Welcome <a href="#"><?php echo $_SESSION['rUsername']; ?></a>!
	</div>
	<!-- END SIDEBAR USER TITLE -->	
	<!-- SIDEBAR MENU -->
	<div class="profile-usermenu">
		<ul class="nav">
			<li class="<?php CheckPageA('dashboard.php'); ?>">
				<a href="dashboard.php"> <i class="glyphicon glyphicon-home"></i> Home </a>
			</li>											
			<li class="<?php CheckPageA('vpn.php'); ?>"><a href="vpn.php"> <i class="glyphicon glyphicon-th"></i> VPN Control Panel</a></li>
			<?php
			  if ($user->haveDedicated($odb)) {
			?>
			<li class="<?php CheckPageA('dedicated.php') || CheckPageA('viewdedicated.php'); ?>"><a href="dedicated.php"> <i class="glyphicon glyphicon-bookmark"></i> My VPN Server</a></li>
			<?php
                }
			?>
			<li class="<?php CheckPageA('support.php') || CheckPageA('viewticket.php'); ?>">
				<a href="support.php"> <i class="glyphicon glyphicon-send"></i> Support Desk</a>
			</li>	
			<li class="<?php CheckPageA('account.php'); ?>">
				<a href="account.php"> <i class="glyphicon glyphicon-edit"></i> Account Settings </a>
			</li>
			<?php if($user -> IsAdmin($odb)) {?>
			<li class="">
				<a href="ydir/index.php"> <i class="glyphicon glyphicon-globe"></i> Admin CP </a>
			</li>
			<?php }elseif($user -> IsStaff($odb)) {?>
			<li class="">
				<a href="ydir/index.php"> <i class="glyphicon glyphicon-question-sign"></i> Staff Tickets </a>
			</li>
			<?php } ?>			
		</ul>
	</div>	
	<!-- END MENU -->
	<a class="btn btn-primary btn-logout" href="logout.php">Logout</a>
</div>