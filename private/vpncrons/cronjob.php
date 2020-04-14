<?php
require '/home/nginx/domains/crypticvpn.com/public/application/config.php';
require '/home/nginx/domains/crypticvpn.com/public/application/global.php';

$cronjobs -> updateResponse($odb, $encryptionKey);
$cronjobs -> updateDediResponse($odb, $encryptionKey);
$rvpn -> deleteExpiredAccounts($odb, $rad, $encryptionKey);
$cronjobs ->checkPWResets($odb);
$cronjobs -> finishActions($odb, $encryptionKey);
//$cronjobs -> closeAnsweredTickets($odb);

echo 'No direct access.';
?>