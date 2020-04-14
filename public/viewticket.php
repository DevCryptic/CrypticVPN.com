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

$ticketid = $_GET['id'];
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	header('location: support.php');
	die();
}
$getInfo = $odb->prepare("select * from `tickets` where `id` = :id");
$getInfo->execute(array(":id" => $ticketid));
$Info = $getInfo->fetch(PDO::FETCH_ASSOC);
if($Info['senderid'] != $_SESSION['rID'])
{
	header('location: support.php');
	die;
}
$SQLGetInfo = $odb -> prepare("SELECT * FROM `tickets` WHERE `id` = :id AND `senderid` = :owner LIMIT 1");
$SQLGetInfo -> execute(array(':id' => $ticketid, ':owner' => $_SESSION['rID']));
$ticketInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
$title = $ticketInfo['title'];
$details = $ticketInfo['details'];
$category = $ticketInfo['department'];
$status = $ticketInfo['status'];
$tdate = date("m-d-Y h:i:s A", $ticketInfo['date']);

if ($status == 1) {
	$statustext = '<span class="uk-badge uk-badge-warning">Pending</span>';
} elseif ($status == 2){
	$statustext = '<span class="uk-badge uk-badge-success">Answered</span>';
} elseif ($status == 3){
	$statustext = '<span class="uk-badge uk-badge-danger">Closed</span>';
} elseif ($status == 4){
	$statustext = '<span class="uk-badge uk-badge-danger">Closed</span>';
}
?>
<!DOCTYPE html>
<html lang="en">
<title><?php echo $site_title; ?> - View Ticket (#<?php echo $ticketid; ?>)</title> 
<?php include('templates/head.php'); ?>
<body>
    <?php include('templates/header.php'); ?>
    <div class="container">
      <div class="wrap-main">
        <div class="main-contain col-sm-11">
            <div class="left-content col-sm-3">
                <?php include('templates/sidebar_left.php'); ?>
            </div>
            <div class="right-content bg-white col-sm-9">
                <?php
				if (isset($_POST['sendReply']))
				{
					if ($status != 1 || $status != 3) {
					$reply = htmlspecialchars($_POST['reply']);
					$errors = array();
					if (empty($reply))
					{
						$errors[] = 'Please enter a reply to send.';
					}
					if (strlen($reply) < 10 || strlen($reply) > 4096)
					{
						$errors[] = 'Your answer must contain between 10 - 4096 characters.';
					}

					//check if exist
					$SQLCheckReplyExist = $odb -> prepare("SELECT * FROM `ticketreplies` WHERE `tid` = :tid AND `author` = :author AND `reply` = :reply AND date < DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
					$SQLCheckReplyExist -> execute(array(':tid' => $ticketid, ':author' =>  $_SESSION['rID'], ':reply' => $reply, ':reply' => $reply));
					if($SQLCheckReplyExist -> rowCount() > 0)
					{
						$errors[] = 'You have already submitted this reply to this ticket within the last 30 minutes. Please refresh the page.';
					}
					//check if exist end

					if(empty($errors)) {
						$replyToSTore = nl2br(htmlentities($reply, ENT_QUOTES, 'UTF-8'));

						$insertReply = $odb -> prepare("INSERT INTO `ticketreplies` (`tid`, `author`, `reply`, `date`) VALUES
																		(:tickedID, :author, :reply, UNIX_TIMESTAMP())");
						$insertReply -> execute(array(':tickedID' => $ticketid, ':author' => $_SESSION['rID'], ':reply' => $replyToSTore));
						$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = 1 WHERE `id` = :id ");
						$SQLUpdate -> execute(array(':id' => $ticketid));
							//email admin
								$mailcontent = 'Hello, <br><br> Hello, there is a new response on ticket #'.$ticketid.'<br><br>Respond to it ASAP!';
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
								$mail->addAddress('admin@crypticvpn.com', 'cryptic');
								$mail->addAddress('blackphoex@ub3r.org', 'Blackphoenician');
								$mail->Subject = 'New Support Ticket Response';
								$mail->msgHTML($mailcontent);
								$mail->send();
								//email admin end
						echo '<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have sent your reply successfully!</div><meta http-equiv="refresh" content="0;url='.$_SERVER['REQUEST_URI'].'">';
						} else {
						echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
						foreach($errors as $error)
						{
							echo '- '.$error.'<br />';
						}
						echo '</div>';
						}
					} else {
						echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> The ticket is closed or waiting for an answer from staff.</div>';
					}
				}

				if (isset($_POST['closeTicket']))
				{
					$SQLGetUserInfo = $odb -> prepare("SELECT `username`,`email` FROM `accounts` WHERE `id` = :id LIMIT 1");
					$SQLGetUserInfo -> execute(array(':id' => $_SESSION['rID']));
					$userInfo = $SQLGetUserInfo -> fetch(PDO::FETCH_ASSOC);
					$eUsermail = $userInfo['email'];
					$eUsername = $userInfo['username'];
					$mailcontent = 'Hello, <br><br> Your ticket #'.$ticketid.' was closed.<br><br>Thank you!';
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
					$mail->addAddress($eUsermail, $eUsername);
					$mail->Subject = 'Ticket #'.$ticketid.' Closed';
					$mail->msgHTML($mailcontent);
					$mail->send();
					$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = 3 WHERE `id` = :id ");
					$SQLUpdate -> execute(array(':id' => $ticketid));
					echo '<div class="alert alert-danger" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> You have closed the ticket successfully!</div><meta http-equiv="refresh" content="0;url='.$_SERVER['REQUEST_URI'].'">';
				}
			?>                   	
                <form method="post">
				
				<? echo 	
						'		               
		                <p>Ticket ID: '.$ticketid.'		
						<p>Subject: '.$title.' 
		                '

			    ?>			
				<ul class="uk-comment-list">

				<? echo '
			            <li>
			             <article class="uk-comment panel panel-info">
			              <header class="uk-comment-header panel-heading">
			               <h4 class="uk-comment-title">'.$_SESSION['rUsername'].'</h4>
			               <div class="uk-comment-meta pull-right">'.$tdate.'</div>
			              </header>
			              <div class="uk-comment-body panel-body">
			               <p>'.nl2br(htmlspecialchars_decode($details)).'</p>
			              </div>
			             </article>
			            </li>';
				?>	            </ul>

				  <hr>
					</pre><ul class="uk-comment-list">
					<?php
								$checkIfExists = $odb -> prepare("SELECT * FROM `ticketreplies` WHERE `tid` = :ticketID");
								$checkIfExists -> execute(array(':ticketID' => $ticketid));
								if($checkIfExists -> rowCount() == 0)
								{
									echo '<i>No replies found for this ticket yet.</i>';
								} else {
									$SQLGetReplies = $odb -> prepare("SELECT * FROM `ticketreplies` WHERE `tid` = :tid ORDER BY `date`");
									$SQLGetReplies -> execute(array(':tid' => $ticketid));

							
									while($getInfo = $SQLGetReplies -> fetch(PDO::FETCH_ASSOC))
									{
										$author = $getInfo['author'];
										$reply = $getInfo['reply'];
										$date = date("m-d-Y h:i:s A", $getInfo['date']);

										$SQLGetAuthorUserName = $odb -> prepare("SELECT `username` FROM `accounts` WHERE `id` = :id LIMIT 1");
										$SQLGetAuthorUserName -> execute(array(':id' => (int)$author));
										$ReplyAuthor = $SQLGetAuthorUserName -> fetch(PDO::FETCH_ASSOC);
										$ReplyAuthorUserName = $ReplyAuthor['username'];

										if ($author == $_SESSION['rID']){
								           echo '
								            <li>
								             <article class="uk-comment panel panel-info">
								              <header class="uk-comment-header panel-heading">
								               <h4 class="uk-comment-title">'.$_SESSION['rUsername'].'</h4>
								               <div class="uk-comment-meta pull-right">'.$date.'</div>
								              </header>
								              <div class="uk-comment-body panel-body">
												<p>'.nl2br(htmlspecialchars_decode(preg_replace('~\s*<br ?/?>\s*~',"<br />",$reply))).'</p>
								              </div>
								             </article>
								            </li>';
								          }else{
								           echo '
								           <li>
								            <article class="uk-comment panel panel-danger">
								             <header class="uk-comment-header panel-heading">
								              <h4 class="uk-comment-title">'.$ReplyAuthorUserName.'</h4>
								               <div class="uk-comment-meta pull-right">'.$date.'</div>
								             </header>
								             <div class="uk-comment-body panel-body">
												<p>'.nl2br(htmlspecialchars_decode(preg_replace('~\s*<br ?/?>\s*~',"<br />",$reply))).'</p>
								             </div>
								            </article>
								           </li>';
								           
								         }
									}
							}
																	
					?>

					</ul>
				  <hr>
				  <div class="form-group">
					<label for="message">New Reply <span class="text-red">*</span></label>
					<textarea class="form-control" id="message" name="reply" <?php if ($status == 3){ echo 'disabled'; } ?> <?php if ($status == 1){ echo 'disabled="disabled"'; } ?>required></textarea>
					<?php if ($status == 1){ echo '<p class="text-danger">You have to wait for a staff to reply before sending another reply. If your problem solved, please close the ticket!</p>'; } ?>
				 </div>   
                  <button type="submit" name="sendReply" value="1" class="btn btn-primary" <?php if ($status == 3 || $status == 1){ echo 'disabled'; } ?>>Add Reply</button>
				  <button type="submit" name="closeTicket" class="btn btn-danger" onclick="return confirm('Are you sure you want to close the ticket ?');">Close Ticket</button>
                </form>                         
            </div>
        </div>
      </div>
    </div><!-- /.container -->

    <?php include('templates/footer.php'); ?>
<?php include('templates/scriptjs.php'); ?>
</body>
</html>