

------------Создание процедуры "pht_proc_delete_object"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `pht_proc_delete_object` $$
CREATE PROCEDURE `pht_proc_delete_object`(IN `v_component` VARCHAR(100), IN `v_id_object` INT)
    NO SQL
DELETE FROM pht_photo
WHERE component = v_component AND id_object=v_id_object$$
DELIMITER ;
 

------------Создание процедуры "pht_proc_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `pht_proc_list` $$
CREATE PROCEDURE `pht_proc_list`(IN `v_component` VARCHAR(50), IN `v_id_object` INT, IN `v_object` VARCHAR(50))
    NO SQL
SELECT 	pb.*, 
       	pt.default_folder,
	pt.default_img,		
	pt.is_full_save,
	pt.is_full_preview,
	pt.is_user_delete,	
	pt.inside_color,
        p.id_photo as `id_photo`,
        p.title_photo,
	p.url_photo,
	p.order
FROM pht_photo_bind pb 
LEFT JOIN pht_photo_type pt
ON pb.photo_type = pt.photo_type
LEFT JOIN pht_photo p
ON pb.id_photo = p.id_photo
where (component = v_component) and 
      (id_object = v_id_object) and
      (object = v_object)$$
DELIMITER ;
 

------------Создание процедуры "pht_proc_set_default_img"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `pht_proc_set_default_img` $$
CREATE PROCEDURE `pht_proc_set_default_img`(IN `v_id_photo` INT)
    NO SQL
BEGIN
DECLARE v_default_img VARCHAR(100);
SELECT t.default_img FROM 
pht_photo_type t inner join pht_photo p
ON t.photo_type = p.photo_type
WHERE p.id_photo = v_id_photo into v_default_img;

UPDATE pht_photo SET url_photo = v_default_img, title_photo = NULL
WHERE id_photo = v_id_photo;
END$$
DELIMITER ;
 

------------Создание процедуры "pht_proc_view"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `pht_proc_view` $$
CREATE PROCEDURE `pht_proc_view`(IN `v_id_photo` INT)
    NO SQL
select 	pb.*, 
       	pt.default_folder,
	pt.default_img,	
	pt.small_resize_type,
	pt.small_max_width,	
	pt.small_max_height,	
	pt.normal_resize_type,	
	pt.normal_max_width,
	pt.normal_max_height,	
	pt.is_full_save,
	pt.is_full_preview,
	pt.is_user_delete,	
	pt.inside_color,
        p.id_photo as `id_photo`,
        p.title_photo,
	p.url_photo,
	p.order
FROM pht_photo_bind pb 
LEFT JOIN pht_photo_type pt
ON pb.photo_type = pt.photo_type
LEFT JOIN pht_photo p
ON pb.id_photo = p.id_photo
WHERE (pb.id_photo_bind = v_id_photo)$$
DELIMITER ;
 
-- ---------------------------------------------------
-- ---------------Начинаются таблицы------------------
-- ---------------------------------------------------

-- ---------Создание таблицы "pht_photo"------------
CREATE TABLE IF NOT EXISTS `pht_photo` (
  `id_photo` int(11) NOT NULL AUTO_INCREMENT, 
  `title_photo` varchar(255) NOT NULL, 
  `url_photo` varchar(255) NOT NULL, 
  `order` int(11) NOT NULL, 
  `photo_type` varchar(100) NOT NULL DEFAULT 'article', 
  `id_owner` int(11) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  PRIMARY KEY (`id_photo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "pht_photo_bind"------------
CREATE TABLE IF NOT EXISTS `pht_photo_bind` (
  `id_photo_bind` int(11) NOT NULL AUTO_INCREMENT, 
  `id_photo` int(11) NOT NULL, 
  `component` varchar(50) NOT NULL, 
  `object` varchar(50) NOT NULL, 
  `id_object` int(11) NOT NULL, 
  `photo_type` varchar(50) NOT NULL DEFAULT 'article', 
  `order` int(11) NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  `id_updater` int(11) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  PRIMARY KEY (`id_photo_bind`), 
  KEY `id_photo` (`id_photo`), 
  KEY `component` (`component`), 
  KEY `object` (`object`), 
  KEY `id_object` (`id_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "pht_photo_type"------------
CREATE TABLE IF NOT EXISTS `pht_photo_type` (
  `photo_type` varchar(100) NOT NULL, 
  `default_folder` varchar(255) NOT NULL, 
  `default_img` varchar(255) NOT NULL, 
  `small_resize_type` varchar(100) NOT NULL DEFAULT 'inside', 
  `small_max_width` int(11) NOT NULL, 
  `small_max_height` int(11) NOT NULL, 
  `normal_resize_type` varchar(100) NOT NULL DEFAULT 'inside', 
  `normal_max_width` int(11) NOT NULL, 
  `normal_max_height` int(11) NOT NULL, 
  `is_full_save` tinyint(1) NOT NULL, 
  `is_full_preview` tinyint(1) NOT NULL, 
  `is_user_delete` tinyint(1) NOT NULL, 
  `inside_color` varchar(100) NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `is_visible` int(11) NOT NULL, 
  `order` int(11) NOT NULL, 
  PRIMARY KEY (`photo_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

