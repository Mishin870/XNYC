<?php
require_once __DIR__ . '/DataBase.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Events.php';
require_once __DIR__ . '/Money.php';

/**
 * Модель подарков
 */
class Gifts {
	
	/**
	 * @return array список всех подарков в системе
	 */
	public static function getAll() {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("SELECT * FROM gifts");
		$query->execute();
		$result = $query->get_result();
		
		$gifts = [];
		while ($item = $result->fetch_assoc()) {
			$gifts[] = $item;
		}
		
		return $gifts;
	}
	
	/**
	 * Попытка покупки подарка пользователем
	 * @param $uid int идентификатор пользователя
	 * @param $gid int идентификатор подарка
	 */
	public static function buy($uid, $gid) {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("SELECT * FROM gifts WHERE id=?");
		$query->bind_param('i', $gid);
		$query->execute();
		$gift = $query->get_result()->fetch_assoc();
		
		$price = intval($gift["price"]);
		
		if (Money::consume($price, $uid)) {
			Messages::sendGift($uid, $gid);
		} else {
			Events::sendLocalMessage($uid, "[Система]<br>Недостаточно монет для покупки подарка!");
		}
	}
	
}