<?php

$output = ["ok" => false, "msg" => "", "minv" => $_GET["minv"], "valid" => $_GET["valid"]];
if(empty($_GET["minv"]) || !isset($_GET["valid"])) {
	$output["msg"] = "params missing";
	die(json_encode($output));
}

$id = $_GET["minv"];
$valid = $_GET["valid"];

try {
	$pdo = new PDO("mysql:host=localhost;dbname=cvpn_data", "cvpn", 'xgV#e24rZ8XPSC4*Ei@c');
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $pdo->prepare("UPDATE `payment_logs` SET `valid` = :valid WHERE `id` = :minv");
	$stmt->execute(["minv" => $id, "valid" => $valid]);

	$output["ok"] = true;
	$output["msg"] = "fine";

	die(json_encode($output));

} catch(Exception $ex) {
	$output["msg"] = "exception: ".$ex->getMessage();
	die(json_encode($output));
}

