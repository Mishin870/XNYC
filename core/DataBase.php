<?php

/**
 * Модель базы данных
 */
class DataBase {
	const DB_USERNAME = "root";
	const DB_PASSWORD = null;
	const DB_DATABASE = "xnyc";
	const DB_HOST = null;
	const DB_PORT = null;
	
	/* @var DataBase */
	private static $instance;
	
	private $mysqli;
	
	/**
	 * Создаёт новое соединение с базой данных
	 *
	 * @throws Exception если не получается подключиться к БД
	 */
	private function __construct() {
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		
		$this->mysqli = new mysqli(
			self::DB_HOST,
			self::DB_USERNAME,
			self::DB_PASSWORD,
			self::DB_DATABASE,
			self::DB_PORT
		);
		
		if ($this->mysqli->connect_errno) {
			throw new Exception("Can't connect to the DataBase!");
		}
	}
	
	public static function getInstance() {
		if (is_null(self::$instance)) {
			try {
				self::$instance = new DataBase();
			} catch (Exception $exception) {
				var_dump($exception->getTraceAsString());
				exit;
			}
		}
		
		return self::$instance;
	}
	
	/**
	 * Создаёт подготовленное выражение
	 * @param $query string выражение
	 * @return mysqli_stmt подготовленное выражение
	 */
	public function prepare($query) {
		return $this->mysqli->prepare($query);
	}
	
	/**
	 * Возвращает список текущих ошибок mysql
	 * @return array
	 */
	public function errors() {
		return $this->mysqli->error_list;
	}
	
}