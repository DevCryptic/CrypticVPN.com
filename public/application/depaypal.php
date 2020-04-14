<?php
define("_VALID_PHP", true);
  if (isset($_POST['payment_status'])) {
  require_once('config.php');
  require_once('global.php');
  require_once('mail/PHPMailerAutoload.php');

  set_time_limit(0);
  function verifyTxnId($txn_id, $odb)
  {
    $sql = $odb->prepare("SELECT COUNT(id) FROM `dpayment_logs` WHERE tid = :tid LIMIT 1");
    $sql -> execute(array(":tid" => $txn_id));
    if ($sql -> fetchColumn(0) > 0)
        return false;
    else
        return true;
  }

  $req = 'cmd=_notify-validate';

  foreach ($_POST as $key => $value) {
      $value = urlencode(stripslashes($value));
      $req .= '&' . $key . '=' . $value;

  }
  $demo = false;
  $url = 'www.paypal.com';

  $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
  $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
  $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
  $fp = fsockopen($url, 80, $errno, $errstr, 30);

  $payment_status = $_POST['payment_status'];
  $receiver_email = $_POST['business'];
  list($dedicated_id, $user_id) = explode("_", $_POST['item_number']);
  $mc_gross = $_POST['mc_gross'];
  $txn_id = $_POST['txn_id'];

  $getxn_id = verifyTxnId($txn_id, $odb);
  $SQLGetPackageInfo = $odb -> prepare("SELECT * FROM `dedicateds` WHERE id = :id");
  $SQLGetPackageInfo -> execute(array(':id' => (int)$dedicated_id));
  $pInfo = $SQLGetPackageInfo -> fetch(PDO::FETCH_ASSOC);
  $membership_id = $pInfo['package'];
  $nextpayment = $pInfo['nextpayment'];
  $SQLGetPackages = $odb -> prepare("SELECT * FROM `dpackages` WHERE `id` = :id LIMIT 1");
  $SQLGetPackages -> execute(array(':id' => $membership_id));
  $getInfo = $SQLGetPackages -> fetch(PDO::FETCH_ASSOC);
  $packagename = $getInfo['name'];
  $spots = $getInfo['spots'];
  $plength = $getInfo['length'];
  $unit = $getInfo['unit'];
  $price = $getInfo['price'];

  $pp_emailsql = $odb -> query("SELECT `paypal` FROM `settings` LIMIT 1");
  $pp_email = $pp_emailsql->fetchColumn(0);

  if (!$fp) {
      echo $errstr . ' (' . $errno . ')';
  } else {
      fputs($fp, $header . $req);

  while (!feof($fp)) {
  $res = fgets($fp, 1024);
  if (strcmp($res, "VERIFIED") == 0) {
    if (preg_match('/Completed/', $payment_status)) {
							$SQLGetUserInfo = $odb -> prepare("SELECT `username`,`email` FROM `accounts` WHERE `id` = :id LIMIT 1");
							$SQLGetUserInfo -> execute(array(':id' => $user_id));
							$userInfo = $SQLGetUserInfo -> fetch(PDO::FETCH_ASSOC);
							$userMail = $userInfo['email'];
							$userName = $userInfo['username'];

              $newPayment = strtotime("+{$plength} {$unit}", $nextpayment);

          		//update next payment
          		$SQL = $odb -> prepare("UPDATE `dedicateds` SET `nextpayment` = :nextpayment WHERE `id` = :id");
          		$SQL -> execute(array(':nextpayment' => $newPayment, ':id' => $dedicated_id));

          		//Mail Order Confirmation
          		$mailcontent = 'Hello, <br><br> You have extended your dedicated vpn server rental time.<br><br>Thank you!';
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
          		$mail->addAddress($userMail, $userName);
          		$mail->Subject = 'Order Confirmation';
          		$mail->msgHTML($mailcontent);
          		$mail->send();

      		$data = array(
							':tid' => $txn_id,
							':plan' => $membership_id,
							':proccessor' => 'Paypal',
							':user' => $user_id,
							':paid' => $mc_gross,
						  );
						  $odb -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
						  $insertsql = $odb -> prepare("INSERT INTO `dpayment_logs` VALUES(NULL, :tid, :user, :proccessor, :paid, :plan, UNIX_TIMESTAMP())");
						  $insertsql -> execute($data);
          }
      }
    }
    fclose($fp);
  }
}
?>
