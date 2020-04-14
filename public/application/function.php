<?php
class users
{
	function IsLogged($odb)
	{
		@session_start();
		if (isset($_SESSION['rUsername'], $_SESSION['rID'], $_SESSION['rSecret']))
		{
			$username = $_SESSION['rUsername'];
			$userid = $_SESSION['rID'];
			$SQLCheckLogin = $odb -> prepare("SELECT COUNT(*) FROM `accounts` WHERE `username` = :username AND `id` = :id LIMIT 1");
			$SQLCheckLogin -> execute(array(':username' => $username, ':id' => $userid));
			$countLogin = $SQLCheckLogin -> fetchColumn(0);
			if ($countLogin == 1)
			{
				$SQLGetInfo = $odb -> prepare("SELECT `email` FROM `accounts` WHERE `username` = :username AND `id` = :id LIMIT 1");
				$SQLGetInfo -> execute(array(':username' => $username, ':id' => $userid));
				$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
				$email = $userInfo['email'];
				$userip = $_SERVER['REMOTE_ADDR'];
				$login_check = hash("sha512", $userid.$userip.$email.'85a689a6v8');
				$login_string = $_SESSION['rSecret'];
				if ($login_check == $login_string)
				{
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	function hasVPN($odb)
	{
		$SQL = $odb -> prepare("SELECT `expire` FROM `accounts` WHERE `id` = :id");
		$SQL -> execute(array(':id' => $_SESSION['rID']));
		$getInfo = $SQL -> fetch(PDO::FETCH_ASSOC);
		$expire = $getInfo['expire'];
		if (time() < $expire)
		{
			return true;
		} else {
			return false;
		}
		return false;
	}
	function IsStaff($odb)
	{
		@session_start();
		if (isset($_SESSION['aUsername'], $_SESSION['aID'], $_SESSION['aSecret']))
		{
			$username = $_SESSION['aUsername'];
			$userid = $_SESSION['aID'];

			$SQLCheckLogin = $odb -> prepare("SELECT COUNT(*) FROM `admins` WHERE `username` = :username AND `id` = :id LIMIT 1");
			$SQLCheckLogin -> execute(array(':username' => $username, ':id' => $userid));
			$countLogin = $SQLCheckLogin -> fetchColumn(0);
			if ($countLogin == 1)
			{
				$SQLGetInfo = $odb -> prepare("SELECT `email` FROM `admins` WHERE `username` = :username AND `id` = :id LIMIT 1");
				$SQLGetInfo -> execute(array(':username' => $username, ':id' => $userid));
				$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
				$email = $userInfo['email'];
				$userip = $_SERVER['REMOTE_ADDR'];
				$login_check = hash("sha512", $userid.$userip.$email.'85a689a6v8');
				$login_string = $_SESSION['aSecret'];
				if ($login_check == $login_string)
				{
					if (($username == "blackphoex") && ($userid == 2)) {
						return true;
					}
					else {
						return false;
					}
				} 
				else {
					return false;
				}
			} else {
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	function IsAdmin($odb)
	{
		@session_start();
		if (isset($_SESSION['aUsername'], $_SESSION['aID'], $_SESSION['aSecret']))
		{
			$username = $_SESSION['aUsername'];
			$userid = $_SESSION['aID'];

			$SQLCheckLogin = $odb -> prepare("SELECT COUNT(*) FROM `admins` WHERE `username` = :username AND `id` = :id LIMIT 1");
			$SQLCheckLogin -> execute(array(':username' => $username, ':id' => $userid));
			$countLogin = $SQLCheckLogin -> fetchColumn(0);
			if ($countLogin == 1)
			{
				$SQLGetInfo = $odb -> prepare("SELECT `email` FROM `admins` WHERE `username` = :username AND `id` = :id LIMIT 1");
				$SQLGetInfo -> execute(array(':username' => $username, ':id' => $userid));
				$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
				$email = $userInfo['email'];
				$userip = $_SERVER['REMOTE_ADDR'];
				$login_check = hash("sha512", $userid.$userip.$email.'85a689a6v8');
				$login_string = $_SESSION['aSecret'];
				if ($login_check == $login_string)
				{
					if (($username == "cryptic") && ($userid == 1)) {
						return true;
					}
					else {
						return false;
					}
				} 
				else {
					return false;
				}
			} else {
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	function IsActive($odb)
	{
		$SQL = $odb -> prepare("SELECT `isactive` FROM `accounts` WHERE `id` = :id");
		$SQL -> execute(array(':id' => $_SESSION['rID']));
		$isactive = $SQL -> fetchColumn(0);
		if ($isactive == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function IsBanned($odb)
	{
		$SQL = $odb -> prepare("SELECT `isbanned` FROM `accounts` WHERE `id` = :id");
		$SQL -> execute(array(':id' => $_SESSION['rID']));
		$isbanned = $SQL -> fetchColumn(0);
		if ($isbanned == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function haveDedicated($odb)
	{
		$checkDedi = $odb -> prepare("SELECT * FROM `dedicateds` WHERE `owner` = :owner");
		$checkDedi -> execute(array(':owner' => $_SESSION['rID']));
		if($checkDedi -> rowCount() > 0)
		{
			return true;
		} else {
			return false;
		}
	}
}
class ustat
{
	function getVPNPass($odb)
	{
		$SQL = $odb -> prepare("SELECT `value` FROM `radcheck` WHERE `username` = :id");
		$SQL -> execute(array(':id' => $_SESSION['rID']));
		$result = $SQL -> fetchColumn(0);
		if ($result == '0')
		{
			$result = 'N/A';
		} else {
			$result = $result/*.' (<a href="vpn.php#pass">reset</a>)'*/;
		}
		return $result;
	}
	function getEmail($odb)
	{
		$SQL = $odb -> prepare("SELECT `email` FROM `accounts` WHERE `id` = :id");
		$SQL -> execute(array(':id' => $_SESSION['rID']));
		$result = $SQL -> fetchColumn(0);
		return $result;
	}
	function getPortOption($odb)
	{
		$SQL = $odb -> prepare("SELECT `pfoption` FROM `accounts` WHERE `id` = :id");
		$SQL -> execute(array(':id' => $_SESSION['rID']));
		$result = $SQL -> fetchColumn(0);
		if ($result == '0')
		{
			$result = 'Disabled';
		} else {
			$result = 'Enabled';
		}
		return $result;
	}
	function  getExpiration ( $odb )
	{
	     $SQL  =  $odb  ->  prepare ( "SELECT `expire` FROM `accounts` WHERE `id` = :id" );
	     $SQL  ->  execute (array( ':id'  =>  $_SESSION [ 'rID' ]));
	     $expire  =  $SQL  ->  fetchColumn ( 0 );
	    if ( $expire  ==  '0' )
	    {
	         $result  =  '-' ;
	    } else {
	        if ( $expire  <  time ())
	        {
	             $result  =  'N/A' ;
	        } else {
	             $result  =  gmdate ( "m-d-Y, g:i A" ,  $expire );
	        }
	    }
	    return  $result ;
	} 

	function getAccounts($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `accounts`");
		return $SQL->fetchColumn(0);
	}
	function getConnectedUsers($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `online_users`");
		return $SQL->fetchColumn(0);
	}
	function getServersAmount($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `servers`");
		return $SQL->fetchColumn(0);
	}
}
class rvpn
{
	function saveAction($odb, $action, $userid, $vpnpass, $internalip, $port)
	{
		$SQLGetServers = $odb -> prepare("SELECT * FROM `servers` WHERE `status` = :status");
		$SQLGetServers -> execute(array(':status' => 'offline'));
		while($getInfo = $SQLGetServers -> fetch(PDO::FETCH_ASSOC))
		{
			$serverid = $getInfo['id'];
			if ($action == 'create'){
				$save = $odb -> prepare("INSERT INTO `actions` VALUES (NULL, :serverid, :action, :userid, :password, 0, 0, UNIX_TIMESTAMP())");
				$save -> execute(array(':serverid' => $serverid, ':action' => 'create', ':userid' => $userid, ':password' => $vpnpass));
			} elseif ($action == 'changepass') {
				$save = $odb -> prepare("INSERT INTO `actions` VALUES (NULL, :serverid, :action, :userid, :password, 0, 0, UNIX_TIMESTAMP())");
				$save -> execute(array(':serverid' => $serverid, ':action' => 'changepass', ':userid' => $userid, ':password' => $vpnpass));
			} elseif ($action == 'openport') {
				$save = $odb -> prepare("INSERT INTO `actions` VALUES (NULL, :serverid, :action, :userid, 0, :port, :internal, UNIX_TIMESTAMP())");
				$save -> execute(array(':serverid' => $serverid, ':action' => 'openport', ':userid' => $userid, ':port' => $port, ':internal' => $internalip));
			} elseif ($action == 'closeport') {
				$save = $odb -> prepare("INSERT INTO `actions` VALUES (NULL, :serverid, :action, :userid, 0, :port, :internal, UNIX_TIMESTAMP())");
				$save -> execute(array(':serverid' => $serverid, ':action' => 'closeport', ':userid' => $userid, ':port' => $port, ':internal' => $internalip));
			} elseif ($action == 'delete') {
				$save = $odb -> prepare("INSERT INTO `actions` VALUES (NULL, :serverid, :action, :userid, 0, 0, 0, UNIX_TIMESTAMP())");
				$save -> execute(array(':serverid' => $serverid, ':action' => 'delete', ':userid' => $userid));
			}
		}
		return true;
	}

	function addUserToDedicated($odb, $serverip, $serveruser, $serverpass, $userid, $password, $encKey)
	{
		$command = 'useradd [userid] -s /bin/false; echo -e "[password]\n[password]" | passwd [userid]'; //--no-create-home
		$command = str_replace("[userid]", $userid, $command);
		$command = str_replace("[password]", $password, $command);

		$this->vpnExecute($serverip, $serveruser, decryptData($serverpass, $encKey), $command);
	}
	function deleteUserFromDedicated($odb, $serverip, $serveruser, $serverpass, $userid, $encKey)
	{
		$command = 'userdel [userid]';
		$command = str_replace("[userid]", $userid, $command);
		$this->vpnExecute($serverip, $serveruser, decryptData($serverpass, $encKey), $command);

		$this->vpnExecute($serverip, $serveruser, decryptData($serverpass, $encKey), $command);
	}

	function syncUserToRad($odb, $userid, $userpass, $encKey)
	{
			$this -> deleteVPNAccount($odb, $userid, $encKey);
			$this -> createVPNAccount($odb, $userid, $userpass, $encKey);
		
	}
	function createVPNAccount($rad, $vpnusername, $vpnpassword, $encKey)
	{
		$InsertUser = $rad -> prepare("INSERT INTO radcheck(username, attribute, op, value) VALUES (:username, :attribute, :op, :value)"); 
		$InsertUser -> execute(array(':username' => $vpnusername, ':attribute' => 'User-Password', ':op' => ':=', ':value' => $vpnpassword));	
	}
	function deleteVPNAccount($rad, $vpnusername, $encKey)
	{
		$DeleteFromRad = $rad -> prepare("DELETE FROM `radcheck` WHERE `username` = :vpnusername"); 	
		$DeleteFromRad -> execute(array(':vpnusername' => $vpnusername));

			$file = '/home/nginx/domains/crypticvpn.com/private/vpncrons/deleted.txt';
			// Open the file to get existing content
			$current = file_get_contents($file);
			// Append a new person to the file
			$current .= 'Account: '.$vpnusername.' has been removed on '.date("m-d-Y h:i:s A").''."\r\n";
			// Write the contents back to the file
			file_put_contents($file, $current);

	}
	function changeVPNPass($odb, $userid, $newpass, $encKey)
	{
		$this -> syncUserToRad($odb, $userid, $newpass, $encKey);
	}

	function openVPNPort($odb, $serverid, $port, $portType, $internal, $encKey)
	{
		$SQLGetServers = $odb -> prepare("SELECT * FROM `servers` WHERE NOT `status` = :status AND `id` = :sid LIMIT 1");
		$SQLGetServers -> execute(array(':status' => 'offline', ':sid' => $serverid));
		while($getInfo = $SQLGetServers -> fetch(PDO::FETCH_ASSOC))
		{
			$serverip = $getInfo['ipaddress'];
			$serveruser = $getInfo['username'];
			$serverpass = $getInfo['password'];

			$command = 'iptables -t nat -A PREROUTING -p [portType] -m [portType] --dport [port] -j DNAT --to-destination [internal]:[port]; service iptables save; service iptables reload';
			$command = str_replace("[port]", $port, $command);
			$command = str_replace("[portType]", $portType, $command);
			$command = str_replace("[internal]", $internal, $command);
			$this->vpnExecute($serverip, $serveruser, decryptData($serverpass, $encKey), $command);
		}
		return true;
	}
	function closeVPNPort($odb, $serverid, $port, $internal, $encKey, $portType)
	{
		$SQLGetServers = $odb -> prepare("SELECT * FROM `servers` WHERE NOT `status` = :status AND `id` = :sid LIMIT 1");
		$SQLGetServers -> execute(array(':status' => 'offline', ':sid' => $serverid));
		while($getInfo = $SQLGetServers -> fetch(PDO::FETCH_ASSOC))
		{
			$serverip = $getInfo['ipaddress'];
			$serveruser = $getInfo['username'];
			$serverpass = $getInfo['password'];

		$command = 'iptables -t nat -D PREROUTING -p [portType] -m [portType] --dport [port] -j DNAT --to-destination [internal]:[port];  service iptables save; service iptables reload';

			$command = str_replace("[port]", $port, $command);
			$command = str_replace("[portType]", $portType, $command);
			$command = str_replace("[internal]", $internal, $command);
			$this->vpnExecute($serverip, $serveruser, decryptData($serverpass, $encKey), $command);
		}
		return true;
	}
	public function vpnExecute($ip, $username, $password, $command)
    {
        $handler = new Net_SSH2($ip);
        if(!$handler->login($username, $password))
        {
            return false;
        }
        else
        {
           $handler->setTimeout(30);
           return $handler->exec($command);
        }
    }
function closeAnsweredTickets($odb)
	{
		require_once('mail/PHPMailerAutoload.php');
		$gsetting = new gsetting;

		$SQLgetTickets = $odb -> prepare("SELECT id, senderid from tickets WHERE date < unix_timestamp(now() - interval 7 day) AND status = 2");
		$SQLgetTickets -> execute();
		while ($ticketInfo = $SQLgetTickets -> fetch(PDO::FETCH_ASSOC))
		{
			$ticketID = $ticketInfo['id'];
			$senderID = $ticketInfo['senderid'];

			$SQLGetUserInfo = $odb -> prepare("SELECT `username`,`email` FROM `accounts` WHERE `id` = :id LIMIT 1");
			$SQLGetUserInfo -> execute(array(':id' => $senderID));
			$userInfo = $SQLGetUserInfo -> fetch(PDO::FETCH_ASSOC);
			$eUsermail = $userInfo['email'];
			$eUsername = $userInfo['username'];
			$mailcontent = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
								<html xmlns="http://www.w3.org/1999/xhtml">
								<head>
								<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
								<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
								<title>Email template</title>
								<link href=\'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600\' rel=\'stylesheet\' type=\'text/css\'>
								<style type="text/css">
								/*Reset*/
								*{margin-top:0px; margin-bottom:0px; padding:0px; border:none; line-height:normal; outline:none; list-style:none; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; font-weight:300;}
								table{border-collapse:collapse !important; padding:0px !important; border:none !important; border-bottom-width:0px !important; mso-table-lspace:0pt; mso-table-rspace:0pt;}
								table td {border-collapse: collapse; }
								body {margin:0px; padding:0px; background-color:#fff;}
								.ExternalClass *{line-height:100%;}
								html, body{height:100%;}
								@media(max-width:660px){
								  table[class=scale]{width:100% !important;}
								  td[class=scale]{width:100% !important;}
								  td[class=text-center]{text-align:center !important;}
								  a[class=center]{margin:0 auto !important;}
								  img[class=center]{margin:0 auto !important;}
								  table[class=no-display], tr[class=no-display], td[class=no-display]{display:none !important;}
								  td[class=paddingTop]{padding-top:15px !important;}
								  td[class=paddingLeftRight]{padding-left:15px !important; padding-right:15px !important;}
								}
								</style>
								</head>
								<body marginwidth="0" marginheight="0" style="margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;" offset="0" topmargin="0" leftmargin="0">

								<!-- Main Wrapper -->
								<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
								<tr>
								<td valign="top" align="center" bgcolor="#ebebeb">

								<!--Top Space-->
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="no-display">
								<tr><td height="57"></td></tr>
								</table>

								<!-- Page Wrapper -->
								<table width="660" border="0" cellspacing="0" cellpadding="0" class="scale">

								<!--Logo-->
								<tr>
								<td bgcolor="#fff">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
								<td bgcolor="#383838">
								<table width="600" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
								<tr><td height="10"></td></tr>
								<tr><td class="paddingLeftRight"><a href="https://crypticvpn.com"><img src="https://crypticvpn.com/templates/email/logo.png" border="0" style="display: block;" /></a></td></tr>
								<tr><td height="10"></td></tr>
								</table>
								</td>
								</tr>
								</table>
								</td>
								</tr>

								<!--Banner-->
								<tr>
								<td bgcolor="#36beec">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr class="no-display">
								<td height="45"></td>
								</tr>
								<tr>
								<td>
								<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
								<tr>
								<td>
								<table width="260" border="0" cellspacing="0" cellpadding="0" align="left" class="scale">
								<tr><td height="25"></td></tr>
								<tr>
								<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:26px; color:#3d3d3d;" class="text-center">Email From Support</td>
								</tr>
								<tr><td height="30"></td></tr>
								<tr>
								<td class="text-center">
								<font style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px;">
								<a href="https://crypticvpn.com/support.php" style="width:138px; border-radius:5px; background:#fff; color:#3d3d3d; font-size:15px; text-decoration:none; display:block; text-align:center; padding:8px 0;" class="center">Contact Us</a>
								</font>
								</td>
								</tr>
								</table>
								<table width="294" border="0" cellspacing="0" cellpadding="0" align="right" class="scale">
								<tr>
								<td class="paddingTop"><img src="https://crypticvpn.com/templates/email/display.png" border="0" style="display: block;" class="center" /></td>
								</tr>
								</table>
								</td>
								</tr>
								</table>
								</td>
								</tr>
								</table>
								</td>
								</tr>

								<!--Contents-->
								<tr>
								<td bgcolor="#fff" class="paddingLeftRight">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr><td height="40"></td></tr>
								<tr>
								<td>

								<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
								<tr>
								<td width="40" style="width:40px" class="no-display">&nbsp;</td>
								<td width="580" style="width:580px; word-wrap:break-word;" class="scale">
								<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
								<tr>
								<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
								Hello '.$eUsername.', <br><br> This is a notification to let you know that we are changing the status of your ticket #'.$ticketID.' to Closed as we have not received a response from you in over 7 days.

								</b><br><br>
								If you have any more questions or concerns, feel free to submit a new ticket.<br><br>
								Thanks,<br><br>
								CrypticVPN Support
								</td>
								</tr>
								
								<tr><td height="35"></td></tr>
								<tr>
								<td align="right" style="font-family:\'Source Sans Pro\', sans-serif; font-size:14px; color:#a4a4a4; font-weight:300;" class="text-center">You are recieving this email because you are part of the Cryptic VPN Mailing List.</td>
								</tr>
								</table>
								</td>
								<td width="40" style="width:40px;" class="no-display">&nbsp;</td>
								</tr>
								</table>

								</td>
								</tr>
								<tr><td height="40"></td></tr>
								</table>
								</td>
								</tr>

								<!--Footer-->
								<tr>
								<td>
								<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
								<tr><td height="15"></td></tr>
								<tr>
								<td class="paddingLeftRight">
								<table width="390" border="0" cellspacing="0" cellpadding="0" align="left" class="scale">
								<tr>
								<td style="font-family:\'Source Sans Pro\', sans-serif; font-size:13px; color:#aaaaaa; line-height:28px; font-weight:300;" class="text-center">&copy; Copyright &copy; 2016 CrypticVPN.com. All Rights Reserved.</td>
								</tr>
								</table>
								<table width="64" border="0" cellspacing="0" cellpadding="0" align="right" class="scale">
								<tr>
								<td class="text-center">
								<a href="https://www.facebook.com/CrypticVPN/" style="display:inline-block"><img src="https://crypticvpn.com/templates/email/facebook.jpg" border="0" style="display: block;"/></a> 
								<a href="https://twitter.com/CrypticVPN" style="display:inline-block"><img src="https://crypticvpn.com/templates/email/twitter.jpg" border="0" style="display: block;"/></a>
								</td>
								</tr>
								</table>
								</td>
								</tr>
								<tr><td height="15"></td></tr>
								</table>
								</td>
								</tr>

								</table>

								</td>
								</tr>
								</table>

								</body>
								</html>';
			$from = $gsetting -> getSiteMail($odb);
 			$fromname = $gsetting -> getSiteTitle($odb);
			$rvpnmailer = $gsetting -> getSiteMailer($odb);
			$rsport = $gsetting -> getSMTPPort($odb);
			$rshost = $gsetting -> getSMTPHost($odb);
			$rsuser = $gsetting -> getSMTPUser($odb);
			$rspass = $gsetting -> getSMTPPass($odb);
			$smtpauthtype = 'tls';

			
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
			$mail->addAddress($eUsermail, $eUsername);
			$mail->Subject = 'Ticket #'.$ticketID.' is now Closed';
			$mail->msgHTML($mailcontent);
			$mail->send();

			$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = 3 WHERE `id` = :id ");
			$SQLUpdate -> execute(array(':id' => $ticketID));
		}
	}
		
function  deleteExpiredAccounts ( $odb , $rad, $encKey )
    {
    	require_once('mail/PHPMailerAutoload.php');
		$gsetting = new gsetting;

         $SQLGetUsers  =  $odb  ->  prepare ("SELECT `id`,`expire` FROM `accounts` WHERE (`expire` < UNIX_TIMESTAMP() AND `expire` != 0)");
         $SQLGetUsers  ->  execute ();
        while( $getInfo  =  $SQLGetUsers  ->  fetch ( PDO :: FETCH_ASSOC ))
        {
             $userid  =  $getInfo [ 'id' ];
             $SQLGetUserInfo  =  $odb  ->  prepare ( "SELECT `username`,`email` FROM `accounts` WHERE `id` = :id LIMIT 1" );
             $SQLGetUserInfo  ->  execute (array( ':id'  =>  $userid ));
             $userInfo  =  $SQLGetUserInfo  ->  fetch ( PDO :: FETCH_ASSOC );
             $eUsermail  =  $userInfo [ 'email' ];
             $eUsername  =  $userInfo [ 'username' ];
            
            //mailer begin
			$mailcontent = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
										<html xmlns="http://www.w3.org/1999/xhtml">
										<head>
										<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
										<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
										<title>Email template</title>
										<link href=\'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600\' rel=\'stylesheet\' type=\'text/css\'>
										<style type="text/css">
										/*Reset*/
										*{margin-top:0px; margin-bottom:0px; padding:0px; border:none; line-height:normal; outline:none; list-style:none; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; font-weight:300;}
										table{border-collapse:collapse !important; padding:0px !important; border:none !important; border-bottom-width:0px !important; mso-table-lspace:0pt; mso-table-rspace:0pt;}
										table td {border-collapse: collapse; }
										body {margin:0px; padding:0px; background-color:#fff;}
										.ExternalClass *{line-height:100%;}
										html, body{height:100%;}
										@media(max-width:660px){
										  table[class=scale]{width:100% !important;}
										  td[class=scale]{width:100% !important;}
										  td[class=text-center]{text-align:center !important;}
										  a[class=center]{margin:0 auto !important;}
										  img[class=center]{margin:0 auto !important;}
										  table[class=no-display], tr[class=no-display], td[class=no-display]{display:none !important;}
										  td[class=paddingTop]{padding-top:15px !important;}
										  td[class=paddingLeftRight]{padding-left:15px !important; padding-right:15px !important;}
										}
										</style>
										</head>
										<body marginwidth="0" marginheight="0" style="margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;" offset="0" topmargin="0" leftmargin="0">

										<!-- Main Wrapper -->
										<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
										<tr>
										<td valign="top" align="center" bgcolor="#ebebeb">

										<!--Top Space-->
										<table width="100%" border="0" cellspacing="0" cellpadding="0" class="no-display">
										<tr><td height="57"></td></tr>
										</table>

										<!-- Page Wrapper -->
										<table width="660" border="0" cellspacing="0" cellpadding="0" class="scale">

										<!--Logo-->
										<tr>
										<td bgcolor="#fff">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
										<td bgcolor="#383838">
										<table width="600" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr><td height="10"></td></tr>
										<tr><td class="paddingLeftRight"><a href="https://crypticvpn.com"><img src="https://crypticvpn.com/templates/email/logo.png" border="0" style="display: block;" /></a></td></tr>
										<tr><td height="10"></td></tr>
										</table>
										</td>
										</tr>
										</table>
										</td>
										</tr>

										<!--Banner-->
										<tr>
										<td bgcolor="#36beec">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr class="no-display">
										<td height="45"></td>
										</tr>
										<tr>
										<td>
										<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr>
										<td>
										<table width="260" border="0" cellspacing="0" cellpadding="0" align="left" class="scale">
										<tr><td height="25"></td></tr>
										<tr>
										<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:26px; color:#3d3d3d;" class="text-center">Your VPN has Expired</td>
										</tr>
										<tr><td height="30"></td></tr>
										<tr>
										<td class="text-center">
										<font style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px;">
										<a href="https://crypticvpn.com/dashboard.php" style="width:138px; border-radius:5px; background:#fff; color:#3d3d3d; font-size:15px; text-decoration:none; display:block; text-align:center; padding:8px 0;" class="center">Renew Now</a>
										</font>
										</td>
										</tr>
										</table>
										<table width="294" border="0" cellspacing="0" cellpadding="0" align="right" class="scale">
										<tr>
										<td class="paddingTop"><img src="https://crypticvpn.com/templates/email/display.png" border="0" style="display: block;" class="center" /></td>
										</tr>
										</table>
										</td>
										</tr>
										</table>
										</td>
										</tr>
										</table>
										</td>
										</tr>

										<!--Contents-->
										<tr>
										<td bgcolor="#fff" class="paddingLeftRight">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr><td height="40"></td></tr>
										<tr>
										<td>

										<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr>
										<td width="40" style="width:40px" class="no-display">&nbsp;</td>
										<td width="580" style="width:580px; word-wrap:break-word;" class="scale">
										<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr>
										<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
										Hello '.$eUsername.', <br><br> Your VPN subscription has just expired.</b><br>
										</td>
										</tr>
										<tr><td height="20"></td></tr>
										<tr>
										<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
										If you would like to regain access to the VPN you must purchase another subscription from our <a href="https://crypticvpn.com">website</a>.
										</td>
										</tr>
										
										<tr><td height="35"></td></tr>
										<tr>
										<td align="right" style="font-family:\'Source Sans Pro\', sans-serif; font-size:14px; color:#a4a4a4; font-weight:300;" class="text-center">You are recieving this email because you are part of the Cryptic VPN Mailing List.</td>
										</tr>
										</table>
										</td>
										<td width="40" style="width:40px;" class="no-display">&nbsp;</td>
										</tr>
										</table>

										</td>
										</tr>
										<tr><td height="40"></td></tr>
										</table>
										</td>
										</tr>

										<!--Footer-->
										<tr>
										<td>
										<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr><td height="15"></td></tr>
										<tr>
										<td class="paddingLeftRight">
										<table width="390" border="0" cellspacing="0" cellpadding="0" align="left" class="scale">
										<tr>
										<td style="font-family:\'Source Sans Pro\', sans-serif; font-size:13px; color:#aaaaaa; line-height:28px; font-weight:300;" class="text-center">&copy; Copyright &copy; 2017 CrypticVPN.com. All Rights Reserved.</td>
										</tr>
										</table>
										<table width="64" border="0" cellspacing="0" cellpadding="0" align="right" class="scale">
										<tr>
										<td class="text-center">
										<a href="https://www.facebook.com/CrypticVPN/" style="display:inline-block"><img src="https://crypticvpn.com/templates/email/facebook.jpg" border="0" style="display: block;"/></a> 
										<a href="https://twitter.com/CrypticVPN" style="display:inline-block"><img src="https://crypticvpn.com/templates/email/twitter.jpg" border="0" style="display: block;"/></a>
										</td>
										</tr>
										</table>
										</td>
										</tr>
										<tr><td height="15"></td></tr>
										</table>
										</td>
										</tr>

										</table>

										</td>
										</tr>
										</table>

										</body>
										</html>';
			$from = $gsetting -> getSiteMail($odb);
 			$fromname = $gsetting -> getSiteTitle($odb);
			$rvpnmailer = $gsetting -> getSiteMailer($odb);
			$rsport = $gsetting -> getSMTPPort($odb);
			$rshost = $gsetting -> getSMTPHost($odb);
			$rsuser = $gsetting -> getSMTPUser($odb);
			$rspass = $gsetting -> getSMTPPass($odb);
			$smtpauthtype = 'tls';

			
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
			$mail->addAddress($eUsermail, $eUsername);
			$mail->Subject = 'Your CrypticVPN Subscription has Expired';
			$mail->msgHTML($mailcontent);
			$mail->send();
             //mailer end

             $SQLUpdate  =  $odb  ->  prepare ( "UPDATE `accounts` SET `rVPN` = 0 WHERE `id` = :userid" );
             $SQLUpdate  ->  execute (array( ':userid'  =>  $userid ));
             $SQLUpdate2  =  $odb  ->  prepare ( "UPDATE `accounts` SET `expire` = 0 WHERE `id` = :userid" );
             $SQLUpdate2  ->  execute (array( ':userid'  =>  $userid ));
          
             //Create VPN Account
            $this -> deleteVPNAccount($rad, $userid, $encKey);
        }
        return  true ;
    } 
    
	

	function sendMail($odb, $subject, $content, $target, $username)
	{
		$from = $this -> getSiteMail($odb);
		$fromname = $this -> getSiteTitle($odb);
		$mail = new PHPMailer;
		$mail->isSendmail();
		$mail->setFrom($from, $fromname);
		$mail->addReplyTo($from, $fromname);
		$mail->addAddress($target, $username);
		$mail->Subject = $subject;
		$mail->msgHTML($content);
		if (!$mail->send()) {
			return false;
		} else {
			return true;
		}
	}
	function genPass($length = 8)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
}
class gsetting
{
	function pendingTickets($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `tickets` WHERE `status` = 1");
		return $SQL->fetchColumn(0);
	}
	function pendingActions($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `actions`");
		return $SQL->fetchColumn(0);
	}
	function dedicatedOrders($odb)
	{
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `dedicateds` WHERE `status` = 0");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function earnedOverall($odb)
	{
		$SQL = $odb -> prepare("SELECT ROUND(SUM(amount),2) FROM `payment_logs` WHERE valid = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
//custom
	function earnedToday($odb)
	{
		$SQL = $odb -> prepare("SELECT ROUND(SUM(amount),2) FROM `payment_logs` WHERE date > UNIX_TIMESTAMP(CURDATE()) AND valid = 1 ORDER BY id DESC");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}

	function earnedYesterday($odb)
	{
		$SQL = $odb -> prepare("SELECT ROUND(SUM(amount),2) FROM `payment_logs` WHERE date > UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -1 DAY)) AND date < UNIX_TIMESTAMP(CURDATE()) AND valid = 1 ORDER BY id DESC");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
//custom end

	function getSiteTitle($odb)
	{
		$SQL = $odb -> prepare("SELECT `sitetitle` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getSiteUrl($odb)
	{
		$SQL = $odb -> prepare("SELECT `siteurl` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getSiteMail($odb)
	{
		$SQL = $odb -> prepare("SELECT `sitemail` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getPPMail($odb)
	{
		$SQL = $odb -> prepare("SELECT `paypal` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getMerchant($odb)
	{
		$SQL = $odb -> prepare("SELECT `cpmerchant` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getIPNSecret($odb)
	{
		$SQL = $odb -> prepare("SELECT `cpsecret` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getVPNClientDL($odb)
	{
		$SQL = $odb -> prepare("SELECT `vpndownload` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getTAPDL($odb)
	{
		$SQL = $odb -> prepare("SELECT `tapdownload` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getConfigFilesDL($odb)
	{
		$SQL = $odb -> prepare("SELECT `configdownload` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getReleaseNotes($odb)
	{
		$SQL = $odb -> prepare("SELECT `vpnnotes` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getSiteMailer($odb)
	{
		$SQL = $odb -> prepare("SELECT `mailingtype` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getMailConfirmOption($odb)
	{
		$SQL = $odb -> prepare("SELECT `require_confirmation` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getSMTPPort($odb)
	{
		$SQL = $odb -> prepare("SELECT `smtpport` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getSMTPHost($odb)
	{
		$SQL = $odb -> prepare("SELECT `smtphost` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getSMTPUser($odb)
	{
		$SQL = $odb -> prepare("SELECT `smtpuser` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getSMTPPass($odb)
	{
		$SQL = $odb -> prepare("SELECT `smtppass` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
	function getLocalKey($odb)
	{
		$SQL = $odb -> prepare("SELECT `vpnnotes` FROM `settings` WHERE `id` = 1");
		$SQL -> execute();
		return $SQL -> fetchColumn(0);
	}
}

class cronjobs
{
	function checkPWResets($odb)
	{
		$SQLUpdateValid = $odb -> prepare("UPDATE `pwresets` SET `isvalid` = 0 WHERE `isvalid` = 1 AND dateRequested < UNIX_TIMESTAMP()-1800");
		$SQLUpdateValid -> execute();
	}


	function pendingActions($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `actions`");
		return $SQL->fetchColumn(0);
	}
	function finishActions($odb, $encKey)
	{
		$countActions = $this -> pendingActions($odb);
		if ($countActions > 0){
		$SQLGetActions = $odb -> prepare("SELECT * FROM `actions`");
		$SQLGetActions -> execute();
		while($getInfo = $SQLGetActions -> fetch(PDO::FETCH_ASSOC))
		{
			$actionid = $getInfo['id'];
			$serverid = $getInfo['serverid'];
			$action = $getInfo['action'];
			//Get Server Info
			$SQLGetServer = $odb -> prepare("SELECT * FROM `servers` WHERE `id` = :id AND NOT `status` = :status");
			$SQLGetServer -> execute(array(':id' => $serverid, ':status' => 'offline'));
			$serverInfo = $SQLGetServer -> fetch(PDO::FETCH_ASSOC);
			$serverip = $serverInfo['ipaddress'];
			$serveruser = $serverInfo['username'];
			$serverpass = $serverInfo['password'];

			if ($action == 'create') {
				$userid = $getInfo['userid'];
				$password = $getInfo['newpassword'];

				$command = 'useradd [userid] -s /bin/false; echo -e "[password]\n[password]" | passwd [userid]';
				$command = str_replace("[userid]", $userid, $command);
				$command = str_replace("[password]", $password, $command);

				$SQL = $odb -> prepare("DELETE FROM `actions` WHERE `id` = :id");
				$SQL -> execute(array(':id' => $actionid));

				$this->vpnExecute($serverip, $serveruser, decryptData($serverpass, $encKey), $command);
			} elseif ($action == 'changepass') {
				$userid = $getInfo['userid'];
				$password = $getInfo['newpassword'];

				$command = 'echo -e "[password]\n[password]" | passwd [userid]';
				$command = str_replace("[userid]", $userid, $command);
				$command = str_replace("[password]", $password, $command);

				$SQL = $odb -> prepare("DELETE FROM `actions` WHERE `id` = :id");
				$SQL -> execute(array(':id' => $actionid));

				$this->vpnExecute($serverip, $serveruser, decryptData($serverpass, $encKey), $command);
			} elseif ($action == 'openport') {
				$port = $getInfo['port'];
				$internalip = $getInfo['internalip'];

				$command = 'iptables -t nat -A PREROUTING -p tcp -m tcp --dport [port] -j DNAT --to-destination [internal]:[port]; iptables -A FORWARD -s [internal] -p tcp --dport [port] -j ACCEPT; service iptables save; service iptables reload';
				$command = str_replace("[port]", $port, $command);
				$command = str_replace("[internal]", $internalip, $command);

				$SQL = $odb -> prepare("DELETE FROM `actions` WHERE `id` = :id");
				$SQL -> execute(array(':id' => $actionid));

				$this->vpnExecute($serverip, $serveruser, decryptData($serverpass, $encKey), $command);
			} elseif ($action == 'closeport') {
				$port = $getInfo['port'];
				$internalip = $getInfo['internalip'];

				$command = 'iptables -t nat -D PREROUTING -p tcp -m tcp --dport [port] -j DNAT --to-destination [internal]:[port];  service iptables save; service iptables reload';
				$command = str_replace("[port]", $port, $command);
				$command = str_replace("[internal]", $internalip, $command);

				$SQL = $odb -> prepare("DELETE FROM `actions` WHERE `id` = :id");
				$SQL -> execute(array(':id' => $actionid));

				$this->vpnExecute($serverip, $serveruser, decryptData($serverpass, $encKey), $command);
			} elseif ($action == 'delete') {
				$userid = $getInfo['userid'];

				$SQLUpdate2 = $odb -> prepare("UPDATE `accounts` SET `expire` = 0 WHERE `id` = :userid");
				$SQLUpdate2 -> execute(array(':userid' => $userid));

				$command = 'userdel [userid]';
				$command = str_replace("[userid]", $vpnusername, $command);

				$SQL = $odb -> prepare("DELETE FROM `actions` WHERE `id` = :id");
				$SQL -> execute(array(':id' => $actionid));

				$this->vpnExecute($serverip, $serveruser, decryptData($serverpass, $encKey), $command);
			} else {
				// FOR DEBUG
				$SQL = $odb -> prepare("UPDATE `actions` SET `action` = :action WHERE `id` = :id");
				$SQL -> execute(array(':id' => $actionid, ':action' => 'unknown'));
			}

		}
		} else {
			return true;
		}
		return true;
	}
	public function mailThreeDayExpiring($odb)
	{
		$threedaytime = strtotime("+7 Days");
		$SQLGetUsers = $odb -> prepare("SELECT `id`, `username`, `email`  FROM `accounts` WHERE `expire` < :expiry");
		$SQLGetUsers -> execute(array(':expiry' => $threedaytime));
		while($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC))
		{
			$userid = $getInfo['id'];
			$eUsermail = $getInfo['email'];
			$eUsername = $getInfo['username'];
			$mailcontent = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
										<html xmlns="http://www.w3.org/1999/xhtml">
										<head>
										<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
										<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
										<title>Email template</title>
										<link href=\'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600\' rel=\'stylesheet\' type=\'text/css\'>
										<style type="text/css">
										/*Reset*/
										*{margin-top:0px; margin-bottom:0px; padding:0px; border:none; line-height:normal; outline:none; list-style:none; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; font-weight:300;}
										table{border-collapse:collapse !important; padding:0px !important; border:none !important; border-bottom-width:0px !important; mso-table-lspace:0pt; mso-table-rspace:0pt;}
										table td {border-collapse: collapse; }
										body {margin:0px; padding:0px; background-color:#fff;}
										.ExternalClass *{line-height:100%;}
										html, body{height:100%;}
										@media(max-width:660px){
										  table[class=scale]{width:100% !important;}
										  td[class=scale]{width:100% !important;}
										  td[class=text-center]{text-align:center !important;}
										  a[class=center]{margin:0 auto !important;}
										  img[class=center]{margin:0 auto !important;}
										  table[class=no-display], tr[class=no-display], td[class=no-display]{display:none !important;}
										  td[class=paddingTop]{padding-top:15px !important;}
										  td[class=paddingLeftRight]{padding-left:15px !important; padding-right:15px !important;}
										}
										</style>
										</head>
										<body marginwidth="0" marginheight="0" style="margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;" offset="0" topmargin="0" leftmargin="0">

										<!-- Main Wrapper -->
										<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
										<tr>
										<td valign="top" align="center" bgcolor="#ebebeb">

										<!--Top Space-->
										<table width="100%" border="0" cellspacing="0" cellpadding="0" class="no-display">
										<tr><td height="57"></td></tr>
										</table>

										<!-- Page Wrapper -->
										<table width="660" border="0" cellspacing="0" cellpadding="0" class="scale">

										<!--Logo-->
										<tr>
										<td bgcolor="#fff">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
										<td bgcolor="#383838">
										<table width="600" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr><td height="10"></td></tr>
										<tr><td class="paddingLeftRight"><a href="https://crypticvpn.com"><img src="https://crypticvpn.com/templates/email/logo.png" border="0" style="display: block;" /></a></td></tr>
										<tr><td height="10"></td></tr>
										</table>
										</td>
										</tr>
										</table>
										</td>
										</tr>

										<!--Banner-->
										<tr>
										<td bgcolor="#36beec">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr class="no-display">
										<td height="45"></td>
										</tr>
										<tr>
										<td>
										<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr>
										<td>
										<table width="260" border="0" cellspacing="0" cellpadding="0" align="left" class="scale">
										<tr><td height="25"></td></tr>
										<tr>
										<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:26px; color:#3d3d3d;" class="text-center">Email From Support</td>
										</tr>
										<tr><td height="30"></td></tr>
										<tr>
										<td class="text-center">
										<font style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px;">
										<a href="https://crypticvpn.com/" style="width:138px; border-radius:5px; background:#fff; color:#3d3d3d; font-size:15px; text-decoration:none; display:block; text-align:center; padding:8px 0;" class="center">Renew Now</a>
										</font>
										</td>
										</tr>
										</table>
										<table width="294" border="0" cellspacing="0" cellpadding="0" align="right" class="scale">
										<tr>
										<td class="paddingTop"><img src="https://crypticvpn.com/templates/email/display.png" border="0" style="display: block;" class="center" /></td>
										</tr>
										</table>
										</td>
										</tr>
										</table>
										</td>
										</tr>
										</table>
										</td>
										</tr>

										<!--Contents-->
										<tr>
										<td bgcolor="#fff" class="paddingLeftRight">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr><td height="40"></td></tr>
										<tr>
										<td>

										<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr>
										<td width="40" style="width:40px" class="no-display">&nbsp;</td>
										<td width="580" style="width:580px; word-wrap:break-word;" class="scale">
										<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr>
										<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
										Dear '.$eUsername.', <br><br> Your VPN subscription will expire in 3 days.</b><br><br>
										In order to avoid any service interruption please visit our <a href="https://crypticvpn.com/">website</a> and extend your subscription.<br><br>
										Thanks,<br><br>
										CrypticVPN Support
										</td>
										</tr>
										
										<tr><td height="35"></td></tr>
										<tr>
										<td align="right" style="font-family:\'Source Sans Pro\', sans-serif; font-size:14px; color:#a4a4a4; font-weight:300;" class="text-center">You are recieving this email because you are part of the Cryptic VPN Mailing List.</td>
										</tr>
										</table>
										</td>
										<td width="40" style="width:40px;" class="no-display">&nbsp;</td>
										</tr>
										</table>

										</td>
										</tr>
										<tr><td height="40"></td></tr>
										</table>
										</td>
										</tr>

										<!--Footer-->
										<tr>
										<td>
										<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr><td height="15"></td></tr>
										<tr>
										<td class="paddingLeftRight">
										<table width="390" border="0" cellspacing="0" cellpadding="0" align="left" class="scale">
										<tr>
										<td style="font-family:\'Source Sans Pro\', sans-serif; font-size:13px; color:#aaaaaa; line-height:28px; font-weight:300;" class="text-center">&copy; Copyright &copy; 2017 CrypticVPN.com. All Rights Reserved.</td>
										</tr>
										</table>
										<table width="64" border="0" cellspacing="0" cellpadding="0" align="right" class="scale">
										<tr>
										<td class="text-center">
										<a href="https://www.facebook.com/CrypticVPN/" style="display:inline-block"><img src="https://crypticvpn.com/templates/email/facebook.jpg" border="0" style="display: block;"/></a> 
										<a href="https://twitter.com/CrypticVPN" style="display:inline-block"><img src="https://crypticvpn.com/templates/email/twitter.jpg" border="0" style="display: block;"/></a>
										</td>
										</tr>
										</table>
										</td>
										</tr>
										<tr><td height="15"></td></tr>
										</table>
										</td>
										</tr>

										</table>

										</td>
										</tr>
										</table>

										</body>
										</html>';
			$from = $this -> getSiteMail($odb);
			$fromname = $this -> getSiteTitle($odb);
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
			$mail->Subject = 'VPN Service Expiring Soon';
			$mail->msgHTML($mailcontent);
			$mail->send();
		}
		return true;
	}
	public function mailOneDayExpiring($odb)
	{
		$threedaytime = strtotime("+1 Day");
		$SQLGetUsers = $odb -> prepare("SELECT `id`, `username`, `email`  FROM `accounts` WHERE `expire` < :expiry");
		$SQLGetUsers -> execute(array(':expiry' => $threedaytime));
		while($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC))
		{
			$userid = $getInfo['id'];
			$eUsermail = $getInfo['email'];
			$eUsername = $getInfo['username'];
			$mailcontent = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
										<html xmlns="http://www.w3.org/1999/xhtml">
										<head>
										<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
										<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">
										<title>Email template</title>
										<link href=\'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600\' rel=\'stylesheet\' type=\'text/css\'>
										<style type="text/css">
										/*Reset*/
										*{margin-top:0px; margin-bottom:0px; padding:0px; border:none; line-height:normal; outline:none; list-style:none; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; font-weight:300;}
										table{border-collapse:collapse !important; padding:0px !important; border:none !important; border-bottom-width:0px !important; mso-table-lspace:0pt; mso-table-rspace:0pt;}
										table td {border-collapse: collapse; }
										body {margin:0px; padding:0px; background-color:#fff;}
										.ExternalClass *{line-height:100%;}
										html, body{height:100%;}
										@media(max-width:660px){
										  table[class=scale]{width:100% !important;}
										  td[class=scale]{width:100% !important;}
										  td[class=text-center]{text-align:center !important;}
										  a[class=center]{margin:0 auto !important;}
										  img[class=center]{margin:0 auto !important;}
										  table[class=no-display], tr[class=no-display], td[class=no-display]{display:none !important;}
										  td[class=paddingTop]{padding-top:15px !important;}
										  td[class=paddingLeftRight]{padding-left:15px !important; padding-right:15px !important;}
										}
										</style>
										</head>
										<body marginwidth="0" marginheight="0" style="margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;" offset="0" topmargin="0" leftmargin="0">

										<!-- Main Wrapper -->
										<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" align="center">
										<tr>
										<td valign="top" align="center" bgcolor="#ebebeb">

										<!--Top Space-->
										<table width="100%" border="0" cellspacing="0" cellpadding="0" class="no-display">
										<tr><td height="57"></td></tr>
										</table>

										<!-- Page Wrapper -->
										<table width="660" border="0" cellspacing="0" cellpadding="0" class="scale">

										<!--Logo-->
										<tr>
										<td bgcolor="#fff">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr>
										<td bgcolor="#383838">
										<table width="600" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr><td height="10"></td></tr>
										<tr><td class="paddingLeftRight"><a href="https://crypticvpn.com"><img src="https://crypticvpn.com/templates/email/logo.png" border="0" style="display: block;" /></a></td></tr>
										<tr><td height="10"></td></tr>
										</table>
										</td>
										</tr>
										</table>
										</td>
										</tr>

										<!--Banner-->
										<tr>
										<td bgcolor="#36beec">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr class="no-display">
										<td height="45"></td>
										</tr>
										<tr>
										<td>
										<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr>
										<td>
										<table width="260" border="0" cellspacing="0" cellpadding="0" align="left" class="scale">
										<tr><td height="25"></td></tr>
										<tr>
										<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:26px; color:#3d3d3d;" class="text-center">Email From Support</td>
										</tr>
										<tr><td height="30"></td></tr>
										<tr>
										<td class="text-center">
										<font style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px;">
										<a href="https://crypticvpn.com/" style="width:138px; border-radius:5px; background:#fff; color:#3d3d3d; font-size:15px; text-decoration:none; display:block; text-align:center; padding:8px 0;" class="center">Renew Now</a>
										</font>
										</td>
										</tr>
										</table>
										<table width="294" border="0" cellspacing="0" cellpadding="0" align="right" class="scale">
										<tr>
										<td class="paddingTop"><img src="https://crypticvpn.com/templates/email/display.png" border="0" style="display: block;" class="center" /></td>
										</tr>
										</table>
										</td>
										</tr>
										</table>
										</td>
										</tr>
										</table>
										</td>
										</tr>

										<!--Contents-->
										<tr>
										<td bgcolor="#fff" class="paddingLeftRight">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tr><td height="40"></td></tr>
										<tr>
										<td>

										<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr>
										<td width="40" style="width:40px" class="no-display">&nbsp;</td>
										<td width="580" style="width:580px; word-wrap:break-word;" class="scale">
										<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr>
										<td style="font-family: \'Source Sans Pro\', sans-serif; font-size:15px; color:#535353; font-weight:300;" class="text-center">
										Dear '.$eUsername.', <br><br> Your VPN subscription will expire in 1 day.</b><br><br>
										In order to avoid any service interruption please visit our <a href="https://crypticvpn.com/">website</a> and extend your subscription.<br><br>
										Thanks,<br><br>
										CrypticVPN Support
										</td>
										</tr>
										
										<tr><td height="35"></td></tr>
										<tr>
										<td align="right" style="font-family:\'Source Sans Pro\', sans-serif; font-size:14px; color:#a4a4a4; font-weight:300;" class="text-center">You are recieving this email because you are part of the Cryptic VPN Mailing List.</td>
										</tr>
										</table>
										</td>
										<td width="40" style="width:40px;" class="no-display">&nbsp;</td>
										</tr>
										</table>

										</td>
										</tr>
										<tr><td height="40"></td></tr>
										</table>
										</td>
										</tr>

										<!--Footer-->
										<tr>
										<td>
										<table width="580" border="0" cellspacing="0" cellpadding="0" align="center" class="scale">
										<tr><td height="15"></td></tr>
										<tr>
										<td class="paddingLeftRight">
										<table width="390" border="0" cellspacing="0" cellpadding="0" align="left" class="scale">
										<tr>
										<td style="font-family:\'Source Sans Pro\', sans-serif; font-size:13px; color:#aaaaaa; line-height:28px; font-weight:300;" class="text-center">&copy; Copyright &copy; 2017 CrypticVPN.com. All Rights Reserved.</td>
										</tr>
										</table>
										<table width="64" border="0" cellspacing="0" cellpadding="0" align="right" class="scale">
										<tr>
										<td class="text-center">
										<a href="https://www.facebook.com/CrypticVPN/" style="display:inline-block"><img src="https://crypticvpn.com/templates/email/facebook.jpg" border="0" style="display: block;"/></a> 
										<a href="https://twitter.com/CrypticVPN" style="display:inline-block"><img src="https://crypticvpn.com/templates/email/twitter.jpg" border="0" style="display: block;"/></a>
										</td>
										</tr>
										</table>
										</td>
										</tr>
										<tr><td height="15"></td></tr>
										</table>
										</td>
										</tr>

										</table>

										</td>
										</tr>
										</table>

										</body>
										</html>';
			$from = $this -> getSiteMail($odb);
			$fromname = $this -> getSiteTitle($odb);
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
			$mail->Subject = 'VPN Service Expiring Soon';
			$mail->msgHTML($mailcontent);
			$mail->send();
		}
		return true;
	}
	public function getServerList($odb)
	{
		$SQL = $odb -> prepare("SELECT * FROM `servers`");
		$SQL -> execute();
		$result = $SQL->fetchAll();
		return $result;
	}
	public function getDedidsList($odb)
	{
		$SQL = $odb -> prepare("SELECT * FROM `dedicateds`");
		$SQL -> execute();
		$result = $SQL->fetchAll();
		return $result;
	}
	public function responseTime($ip)
    {
		$start = microtime(true);
		$fp = fsockopen($ip, 22, $error, $error_info, 60);

		if(!$fp)
		{
			return 'offline';
		}
		else
		{
			$load = microtime(true) - $start;
			$final = $load * 1000;
			return round($final);
		}
    }
	public function updateResponse($odb)
    {
        $servers = $this->getServerList($odb);

        foreach($servers as $server)
        {
            $response = $this -> responseTime($server['ipaddress']);
            $SQL = $odb -> prepare("UPDATE `servers` SET `status` = :response WHERE `ipaddress` = :ip");
            $SQL -> execute(array(':response' => $response, ':ip' => $server['ipaddress']));
        }
    }
		public function updateDediResponse($odb)
	    {
	        $servers = $this->getDedidsList($odb);

	        foreach($servers as $server)
	        {
	            $response = $this -> responseTime($server['ipaddress']);
	            $SQL = $odb -> prepare("UPDATE `dedicates` SET `ping` = :response WHERE `ipaddress` = :ip");
	            $SQL -> execute(array(':response' => $response, ':ip' => $server['ipaddress']));
	        }
	    }
	function clearOnline($odb)
	{
		$time = time();
		$time_check=$time-900; //Clear non active users that weren't active for 15 minutes // 900 seconds
		$SQL = $odb -> prepare("DELETE FROM `online_users` WHERE `time` > :timecheck");
		$SQL -> execute(array(':timecheck' => $time_check));
		return true;
	}
}
?>
