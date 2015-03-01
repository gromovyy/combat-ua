

------------Создание процедуры "lng_proc_set_current_lang"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `lng_proc_set_current_lang` $$
CREATE PROCEDURE `lng_proc_set_current_lang`(IN `lang` CHAR(2))
    NO SQL
BEGIN
  set @current_lang = lang;
END$$
DELIMITER ;
 
-- ---------------------------------------------------
-- ---------------Начинаются таблицы------------------
-- ---------------------------------------------------

-- ---------Создание таблицы "lng_text"------------
CREATE TABLE IF NOT EXISTS `lng_text` (
  `table_name` varchar(50) NOT NULL, 
  `field_name` varchar(50) NOT NULL, 
  `row_id` int(11) NOT NULL, 
  `ru` text NOT NULL, 
  `uk` text NOT NULL, 
  `en` text NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  PRIMARY KEY (`table_name`, `field_name`, `row_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "lng_varchar"------------
CREATE TABLE IF NOT EXISTS `lng_varchar` (
  `table_name` varchar(50) NOT NULL, 
  `field_name` varchar(50) NOT NULL, 
  `row_id` int(11) NOT NULL, 
  `ru` varchar(255) NOT NULL, 
  `uk` varchar(255) NOT NULL, 
  `en` varchar(255) NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  PRIMARY KEY (`table_name`, `field_name`, `row_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

