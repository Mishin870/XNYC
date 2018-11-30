<?php
// Отправка сообщения другому пользователю

session_start();
header('Content-type:application/json;charset=utf-8');

require_once __DIR__ . '/../core/Messages.php';
require_once __DIR__ . '/../core/Utils.php';
require_once __DIR__ . '/../core/Gifts.php';
require_once __DIR__ . '/../core/Money.php';

if (!isset($_SESSION["uid"])) {
	die(json_encode(array(
		"state" => 0,
		"message" => "Необходима авторизация!"
	)));
}
if (!isset($_POST["message"])) {
	die(json_encode(array(
		"state" => 0,
		"message" => "Сообщение отсутствует!"
	)));
}

$uid = intval($_SESSION["uid"]);
$message = $_POST["message"];

Utils::updateActiveNoState($uid);

if (substr($message, 0, 1) === "/") {
	$message = substr($message, 1);
	$arr = explode(" ", $message);
	$cmd = $arr[0];
	
	if ($cmd === "gift") {
		if ($arr[1] === "list") {
			$gifts = Gifts::getAll();
			$result = "[Система]<br>Список всех подарков:";
			
			foreach ($gifts as $gift) {
				$result .= "<br>" . $gift["name"] . " (" . $gift["price"] . " монет, id = " . $gift["id"] . ")";
			}
			
			Events::sendLocalMessage($uid, $result);
		} else {
			$gid = intval($arr[1]);
			
			if ($gid !== 0) {
				Gifts::buy($uid, $gid);
			}
		}
	} else {
		Events::sendLocalMessage($uid, "[Система]<br>/gift list - посмотреть все подарки<br>/gift id - подарить подарок с указанным id");
	}
} else {
	Messages::sendMessage($uid, $message);
}

die(json_encode(array(
	"state" => 1
)));