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

$SQLGetInfo = $odb -> prepare("SELECT `receive_dtinfo`,`special_offers` FROM `accounts` WHERE `id` = :id LIMIT 1");
$SQLGetInfo -> execute(array(':id' => $_SESSION['rID']));
$userIfno = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
$dtnotification = $userIfno['receive_dtinfo'];
$specialoffers = $userIfno['special_offers'];
?>
<!DOCTYPE html>
<html lang="en">
 <title><?php echo $site_title; ?> - My Account</title>   
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
                    <li class="test-class <?php if(empty($_GET['tab']) || $_GET['tab']=='account') echo 'active'; ?>"><a class="deco-none misc-class" href="#account"> Manage Account</a></li>
                </ul>
                <div class="tab-content responsive">
                    <?php
				if (isset($_POST['updateSettings'])) {
					$cpassword = $_POST['cpassword'];
					$npassword = $_POST['npassword'];
					$rpassword = $_POST['rpassword'];
					if (!empty($cpassword) && !empty($npassword) && !empty($rpassword))
					{
						if ($npassword == $rpassword)
						{
							$shanpass = hash("sha512", $npassword);
							$SQLCheckCurrent = $odb -> prepare("SELECT COUNT(*) FROM `accounts` WHERE `username` = :username AND `password` = :password");
							$SQLCheckCurrent -> execute(array(':username' => $_SESSION['rUsername'], ':password' => MD5($cpassword)));
							$countCurrent = $SQLCheckCurrent -> fetchColumn(0);
							if ($countCurrent == 1)
							{
								$SQLUpdate = $odb -> prepare("UPDATE `accounts` SET `password` = :password WHERE `username` = :username AND `id` = :id");
								$SQLUpdate -> execute(array(':password' => MD5($npassword),':username' => $_SESSION['rUsername'], ':id' => $_SESSION['rID']));
								echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have updated your password successfully.</div>';
							}
							else
							{
								echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Your current password is not valid.</div>';
							}
						}
						else
						{
							echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> New passwords does not match.</div>';
						}
					}

					if ($specialoffers == 1 && !isset($_POST['specialoffers'])) {
						$SQL = $odb -> prepare("UPDATE `accounts` SET `special_offers` = 0 WHERE `id` = :id");
						$SQL -> execute(array(':id' =>  $_SESSION['rID']));
						$specialoffers = 0;
						echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You are now unsubscribed to our special offers mailing list.</div>';
					}
					if ($specialoffers == 0 && isset($_POST['specialoffers'])) {
						$SQL = $odb -> prepare("UPDATE `accounts` SET `special_offers` = 1 WHERE `id` = :id");
						$SQL -> execute(array(':id' =>  $_SESSION['rID']));
						$specialoffers = 1;
						echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have subscribed to our special offers mailing list.</div>';
					}

					if ($dtnotification == 0 && isset($_POST['dtnotification'])) {
						$SQL = $odb -> prepare("UPDATE `accounts` SET `receive_dtinfo` = 1 WHERE `id` = :id");
						$SQL -> execute(array(':id' =>  $_SESSION['rID']));
						$dtnotification = 1;
						echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have subscribed to our downtime notifications mailing list.</div>';
					}
					if ($dtnotification == 1 && !isset($_POST['dtnotification'])) {
						$SQL = $odb -> prepare("UPDATE `accounts` SET `receive_dtinfo` = 0 WHERE `id` = :id");
						$SQL -> execute(array(':id' =>  $_SESSION['rID']));
						$dtnotification = 0;
						echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have unsuscribed to our downtime notifications mailing list.</div>';
					}

				}
			?>                             
				<div class="tab-pane <?php if(empty($_GET['tab']) || $_GET['tab']=='account') echo 'active'; ?>" id="account">                                            
                   <div class="table-responsive">
					<form method="post">
					  <div class="form-group">
						<label for="subject">Current Password</label>
						<input type="password" class="form-control" id="password" name="cpassword" value="">
					  </div> 
					  <div class="form-group">
						<label for="subject">New Password</label>
						<input type="password" class="form-control" id="password" name="npassword" value="">
					  </div> 
					  <div class="form-group">
						<label for="subject">Confirm Password</label>
						<input type="password" class="form-control" id="confirm_password" name="rpassword" value="">
					  </div>   
					  <div class="form-group">
						<input id="option2" name="specialoffers" type="checkbox" <?php if ($specialoffers == 1) { echo 'checked'; } ?>>
						<label class="checkbox" for="option2"> Receive Special Offers </label>
						<input id="option3" name="dtnotification" type="checkbox" <?php if ($dtnotification == 1) { echo 'checked'; } ?>>
						<label class="checkbox" for="option3"> Receive Downtime Notifications </label>
					  </div>       			  
					  <button type="submit" name="updateSettings" value="1" class="btn btn-primary">Save</button>
					</form>   
				</div>  
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