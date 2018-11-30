<?php
require_once __DIR__ . '/DataBase.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Events.php';

/**
 * Модель валюты в чате
 */
class Money {
	
	/**
	 * Добавить юзеру монеток за сообщение
	 * @param $uid int идентификатор юзера
	 * @param $message string сообщение
	 */
	public static function giveMoney($uid, $message) {
		$cost = intval(strlen($message));
		
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("UPDATE users SET money = money + ? WHERE id=?");
		$query->bind_param('ii', $cost, $uid);
		$query->execute();
		
		$query = $dataBase->prepare("SELECT money FROM users WHERE id=?");
		$query->bind_param('i', $uid);
		$query->execute();
		$money = intval($query->get_result()->fetch_assoc()["money"]);
		
		Events::addEvent($uid, json_encode(array(
			"type" => 3,
			"money" => $money
		)));
		
		User::clearCache();
	}
	
	/**
	 * Попытаться потратить некоторое количество валюты у пользователя
	 * @param $cost int цена
	 * @param $uid int идентификатор пользователя
	 * @return bool успех операции
	 */
	public static function consume($cost, $uid) {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("SELECT money FROM users WHERE id=?");
		$query->bind_param('i', $uid);
		$query->execute();
		$result = $query->get_result();
		
		$money = intval($result->fetch_assoc()["money"]);
		
		if ($money >= intval($cost)) {
			$query = $dataBase->prepare("UPDATE users SET money = money - ? WHERE id=?");
			$query->bind_param('ii', $cost, $uid);
			$query->execute();
			
			$query = $dataBase->prepare("SELECT money FROM users WHERE id=?");
			$query->bind_param('i', $uid);
			$query->execute();
			$money = intval($query->get_result()->fetch_assoc()["money"]);
			
			Events::addEvent($uid, json_encode(array(
				"type" => 3,
				"money" => $money
			)));
			
			User::clearCache();
			
			return true;
		} else {
			return false;
		}
	}
	
}