<?php
include 'application/config.php';
include 'application/global.php';
if(isset($_GET['id']) && Is_Numeric($_GET['id']) && !isset($_SESSION['rID']))
{
ob_start();
session_start();
function getPath()
{
	$temp = "http://".$_SERVER['HTTP_HOST'];
	if($_SERVER['PHP_SELF'][strlen($_SERVER['PHP_SELF'])-1] == "/")
	{
		$temp.=$_SERVER['PHP_SELF'];
	} else {
		$temp.=dirname($_SERVER['PHP_SELF']);
	}
	if($temp[strlen($temp)-1]=="/")
	{
		$temp = substr($temp, 0, strlen($temp)-1);
	}
	return dirname($temp);
}
$id = (int)$_GET['id'];
$paypalemail = $gsetting -> getPPMail($odb);
$plansql = $odb -> prepare("SELECT * FROM `packages` WHERE `id` = :id");
$plansql -> execute(array(":id" => $id));
$row = $plansql -> fetch();
if($row == NULL) { 
	die("Bad ID"); 
}
$sitel1nk = $site_url;
$paypalurl = "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&amount=".urlencode($row['price'])."&business=".urlencode($paypalemail)."&item_name=".urlencode($row['name'])."&item_number=".urlencode($row['id']."_".$_SESSION['rID'])."&return=http://".urlencode($sitel1nk)."/index.php?payment=success"."&rm=2&notify_url=http://".urlencode($sitel1nk)."/application/paypalipn.php"."&cancel_return=http://".urlencode($sitel1nk)."/index.php?payment=canceled"."&no_note=1&currency_code=USD";

	header("Location: ".$paypalurl);
} else {
	header("Location: index.php");
}
?>