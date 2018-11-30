<?php
// Инициализация сессии и зачистка старых чатов после входа в аккаунт

session_start();
header('Content-type:application/json;charset=utf-8');

require_once __DIR__ . '/../core/DataBase.php';
require_once __DIR__ . '/../core/Utils.php';
require_once __DIR__ . '/../core/Dialogs.php';

if (!isset($_SESSION["uid"])) {
	die(json_encode(array(
		"state" => 0,
		"message" => "Необходима авторизация!"
	)));
}
$uid = intval($_SESSION["uid"]);

Utils::updateActive($uid, 1);
Dialogs::fullyDisconnect($uid);
Dialogs::findChat($uid);

die(json_encode(array(
	"state" => 1
)));