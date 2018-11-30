<?php
// Обработчик "долгих запросов"
// Каждую секунду просматривает новые события в базе данных, адресованные
// данному пользователю. Всего запрос работает 20 секунд

session_start();
header('Content-type:application/json;charset=utf-8');

require_once __DIR__ . '/../core/DataBase.php';
require_once __DIR__ . '/../core/Utils.php';

if (!isset($_SESSION["uid"])) {
	die(json_encode(array(
		"state" => 0,
		"message" => "Необходима авторизация!"
	)));
}
$uid = intval($_SESSION["uid"]);

set_time_limit(0);

$dataBase = DataBase::getInstance();

Utils::updateActiveNoState($uid);

// Подготовленное выражение для просмотра событий текущего юзера
$query = $dataBase->prepare("SELECT * FROM events WHERE user_id=?");
$query->bind_param('i', $uid);

$iteration = 20;

ignore_user_abort(false);
session_write_close();
while ($iteration-- >= 0) {
	$query->execute();
	$result = $query->get_result();
	
	if ($result->num_rows) {
		$events = [];
		
		while ($item = $result->fetch_assoc()) {
			$eventJson = $item["event"];
			$eventParsed = json_decode($eventJson);
			$eventType = intval($eventParsed->type);
			
			if ($eventType === 1) {
				$eventJson = json_encode(array(
					"type" => 1
				));
			}
			
			$events[] = $eventJson;
		}
		
		$cleanQuery = $dataBase->prepare("DELETE FROM events WHERE user_id=?");
		$cleanQuery->bind_param('i', $uid);
		$cleanQuery->execute();
		
		die(json_encode(array(
			"state" => 2,
			"message" => json_encode($events)
		)));
	}
	
	if(connection_status() != CONNECTION_NORMAL) {
		break;
	}
	sleep(1);
}

die(json_encode(array(
	"state" => 1
)));