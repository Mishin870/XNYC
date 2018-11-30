<?php

require_once __DIR__ . '/Dialogs.php';
require_once __DIR__ . '/Events.php';
require_once __DIR__ . '/Money.php';

/**
 * Модель сообщений (обёртка для событий)
 */
class Messages {
	
	/**
	 * Отправить сообщение пользователю
	 * @param $uid int идентификатор пользователя
	 * @param $message string сообщение
	 */
	public static function sendMessage($uid, $message) {
		Money::giveMoney($uid, $message);
		
		$companionId = Dialogs::getCompanion($uid);
		if ($companionId == -1) {
			return;
		}
		
		Events::addEvent($companionId, json_encode(array(
			"type" => 0,
			"message" => $message
		)));
	}
	
	/**
	 * Отправить подарок пользователю (не путать с покупкой. покупка
	 * подарка вызывает эту функцию для его отправки в чат)
	 * @param $uid int идентификатор пользователя
	 * @param $gid int идентификатор подарка
	 */
	public static function sendGift($uid, $gid) {
		$companionId = Dialogs::getCompanion($uid);
		if ($companionId == -1) {
			return;
		}
		
		Events::addEvent($companionId, json_encode(array(
			"type" => 0,
			"message" => "Подарок:<br><img src='img/gifts/" . $gid . ".png'/>"
		)));
		Events::addEvent($uid, json_encode(array(
			"type" => 5,
			"message" => "Подарок:<br><img src='img/gifts/" . $gid . ".png'/>"
		)));
	}
	
}