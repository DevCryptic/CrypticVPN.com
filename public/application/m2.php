<?
require '/home/nginx/domains/crypticvpn.com/public/application/config.php';
require '/home/nginx/domains/crypticvpn.com/public/application/global.php';

$mailcontent = 'test';

$from = 'no-reply@meep.com';
$fromname = 'meep';
$mail = new PHPMailer;
if ($rvpnmailer == 'php') {
	$mail->isSendmail();
} else {
	$mail->isSMTP();
	$mail->SMTPSecure = $smtpauthtype;
	$mail->Host = $rshost;
	$mail->Port = $rsport;
	$mail->SMTPAuth = true;
	$mail->Username = $rsuser;
	$mail->Password = $rspass;
}
$mail->setFrom($from, $fromname);
$mail->addReplyTo($from, $fromname);
$mail->addAddress('test@ub3r.org', 'Mike');
$mail->Subject = 'test email';
$mail->msgHTML($mailcontent);
$mail->send();

?>