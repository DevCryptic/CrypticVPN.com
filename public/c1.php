<?php
require_once 'application/config.php';
require_once 'application/global.php';

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

if(isset($_POST['contact'])){
        if(empty($_POST['yourname']) || empty($_POST['email']) || empty($_POST['message']) || empty($_POST['reason'])){
            $errors = 'Please enter full the required fields.';
        }elseif(!validEmail($_POST['email'])){
            $errors = 'Error! Email invalid.';
        }else{
             $captcha = $_POST['g-recaptcha-response'];
								$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$rprivatekey."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
            if($response.success==false || !($captcha))
            {
            $errors = 'You have entered an invalid captcha!';
            }

        if($errors==''){
            $content = "<p><strong>Content:</strong></p>";
            $content .= "<p><strong>Name: </strong>".$_POST['yourname']."</p>";
            $content .= "<p><strong>Email: </strong>".$_POST['email']."</p>";
            $content .= "<p><strong>Reason: </strong>".$_POST['reason']."</p>";
            $content .= "<p><strong>Message: </strong>".$_POST['message']."</p>";
            $content .= "<br><p>Sent from ".get_client_ip()."</p>";

            $from = $gsetting -> getSiteMail($odb);
			$fromname = $gsetting -> getSiteTitle($odb);
			$mail = new PHPMailer;
			if ($rvpnmailer == 'php') {
				$mail->isSendmail();
			} else {
				$mail->isSMTP();
				$mail->SMTPSecure = $smtpauthtype;
				$mail->Host = $rshost;
				$mail->Port = $rsport;
				$mail->SMTPAuth = $smtpauthstat;
				$mail->Username = $rsuser;
				$mail->Password = $rspass;
			}
			$mail->setFrom($from, $fromname);
			$mail->addReplyTo($from, $fromname);
			$mail->addAddress($from, $fromname);
			$mail->Subject = 'CrypticVPN Contact Form';
			$mail->msgHTML($content);
			$mail->send();
        }else $old_data = $_POST;    
    }
} 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contact</title> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="./assets/favicon.ico">  
    <link href="./assets/css/font-style.css" rel="stylesheet"/>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="./assets/css/login.css" rel="stylesheet"/> 
    <link href="./assets/css/register.css" rel="stylesheet"/>
</head>
<body class="bg_register">
    <main id="swapper">
        <section class="login register">
            <div class="container">
                <div class="row div_row">
                    <div class="div_logo" align="center">
                        <a href="index.php"><img src="assets/img/logo.png"></a>
                    </div>
                    <div class="box-white">
                        <form action="c1.php" method="POST" name="form-register">
                            <?php include('messages.php'); ?>
                            <div class="form-group">
                                <label for="yourname">Your name <span class="text-red">*</span></label>
                                <input type="text" class="form-control" id="yourname" name="yourname" value="<?=isset($old_data['yourname'])?$old_data['yourname']:''?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email <span class="text-red">*</span></label>
                                <input type="text" class="form-control" id="email" name="email" value="<?=isset($old_data['email'])?$old_data['email']:''?>" required>
                            </div>
                            <div class="form-group">
                                <label for="department">Reason <span class="text-red">*</span></label>                                
                                <select class="form-control" name="reason" required>
                                    <option value="General" <?php if(isset($old_data['reason']) && $old_data['reason']=='General' ) echo 'selected="selected"'; ?> >General Inquiry</option>
                                    <option value="Support" <?php if(isset($old_data['department']) && $old_data['reason']=='Support' ) echo 'selected="selected"'; ?> >Support</option>
                                    <option value="Technical" <?php if(isset($old_data['reason']) && $old_data['reason']=='Technical' ) echo 'selected="selected"'; ?> >Technical Issues</option>
                                    <option value="Careers" <?php if(isset($old_data['reason']) && $old_data['reason']=='Careers' ) echo 'selected="selected"'; ?> >Careers</option>
                                    <option value="DMCA" <?php if(isset($old_data['reason']) && $old_data['reason']=='DMCA' ) echo 'selected="selected"'; ?> >DMCA</option>
                                    <option value="Legal" <?php if(isset($old_data['reason']) && $old_data['reason']=='Legal' ) echo 'selected="selected"'; ?> >Legal</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="message">Message <span class="text-red">*</span></label>
                                <textarea class="form-control" id="message" name="message" required><?php echo isset($old_data['message'])?$old_data['message']:'' ?></textarea>
                            </div>
                            <div class="div_content col-xs-6">
						    <div class="g-recaptcha" data-sitekey="<?php echo $rpublickey; ?>"></div>
                            </div>
                            <div class="div_content col-xs-12 col-md-8">
                                <input type="submit" name="contact" id="contact" value="Submit"/>
                            </div>
                        </form>
                    </div>
                    <?php include('templates/guest_footer.php'); ?>                                       
                </div>
            </div>
        </section>
    </main> 
    <script src="assets/js/jquery.min.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>