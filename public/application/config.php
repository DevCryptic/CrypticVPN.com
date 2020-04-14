<?php
// Turn off error reporting
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(0);

// DatabaseInfo
define('DB_HOST', 'localhost');
define('DB_NAME', 'cvpn_data');
define('DB_USERNAME', 'cvpn');
define('DB_PASSWORD', 'xgV#e24rZ8XPSC4*Ei@c');

//$odb = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);

//Timezone Config
//putenv("TZ=America/New_York");

//rad
define('radDB_HOST', '107.191.96.57');
define('radDB_NAME', 'radius');
define('radDB_USERNAME', 'radius');
define('radDB_PASSWORD', 'Tj16i$l&HnKFnCMfaWU1');
//$rad = new PDO('mysql:host=' . radDB_HOST . ';dbname=' . radDB_NAME, radDB_USERNAME, radDB_PASSWORD);
//rad end

try {
 $rad = new PDO('mysql:host=' . radDB_HOST . ';dbname=' . radDB_NAME, radDB_USERNAME, radDB_PASSWORD);
 $rad->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 } catch (PDOException $e) {
     //echo 'EXT Connection failed: ' . $e->getMessage(); //debug
    // die();
    die ( 'If you are seeing this error, one of our EXT databases are offline. Check our Twitter for updates: https://twitter.com/CrypticVPN' );
 }

 try {
 $odb = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
 $odb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 } catch (PDOException $e) {
     //echo 'LOC Connection failed: ' . $e->getMessage();
     // die();
    die ( 'If you are seeing this error, one of our core databases are offline. Check our Twitter for updates: https://twitter.com/CrypticVPN' );

 } 

$smtpauthtype = 'tls'; // tls - ssl
$smtpauthstat = true; // true - false

//Re-captcha keys, you need to obtain them from https://www.google.com/recaptcha/intro/index.html
$rpublickey = '6LfovRYTAAAAANjfP3hQCS7zAPVlGTixJFABboqG';
$rprivatekey = '6LfovRYTAAAAAMmgjtheufc4OYF2YIsYo9OzUrdV';

//This is the encryption key used to encrypt your password data while saving on database. Please change this only ONCE, before you add anything to database.
//If you change this after adding vpn server, it will fail to decrypt the password of the server and will result in non-functional script until you update the password of the server with new encryption key.
//Make sure your key is 32 characters in length and alphanumeric only!
$encryptionKey = "c62ra0wdj0b9Zjfqpym0cm9p6PwSd2yN";

function encryptData($value, $key){
   $text = $value;
   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);
   return base64_encode($crypttext);
}

function decryptData($value, $key){
   $crypttext = base64_decode($value);
   $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
   $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
   $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypttext, MCRYPT_MODE_ECB, $iv);
   return trim($decrypttext);
}
?>
