<?php
session_start();
unset($_SESSION['rUsername']);
unset($_SESSION['rID']);
unset($_SESSION['rSecret']);
session_destroy();
header('location: index.php');
?>