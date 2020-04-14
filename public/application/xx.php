<?
require '/home/nginx/domains/crypticvpn.com/public/application/config.php';
require '/home/nginx/domains/crypticvpn.com/public/application/global.php';

$user_id="5173";

$SQLGetInfo = $rad -> prepare("SELECT `acctstarttime` AS `Start`, `acctstoptime` AS `End`, `acctsessiontime` AS `Duration`, `callingstationid` AS `IP` FROM `radacct` WHERE `username` = :id");
$SQLGetInfo -> execute(':id', $user_id, PDO::PARAM_INT);
$result = $SQLGetInfo -> fetchAll();
echo $result;
?>