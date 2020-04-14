<?php
require '/home/nginx/domains/crypticvpn.com/public/application/config.php';
require '/home/nginx/domains/crypticvpn.com/public/application/global.php';

$cronjobs -> updateResponse($odb);

//$cronjobs -> updateDediResponse($odb, $encryptionKey);
//$cronjobs -> clearOnline($odb);
echo 'No direct access.';
?>