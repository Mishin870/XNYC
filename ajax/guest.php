<?php
// Вход за гостя с автосозданием аккаунта

session_start();
header('Content-type:application/json;charset=utf-8');

require_once __DIR__ . '/../core/User.php';

unset($_SESSION["uid"]);
$user = User::getInstance();

die(json_encode(array(
	"state" => 1,
	"uid" => $user->getUid(),
	"hash" => $user->getHash(),
	"money" => $user->getMoney()
)));