<?php
ob_start();
require_once '../application/config.php';
require_once '../application/global.php';
?>
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Remove Tap Highlight on Windows Phone IE -->
    <meta name="msapplication-tap-highlight" content="no"/>

    <link rel="icon" type="image/png" href="assets/img/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="assets/img/favicon-32x32.png" sizes="32x32">

    <title><?php echo $site_title; ?> - Admin Login</title>

    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- uikit -->
    <link rel="stylesheet" href="bower_components/uikit/css/uikit.almost-flat.min.css"/>

    <!-- altair admin login page -->
    <link rel="stylesheet" href="assets/css/login_page.min.css" />

</head>
<body class="login_page">

    <div class="login_page_wrapper">
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
								echo '<div class="uk-alert uk-alert-success" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> You have logged in successfully. Redirecting..
									</div><meta http-equiv="refresh" content="3;url=index.php">';

						}
						else
						{
							echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a> Incorrect username or password entered!</div>';
						}
					}
					else
					{
						echo '<div class="uk-alert uk-alert-danger" data-uk-alert><a href="#" class="uk-alert-close uk-close"></a>';
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
				header('location: index.php');
			}
		?>
        <div class="md-card" id="login_card">
            <div class="md-card-content large-padding" id="login_form">
                <div class="login_heading">
                    <div class="user_avatar"></div>
                </div>
                <form method="post">
                    <div class="uk-form-row">
                        <label for="login_username">Username</label>
                        <input class="md-input" type="text" id="login_username" name="username" />
                    </div>
                    <div class="uk-form-row">
                        <label for="login_password">Password</label>
                        <input class="md-input" type="password" id="login_password" name="password" />
                    </div>
                    <div class="uk-margin-medium-top">
                        <button type="submit" name="loginBtn" class="md-btn md-btn-primary md-btn-block md-btn-large">Sign In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- common functions -->
    <script src="assets/js/common.min.js"></script>
    <!-- altair core functions -->
    <script src="assets/js/altair_admin_common.min.js"></script>

    <!-- altair login page functions -->
    <script src="assets/js/pages/login.min.js"></script>

    <!-- uikit functions -->
    <script src="assets/js/uikit_custom.min.js"></script>

</body>
</html>
