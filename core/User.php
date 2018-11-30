<?php

require_once __DIR__ . '/DataBase.php';

/**
 * Модель локального пользователя в сессии
 */
class User {
	/**
	 * Соль, прикрепляемая к ID пользователя перед хешированием,
	 * для защиты от радужных таблиц
	 */
	const SALT = "X20lUTcKrAAD2cnmYpVMokJShC5eG6M4";
	
	private static $instance;
	
	private $hash;
	private $uid;
	private $money;
	
	private function __construct() {
		if (!isset($_SESSION["uid"])) {
			$this->uid = $this->createUser();
		} else {
			$this->uid = $_SESSION["uid"];
		}
		
		$user = $this->selectUser($this->uid);
		$this->hash = $user["hash"];
		$this->money = intval($user["money"]);
	}
	
	/**
	 * Принудительно очищает закешированные данные (например, money)
	 * для того, чтобы обновить их из БД при следующем запросе
	 */
	public static function clearCache() {
		self::$instance = null;
	}
	
	public function getHash() {
		return $this->hash;
	}
	public function getUid() {
		return $this->uid;
	}
	public function getMoney() {
		return $this->money;
	}
	
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new User();
		}
		
		return self::$instance;
	}
	
	/**
	 * Ищет id пользователя по его хешу
	 * @param $hash string хеш пользователя
	 * @return int идентификатор пользователя
	 */
	public static function getUidByHash($hash) {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("SELECT * FROM users WHERE hash=?");
		$query->bind_param('s', $hash);
		$query->execute();
		
		$result = $query->get_result();
		
		if ($result->num_rows == 0) {
			return -1;
		} else {
			return $result->fetch_assoc()["id"];
		}
	}
	
	/**
	 * Запрашивает из БД информацию о пользователе
	 * @param $uid int идентификатор пользователя
	 * @return array|null данные о пользователе
	 */
	private function selectUser($uid) {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("SELECT * FROM users WHERE id=?");
		$query->bind_param('i', $uid);
		$query->execute();
		
		$result = $query->get_result();
		return $result->fetch_assoc();
	}
	
	/**
	 * Создаёт нового пользователя
	 * @return int userId идентификатор нового пользователя
	 */
	private function createUser() {
		$dataBase = DataBase::getInstance();
		
		$query = $dataBase->prepare("INSERT INTO users(hash, money) VALUES ('', 0)");
		$query->execute();
		$userId = $query->insert_id;
		
		$hash = hash("sha256", strval($userId) . self::SALT);
		$query = $dataBase->prepare("UPDATE users SET hash=? WHERE id=?");
		$query->bind_param(
			"si",
			$hash,
			$userId
		);
		$query->execute();
		
		$_SESSION["uid"] = $userId;
		$_SESSION["companion_id"] = -1;
		
		return $userId;
	}
	
}