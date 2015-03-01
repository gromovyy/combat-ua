

------------Создание процедуры "artcl_proc_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `artcl_proc_list` $$
CREATE PROCEDURE `artcl_proc_list`(IN `v_component` VARCHAR(50), IN `v_id_object` INT, IN `v_object` VARCHAR(50))
    NO SQL
SELECT ab.*,
       a.title,
       a.content,
       a.category,
       a.id_owner
FROM artcl_article_bind ab
LEFT JOIN artcl_article a
ON ab.id_article = a.id_article
WHERE (ab.component = v_component 
       AND ab.id_object=v_id_object 
       AND ab.object = v_object)
ORDER BY ab.order DESC$$
DELIMITER ;
 

------------Создание процедуры "artcl_proc_list_all"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `artcl_proc_list_all` $$
CREATE PROCEDURE `artcl_proc_list_all`()
    NO SQL
SELECT * FROM artcl_article AS a
LEFT JOIN ( SELECT url_photo, title_photo, id_object, default_folder
FROM pht_photo p LEFT JOIN pht_photo_type t
ON p.photo_type = t.photo_type
WHERE p.component =  "Article"
GROUP BY id_object
) AS ph
ON a.id_article = ph.id_object
ORDER BY a.create_date DESC$$
DELIMITER ;
 

------------Создание процедуры "artcl_proc_view"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `artcl_proc_view` $$
CREATE PROCEDURE `artcl_proc_view`(IN `v_id_article_bind` INT)
    NO SQL
SELECT ab.*,
       a.title,
       a.content,
       a.category,
       a.id_owner
FROM artcl_article_bind ab
LEFT JOIN artcl_article a
ON ab.id_article = a.id_article
WHERE ab.id_article_bind = v_id_article_bind$$
DELIMITER ;
 

------------Создание процедуры "artcl_proc_view_title"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `artcl_proc_view_title` $$
CREATE PROCEDURE `artcl_proc_view_title`(IN `name` VARCHAR(255) CHARSET utf8)
    NO SQL
SELECT * FROM artcl_article
WHERE get_letter_only(title) = get_letter_only(name)$$
DELIMITER ;
 
------------------------------------------------------
------------------Начинаются таблицы------------------
------------------------------------------------------

------------Создание таблицы "artcl_article"------------
CREATE TABLE `artcl_article` (
  `id_article` int(11) NOT NULL AUTO_INCREMENT, 
  `title` varchar(255) NOT NULL DEFAULT 'Нова стаття' COMMENT 'lang', 
  `content` text NOT NULL COMMENT 'lang', 
  `url_source` varchar(255) NOT NULL COMMENT 'lang', 
  `url_download` varchar(255) NOT NULL, 
  `category` varchar(100) NOT NULL DEFAULT 'static', 
  `is_visible` int(11) NOT NULL, 
  `start_date` date NOT NULL, 
  `finish_date` date NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `id_owner` int(11) NOT NULL, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) DEFAULT NULL, 
  `order` int(11) NOT NULL, 
  PRIMARY KEY (`id_article`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

------------Создание таблицы "artcl_article_bind"------------
CREATE TABLE `artcl_article_bind` (
  `id_article_bind` int(11) NOT NULL AUTO_INCREMENT, 
  `id_article` int(11) NOT NULL, 
  `component` varchar(50) NOT NULL, 
  `object` varchar(50) NOT NULL, 
  `id_object` int(11) NOT NULL, 
  `order` int(11) NOT NULL, 
  `is_visible` int(11) NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  `id_updater` int(11) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  PRIMARY KEY (`id_article_bind`), 
  KEY `id_article` (`id_article`), 
  KEY `component` (`component`), 
  KEY `object` (`object`), 
  KEY `id_object` (`id_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

------------Создание таблицы "artcl_article_content"------------
CREATE TABLE `artcl_article_content` (
  `id_article_content` int(11) NOT NULL AUTO_INCREMENT, 
  `id_article` int(11) NOT NULL, 
  `content_type` varchar(20) NOT NULL DEFAULT 'text', 
  `id_content` int(11) NOT NULL, 
  `content` text NOT NULL COMMENT 'lang', 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `id_owner` int(11) NOT NULL, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) DEFAULT NULL, 
  `order` int(11) NOT NULL, 
  PRIMARY KEY (`id_article_content`), 
  KEY `id_article` (`id_article`), 
  KEY `id_content` (`id_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

