-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.7.16 - MySQL Community Server (GPL)
-- Операционная система:         Win64
-- HeidiSQL Версия:              9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры для таблица xnyc.active
CREATE TABLE IF NOT EXISTS `active` (
  `user_id` int(11) NOT NULL,
  `last_seen` datetime NOT NULL,
  `is_free` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы xnyc.active: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `active` DISABLE KEYS */;
INSERT INTO `active` (`user_id`, `last_seen`, `is_free`) VALUES
	(80, '2018-11-26 23:47:41', 1);
/*!40000 ALTER TABLE `active` ENABLE KEYS */;

-- Дамп структуры для таблица xnyc.dialogs
CREATE TABLE IF NOT EXISTS `dialogs` (
  `first_id` int(11) NOT NULL,
  `second_id` int(11) NOT NULL,
  UNIQUE KEY `dialogs_first_id_uindex` (`first_id`),
  UNIQUE KEY `dialogs_second_id_uindex` (`second_id`),
  CONSTRAINT `dialogs_active_user_id_fk` FOREIGN KEY (`first_id`) REFERENCES `active` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `dialogs_active_user_id_fk_2` FOREIGN KEY (`second_id`) REFERENCES `active` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы xnyc.dialogs: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `dialogs` DISABLE KEYS */;
/*!40000 ALTER TABLE `dialogs` ENABLE KEYS */;

-- Дамп структуры для таблица xnyc.events
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '-1',
  `event` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `events_active_user_id_fk` (`user_id`),
  CONSTRAINT `events_active_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `active` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы xnyc.events: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;

-- Дамп структуры для таблица xnyc.gifts
CREATE TABLE IF NOT EXISTS `gifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы xnyc.gifts: ~10 rows (приблизительно)
/*!40000 ALTER TABLE `gifts` DISABLE KEYS */;
INSERT INTO `gifts` (`id`, `name`, `price`) VALUES
	(1, 'Козёл и кот', 100),
	(2, 'Снегурочка', 100),
	(3, 'Кот', 100),
	(4, 'Шампанское', 100),
	(5, 'Снеговик', 100),
	(6, 'Кот и колокольчик', 100),
	(7, 'Поевший кот', 100),
	(8, 'Мандаринка', 100),
	(9, 'Ёлка', 100),
	(10, 'Хо-хо-хо', 100);
/*!40000 ALTER TABLE `gifts` ENABLE KEYS */;

-- Дамп структуры для таблица xnyc.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` text NOT NULL,
  `money` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы xnyc.users: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `hash`, `money`) VALUES
	(76, '8eafbc109bf66f0c14361bb60d81fe492308a81388fbdd343ff0e8ecdd65447f', 0),
	(77, 'aedcfbcec938ce6b42d8710482b65c827842f3942a8ff640563f4cb81386c0b5', 0),
	(78, '8bb7899c235eeb608616fea7365c2204aa2c52d864a19b3864e900b77553b71a', 0),
	(79, 'bd5749c6a7af3fd28f4fa0879318641fdc2feb3cdcd8d02e5a1da1265f36f132', 7),
	(80, '3f4b06599f03ca76ea19164fa625b3b3cb1f5475adbdb28078aa99db93adca93', 1318),
	(81, '9e440a5bd86e329698170532df17aa38c4e2ea7919dea6c226a5543c26c63ef1', 30);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
