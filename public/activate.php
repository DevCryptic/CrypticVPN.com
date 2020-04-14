<?php
ob_start();
session_start();
require_once 'application/config.php';
require_once 'application/global.php';

if ($user -> IsActive($odb))
{
	header('Location: dashboard.php');
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
 <title><?php echo $site_title; ?> - Activate</title>   
<?php include('templates/head.php'); ?>
<body>
    <?php include('templates/header.php'); ?>
    <div class="container">
      <div class="wrap-main">
        <div class="main-contain col-sm-11">
            <div class="left-content col-sm-3">
                <div id="sidebar-wrapper" class="profile-sidebar">
				<!-- SIDEBAR USERPIC -->
				<div class="profile-userpic">
					<img src="assets/img/avatar.png" class="img-responsive side-avatar" alt="">
				</div>
				<!-- END SIDEBAR USERPIC -->
				<!-- SIDEBAR MENU -->
				<div class="profile-usermenu">
					<ul class="nav">
						<li class="<?php CheckPageA('dashboard.php') || CheckPageA('activate.php'); ?>">
							<a href="dashboard.php"> <i class="glyphicon glyphicon-home"></i> Home </a>
							<a href="logout.php">Logout</a>
						</li>											
					</ul>
				</div>	
			</div>
            </div>
            <div class="right-content col-sm-9">
                <ul class="nav nav-tabs responsive" id="mysetting">
                    <li class="test-class <?php if(empty($_GET['tab']) || $_GET['tab']=='activate') echo 'active'; ?>"><a class="deco-none misc-class" href="#activate"> Activation</a></li>
                </ul>
                <div class="tab-content responsive">
                    <?php
						
					?>
				<div class="tab-pane <?php if(empty($_GET['tab']) || $_GET['tab']=='activate') echo 'active'; ?>" id="activate">                                            
					<?php
				if (!isset($_GET['confirm']) && !isset($_GET['u'])) {
						echo '
						<div class="md-card">
			                <div class="md-card-content">
			                    <h3 class="heading_a">E-Mail Confirmation</h3>
			                    <div class="uk-grid uk-grid-divider" data-uk-grid-margin>
			                        <div class="uk-width-large-1-1 uk-width-medium-1-1">
			                            <ul class="md-list md-list-addon">
			                                <li>
			                                    <div class="md-list-addon-element">
			                                        
			                                    </div>
			                                    <div class="md-list-content">
			                                        <span class="md-list-heading">Please check your inbox!</span>
			                                        <span class="uk-text-small uk-text-muted">We have sent a confirmation mail to your inbox. Please check your inbox and click on the confirmation link to activate your account.</span>
													<span class="uk-text-small uk-text-muted">If you didn\'t receive a mail from us yet, please wait 15 minutes. Don\'t forget to check your Junk folder aswell.</span>
			                                    </div>
			                                </li>
			                            </ul>
			                        </div>
			                    </div>
			                    </div>
			              </div>';
					} else {
						$confirmation = htmlspecialchars($_GET['confirm']);
						$username = htmlspecialchars($_GET['u']);
						$SQLCheckActivation = $odb -> prepare("SELECT COUNT(*) FROM `accounts` WHERE `username` = :username AND `activation` = :activation");
						$SQLCheckActivation -> execute(array(':username' => $username, ':activation' => $confirmation));
						$activationCheck = $SQLCheckActivation -> fetchColumn(0);
						if ($activationCheck == 1)
							{
								$SQL = $odb -> prepare("UPDATE `accounts` SET `isactive` = 1 WHERE `username` = :username");
								$SQL -> execute(array(':username' => $username));
								echo '<div class="md-card">
					                <div class="md-card-content">
					                    <h3 class="heading_a">E-Mail Confirmation</h3>
					                    <div class="uk-grid uk-grid-divider" data-uk-grid-margin>
					                        <div class="uk-width-large-1-1 uk-width-medium-1-1">
					                            <ul class="md-list md-list-addon">
					                                <li>
					                                    <div class="md-list-addon-element">
					                                        
					                                    </div>
					                                    <div class="md-list-content">
					                                        <span class="md-list-heading">Success!</span>
					                                        <span class="uk-text-small uk-text-muted">You have confirmed your e-mail account successfully! You will be redirected to the login page shortly.</span>
					                                    </div>
					                                </li>
					                            </ul>
					                        </div>
					                    </div>
					                    </div>
					              </div><meta http-equiv="refresh" content="5;url=login.php">';
							} else {
								echo '<div class="md-card">
					                <div class="md-card-content">
					                    <h3 class="heading_a">E-Mail Confirmation</h3>
					                    <div class="uk-grid uk-grid-divider" data-uk-grid-margin>
					                        <div class="uk-width-large-1-1 uk-width-medium-1-1">
					                            <ul class="md-list md-list-addon">
					                                <li>
					                                    <div class="md-list-addon-element">
					                                        
					                                    </div>
					                                    <div class="md-list-content">
					                                        <span class="md-list-heading">Oops!</span>
					                                        <span class="uk-text-small uk-text-muted">Invalid activation link!</span>
					                                    </div>
					                                </li>
					                            </ul>
					                        </div>
					                    </div>
					                    </div>
					              </div>';
							}
					}
			?>
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