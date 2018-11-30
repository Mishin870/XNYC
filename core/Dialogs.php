<?php
require_once __DIR__ . '/DataBase.php';
require_once __DIR__ . '/Events.php';
require_once __DIR__ . '/Utils.php';

/**
 * Модель чатов между пользователями
 */
class Dialogs {
	
	/**
	 * Удалить все диалоги, связанные с юзером
	 * @param $uid int идентификатор юзера
	 */
	public static function fullyDisconnect($uid) {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("SELECT * FROM dialogs WHERE first_id=? OR second_id=?");
		$query->bind_param('ii', $uid, $uid);
		$query->execute();
		$result = $query->get_result();
		
		if ($result->num_rows) {
			$ids = [];
			while ($item = $result->fetch_assoc()) {
				$ids[] = intval($item["first_id"]);
				$ids[] = intval($item["second_id"]);
			}
			
			$query->free_result();
			
			$query = $dataBase->prepare("DELETE FROM dialogs WHERE first_id=? OR second_id=?");
			$query->bind_param('ii', $uid, $uid);
			$query->execute();
			
			$ids = array_unique($ids);
			foreach ($ids as $id) {
				if ($id != $uid) {
					self::sendDisconnect($id, "Собеседник вышел из сети, перезагрузите страницу!");
				}
			}
		}
	}
	
	/**
	 * Найти чат клиенту и соединить с чатом
	 * @param $myId int идентификатор клиента
	 */
	public static function findChat($myId) {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("SELECT * FROM active WHERE is_free=1 AND user_id<>? LIMIT 1");
		$query->bind_param('i', $myId);
		$query->execute();
		$result = $query->get_result();
		
		if ($result->num_rows) {
			$destId = intval($result->fetch_assoc()["user_id"]);
			$query->free_result();
			
			self::connect($myId, $destId);
		}
	}
	
	/**
	 * Получить id текущего собеседника или -1
	 * @param $uid int идентификатор юзера
	 * @return int id текущего собеседника
	 */
	public static function getCompanion($uid) {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("SELECT * FROM dialogs WHERE first_id=? OR second_id=? LIMIT 1");
		$query->bind_param('ii', $uid, $uid);
		$query->execute();
		$result = $query->get_result();
		
		if ($result->num_rows) {
			$row = $result->fetch_assoc();
			$firstId = intval($row["first_id"]);
			$secondId = intval($row["second_id"]);
			
			if ($firstId != $uid) {
				return $firstId;
			} else if ($secondId != $uid) {
				return $secondId;
			}
		}
		
		return -1;
	}
	
	/**
	 * Соединить двух пользователей в диалог
	 * @param $first_id int идентификатор первого юзера
	 * @param $second_id int идентификатор второго юзера
	 */
	private static function connect($first_id, $second_id) {
		Utils::updateActive($first_id, 0);
		Utils::updateActive($second_id, 0);
		
		self::sendConnect($first_id);
		self::sendConnect($second_id);
		
		$dataBase = DataBase::getInstance();
		$query = $dataBase->prepare("INSERT INTO dialogs(first_id, second_id) VALUES (?, ?)");
		$query->bind_param('ii', $first_id, $second_id);
		$query->execute();
	}
	
	/**
	 * Отправить событие найденного собеседника пользователю
	 * @param $uid int идентификатор пользователя
	 */
	private static function sendConnect($uid) {
		Events::addEvent($uid, json_encode(array(
			"type" => 1
		)));
	}
	
	/**
	 * Отправить событие отсоединения собеседника пользователю
	 * @param $uid int идентификатор пользователя
	 * @param $message string причина отсоединения
	 */
	private static function sendDisconnect($uid, $message) {
		Events::addEvent($uid, json_encode(array(
			"type" => 2,
			"message" => $message
		)));
	}
	
}