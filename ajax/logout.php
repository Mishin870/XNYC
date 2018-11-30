<?php
// Выход из аккаунта

session_start();
header('Content-type:application/json;charset=utf-8');

if (isset($_SESSION["uid"])) {
	$uid = intval($_SESSION["uid"]);
	unset($_SESSION["uid"]);
	
	require_once __DIR__ . '/../core/Dialogs.php';
	require_once __DIR__ . '/../core/Utils.php';
	
	Dialogs::fullyDisconnect($uid);
	Utils::setOffline($uid);
}

die(json_encode(array(
	"state" => 1
)));