<?php
define('DIRECT', TRUE);
function getRealIpAddr()
{
	if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	{
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	{
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else
	{
		$ip=$_SERVER['REMOTE_ADDR'];
	}
	//$final = end(explode(',', $ip));
	$ip1 = explode(',', $ip); 
	$final = end($ip1);
	//return $ip;
    return $final;

}

$currentpage = $_SERVER['SCRIPT_NAME'];
function CheckPageA($page)
{
	global $currentpage;
	if (strstr($currentpage, $page))
	{
		echo ' active';
	}
}
function CheckPageAdmin($page)
{
	global $currentpage;
	if (strstr($currentpage, $page))
	{
		echo ' current_section';
	}
}
function CheckPageB($page)
{
	global $currentpage;
	if (strstr($currentpage, $page))
	{
		echo ' act_item';
	}
}

$_SERVER['REMOTE_ADDR'] = getRealIpAddr();
require 'function.php';
require 'SSH2.php';
require 'mail/PHPMailerAutoload.php';
$user = new users;
$userstat = new ustat;
$rvpn = new rvpn;
$gsetting = new gsetting;
$cronjobs = new cronjobs;

/*
	General Website Config
*/
$site_url = $gsetting -> getSiteUrl($odb);
$site_title = $gsetting -> getSiteTitle($odb);
$rvpnmailer = $gsetting -> getSiteMailer($odb);
$rvpnregconfirmation = $gsetting -> getMailConfirmOption($odb);
$rsport = $gsetting -> getSMTPPort($odb);
$rshost = $gsetting -> getSMTPHost($odb);
$rsuser = $gsetting -> getSMTPUser($odb);
$rspass = $gsetting -> getSMTPPass($odb);
?>