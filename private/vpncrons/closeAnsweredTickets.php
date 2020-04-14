<?php
require '/home/nginx/domains/crypticvpn.com/public/application/config.php';
require '/home/nginx/domains/crypticvpn.com/public/application/global.php';

$rvpn -> closeAnsweredTickets($odb);

echo 'No direct access.';
?>