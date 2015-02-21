

------------Создание процедуры "vd_proc_delete_object"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `vd_proc_delete_object` $$
CREATE PROCEDURE `vd_proc_delete_object`(IN `v_component` VARCHAR(100), IN `v_id_object` INT)
    NO SQL
DELETE FROM vd_video
WHERE component = v_component AND id_object=v_id_object$$
DELIMITER ;
 

------------Создание процедуры "vd_proc_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `vd_proc_list` $$
CREATE PROCEDURE `vd_proc_list`(IN `v_component` VARCHAR(50), IN `v_id_object` INT, IN `v_object` VARCHAR(50))
    NO SQL
SELECT vb.*,
       v.title_video,
       v.url_video
FROM vd_video_bind vb
LEFT JOIN vd_video v
ON vb.id_video = v.id_video
where (vb.component = v_component) and 
      (vb.id_object = v_id_object) and
      (vb.object = v_object)$$
DELIMITER ;
 

------------Создание процедуры "vd_proc_view"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `vd_proc_view` $$
CREATE PROCEDURE `vd_proc_view`(IN `v_id_video` INT)
    NO SQL
SELECT vb.*,
       v.title_video,
       v.url_video
       FROM vd_video_bind vb
LEFT JOIN vd_video v
ON vb.id_video = v.id_video
where (id_video_bind = v_id_video)$$
DELIMITER ;
 
------------------------------------------------------
------------------Начинаются таблицы------------------
------------------------------------------------------

------------Создание таблицы "vd_video"------------
CREATE TABLE `vd_video` (
  `id_video` int(11) NOT NULL AUTO_INCREMENT, 
  `component` varchar(100) NOT NULL, 
  `id_object` int(11) NOT NULL, 
  `title` varchar(1024) NOT NULL DEFAULT 'Назва відео', 
  `url_video` varchar(255) NOT NULL, 
  `order` int(11) NOT NULL, 
  `id_owner` int(11) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  PRIMARY KEY (`id_video`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

------------Создание таблицы "vd_video_bind"------------
CREATE TABLE `vd_video_bind` (
  `id_video_bind` int(11) NOT NULL AUTO_INCREMENT, 
  `id_video` int(11) NOT NULL, 
  `component` varchar(50) NOT NULL, 
  `object` varchar(50) NOT NULL, 
  `is_visible` int(11) NOT NULL, 
  `id_object` int(11) NOT NULL, 
  `order` int(11) NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  `id_updater` int(11) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  PRIMARY KEY (`id_video_bind`), 
  KEY `id_video` (`id_video`), 
  KEY `component` (`component`), 
  KEY `object` (`object`), 
  KEY `id_object` (`id_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

