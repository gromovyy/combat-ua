

------------Создание процедуры "attchmnt_proc_delete_object"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `attchmnt_proc_delete_object` $$
CREATE PROCEDURE `attchmnt_proc_delete_object`(IN `v_component` VARCHAR(100), IN `v_id_object` INT)
    NO SQL
DELETE FROM attchmnt_attachment
WHERE component = v_component AND id_object=v_id_object$$
DELIMITER ;
 

------------Создание процедуры "attchmnt_proc_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `attchmnt_proc_list` $$
CREATE PROCEDURE `attchmnt_proc_list`(IN `v_component` VARCHAR(50), IN `v_id_object` INT, IN `v_object` VARCHAR(50))
    NO SQL
SELECT 	ab.*, 
	a.dir, 
        a.file_name, 
        a.title
FROM attchmnt_attachment_bind ab
LEFT JOIN attchmnt_attachment a 
ON ab.id_attachment = a.id_attachment
where (ab.component = v_component) and 
      (ab.id_object = v_id_object) and
      (ab.object = v_object)$$
DELIMITER ;
 

------------Создание процедуры "attchmnt_proc_view"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `attchmnt_proc_view` $$
CREATE PROCEDURE `attchmnt_proc_view`(IN `v_id_attachment` INT)
    NO SQL
SELECT ab.*,
       a.dir,
       a.file_name,
       a.title
FROM attchmnt_attachment_bind ab
LEFT JOIN attchmnt_attachment a
ON ab.id_attachment = a.id_attachment
WHERE id_attachment_bind = v_id_attachment$$
DELIMITER ;
 
------------------------------------------------------
------------------Начинаются таблицы------------------
------------------------------------------------------

------------Создание таблицы "attchmnt_attachment"------------
CREATE TABLE `attchmnt_attachment` (
  `id_attachment` int(11) NOT NULL AUTO_INCREMENT, 
  `dir` varchar(50) NOT NULL, 
  `file_name` varchar(255) NOT NULL, 
  `title` varchar(255) NOT NULL DEFAULT 'Новий додаток', 
  `component` varchar(100) NOT NULL, 
  `id_object` int(11) NOT NULL, 
  `order` int(11) NOT NULL, 
  `is_user_delete` tinyint(1) NOT NULL DEFAULT '1', 
  `id_owner` int(11) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  PRIMARY KEY (`id_attachment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

------------Создание таблицы "attchmnt_attachment_bind"------------
CREATE TABLE `attchmnt_attachment_bind` (
  `id_attachment_bind` int(11) NOT NULL AUTO_INCREMENT, 
  `id_attachment` int(11) NOT NULL, 
  `component` varchar(50) NOT NULL, 
  `object` varchar(50) NOT NULL, 
  `id_object` int(11) NOT NULL, 
  `order` int(11) NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  `id_updater` int(11) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  PRIMARY KEY (`id_attachment_bind`), 
  KEY `id_attachment` (`id_attachment`), 
  KEY `component` (`component`), 
  KEY `object` (`object`), 
  KEY `id_object` (`id_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

