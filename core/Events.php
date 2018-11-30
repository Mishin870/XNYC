<?php
require_once __DIR__ . '/DataBase.php';

/**
 * Модель событий для LongPoll'а
 */
class Events {
	
	/**
	 * Послать событие юзеру
	 * @param $uid int идентификатор юзера
	 * @param $jsonMessage string строка JSON-сообщения
	 */
	public static function addEvent($uid, $jsonMessage) {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("INSERT INTO events(user_id, event) VALUES (?, ?)");
		$query->bind_param(
			'is',
			$uid,
			$jsonMessage
		);
		$query->execute();
	}
	
	/**
	 * Обёртка для отправки текстовых сообщений только самому пользователю<br>
	 * Используется для отправки системных сообщений пользователю в ответ
	 * на его команды
	 * @param $uid int идентификатор пользователя
	 * @param $message string сообщение
	 */
	public static function sendLocalMessage($uid, $message) {
		self::addEvent($uid, json_encode(array(
			"type" => 0,
			"message" => $message
		)));
	}
	
}