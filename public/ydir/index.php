<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';
require_once('../application/recaptchalib.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $site_title; ?> - Admin Login</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./assets/favicon.ico">  
    <link href="../assets/css/font-style.css" rel="stylesheet"/>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="../assets/css/login.css" rel="stylesheet"/> 
</head>

<body class="bg_login">
    <main id="swapper">
        <section class="login">
            <div class="container">
                <div class="row div_row">
                    <div class="div_logo" align="center">
                        <a href="index.php"><img src="../assets/img/logo.png"></a>
                    </div>
                    <div class="box-login col-sm-6 col-sm-offset-3">
                        <form action="index.php" method="POST" name="form-login">
                            <?php 
								if (!($user -> IsAdmin($odb)))
				{
				if (isset($_POST['loginBtn']))
				{
					$username = $_POST['username'];
					$password = $_POST['password'];
					$errors = array();
					if (empty($username) || empty($password))
					{
						$errors[] = 'Please enter your username and password.';
					}
					if (!ctype_alnum($username) || strlen($username) < 4 || strlen($username) > 15)
					{
						$errors[] = 'Username must be 4-15 characters and alphanumeric only!';
					}

					if (empty($errors))
					{
						$SQLCheckLogin = $odb -> prepare("SELECT COUNT(*) FROM `admins` WHERE `username` = :username AND `password` = :password");
						$SQLCheckLogin -> execute(array(':username' => $username, ':password' => MD5($password)));
						$countLogin = $SQLCheckLogin -> fetchColumn(0);
						if ($countLogin == 1)
						{
							$SQLGetInfo = $odb -> prepare("SELECT `username`, `id` , `email` FROM `admins` WHERE `username` = :username AND `password` = :password");
							$SQLGetInfo -> execute(array(':username' => $username, ':password' => MD5($password)));
							$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
							$userid = $userInfo['id'];
							$email = $userInfo['email'];
							$userip = $_SERVER['REMOTE_ADDR'];
							$username = $userInfo['username'];
								$_SESSION['aUsername'] = $userInfo['username'];
								$_SESSION['aID'] = $userInfo['id'];
								$session_code = hash("sha512", $userid.$userip.$email.'85a689a6v8');
								$_SESSION['aSecret'] = $session_code;
									echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have logged in successfully. Redirecting..
														</div><meta http-equiv="refresh" content="3;url=dashboard.php">';

						}
						else
						{
							echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Incorrect username or password entered!</div>';
						}
					}
					else
					{
							echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
						foreach($errors as $error)
						{
							echo '-'.$error.'<br />';
						}
						echo '</div>';
					}
				}
			}
			else
			{
				header('location: dashboard.php');
			}
							?>
                            <div class="div_content col-xs-12 col-md-8">
                                <span class="span_icon_username"><img src="../assets/img/icon/icon_username.png"></span>
                                <input type="text" name="username" id="username" placeholder="Username" required/>
                            </div>
                            <div class="div_content col-xs-12 col-md-8">
                                <span class="span_icon_password"><img src="../assets/img/icon/icon_password.png"></span>
                                <input type="password" name="password" id="password" placeholder="Password" required/>
                            </div>
                            <div class="div_content col-xs-12 col-md-8">
                                <label class="lb_remember"><input type="checkbox" name="remember" id="cb_remember" value="1" />Remember Me</label>
                            </div>
                   			<div class="div_content col-xs-12 col-md-8">
                                <input type="submit" name="loginBtn" id="sm_account" value="Login" />
                            </div>
                       	    </form>
                    </div>
                    <?php include('../templates/guest_footer.php'); ?>                                      
                </div>
            </div>
        </section>
    </main>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>