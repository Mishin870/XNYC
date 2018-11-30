<?php
// Вход за существующий аккаунт. POST: hash - хеш аккаунта

session_start();
header('Content-type:application/json;charset=utf-8');

require_once __DIR__ . '/../core/User.php';

$hash = $_POST["hash"];
$uid = User::getUidByHash($hash);

if ($uid != -1) {
	$_SESSION["uid"] = $uid;
	$_SESSION["companion_id"] = -1;
	$user = User::getInstance();
	
	die(json_encode(array(
		"state" => 1,
		"uid" => $uid,
		"hash" => $hash,
		"money" => $user->getMoney()
	)));
} else {
	die(json_encode(array(
		"state" => 0,
		"message" => "Пользователь не найден!"
	)));
}