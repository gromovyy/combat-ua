

------------Создание процедуры "st_proc_get_positions"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `st_proc_get_positions` $$
CREATE PROCEDURE `st_proc_get_positions`(IN `v_url` VARCHAR(255) CHARSET utf8)
    NO SQL
SELECT * 
FROM  `st_page_position` p
INNER JOIN (

SELECT url, base_view, id_page_type, is_edit,theme, name as title
FROM  `st_page_type`
) AS t ON t.id_page_type = p.id_page_type
WHERE  SUBSTRING_INDEX(t.url, '/',1) = TRIM(v_url)$$
DELIMITER ;
 

------------Создание процедуры "st_proc_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `st_proc_list` $$
CREATE PROCEDURE `st_proc_list`(IN `v_url` VARCHAR(100) CHARSET utf8)
    NO SQL
SELECT * FROM `st_page_type` WHERE `url` = `v_url`$$
DELIMITER ;
 
-- ---------------------------------------------------
-- ---------------Начинаются таблицы------------------
-- ---------------------------------------------------

-- ---------Создание таблицы "st_page_position"------------
CREATE TABLE IF NOT EXISTS `st_page_position` (
  `id_page_position` int(11) NOT NULL AUTO_INCREMENT, 
  `id_page_type` int(11) DEFAULT NULL, 
  `name` varchar(100) NOT NULL DEFAULT 'position1', 
  `component` varchar(100) NOT NULL DEFAULT 'Site', 
  `view` varchar(100) NOT NULL DEFAULT 'workLayout', 
  `function` varchar(100) NOT NULL DEFAULT 'e_function', 
  `p1` varchar(512) DEFAULT NULL, 
  `p2` varchar(512) DEFAULT NULL, 
  `p3` varchar(512) DEFAULT NULL, 
  `p4` varchar(512) DEFAULT NULL, 
  `p5` varchar(512) DEFAULT NULL, 
  `id_owner` int(11) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `order` int(10) DEFAULT NULL, 
  PRIMARY KEY (`id_page_position`), 
  KEY `id_page_type` (`id_page_type`)
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "st_page_type"------------
CREATE TABLE IF NOT EXISTS `st_page_type` (
  `id_page_type` int(11) NOT NULL AUTO_INCREMENT, 
  `name` varchar(100) NOT NULL DEFAULT 'Новий тип', 
  `url` varchar(255) DEFAULT NULL, 
  `base_view` varchar(100) DEFAULT 'workLayout', 
  `is_edit` tinyint(1) NOT NULL DEFAULT '1', 
  `id_owner` int(11) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `theme` varchar(50) DEFAULT NULL, 
  `order` int(10) DEFAULT NULL, 
  PRIMARY KEY (`id_page_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `st_page_position`  ADD CONSTRAINT `st_page_position_ibfk_1` FOREIGN KEY (`id_page_type`) REFERENCES `st_page_type` (`id_page_type`) ON DELETE CASCADE ON UPDATE CASCADE
;

