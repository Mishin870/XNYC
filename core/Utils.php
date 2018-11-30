<?php
require_once __DIR__ . '/DataBase.php';
require_once __DIR__ . '/Dialogs.php';

/**
 * Вспомогательные функции
 */
class Utils {
	
	/**
	 * Обновление времени последнего актива юзера и запись его в актив
	 * @param $uid int идентификатор юзера
	 * @param $isFree int свободен ли юзер для беседы
	 */
	public static function updateActive($uid, $isFree) {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("INSERT INTO active(user_id, last_seen, is_free) VALUES (?, NOW(), ?) ON DUPLICATE KEY UPDATE user_id=?, last_seen=NOW(), is_free=?");
		$query->bind_param('iiii', $uid, $isFree, $uid, $isFree);
		$query->execute();
		
		self::removeInactive();
	}
	
	/**
	 * Обновление времени последнего актива юзера и запись его в актив
	 * @param $uid int идентификатор юзера
	 */
	public static function updateActiveNoState($uid) {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("INSERT INTO active(user_id, last_seen, is_free) VALUES (?, NOW(), 1) ON DUPLICATE KEY UPDATE user_id=?, last_seen=NOW()");
		$query->bind_param('ii', $uid, $uid);
		$query->execute();
		
		self::removeInactive();
	}
	
	/**
	 * Принудительное исключение пользователя из списка активных
	 * (например, при выходе из системы)
	 * @param $uid int идентификатор пользователя
	 */
	public static function setOffline($uid) {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("DELETE FROM active WHERE user_id=?");
		$query->bind_param('i', $uid);
		$query->execute();
	}
	
	/**
	 * Очистка всех неактивных пользователей
	 */
	private static function removeInactive() {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("SELECT * FROM active WHERE last_seen < ADDDATE(NOW(), INTERVAL -2 MINUTE)");
		$query->execute();
		$result = $query->get_result();
		
		while ($item = $result->fetch_assoc()) {
			Dialogs::fullyDisconnect(intval($item["user_id"]));
		}
		
		$query->free_result();
		$query = $dataBase->prepare("DELETE FROM active WHERE last_seen < ADDDATE(NOW(), INTERVAL -2 MINUTE)");
		$query->execute();
	}
	
}