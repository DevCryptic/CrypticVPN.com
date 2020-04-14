<?php
session_start();
unset($_SESSION['aUsername']);
unset($_SESSION['aID']);
unset($_SESSION['aSecret']);
session_destroy();
header('location: index.php');
?>