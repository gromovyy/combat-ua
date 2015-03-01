

------------Создание процедуры "cmmnt_proc_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cmmnt_proc_list` $$
CREATE PROCEDURE `cmmnt_proc_list`(IN `v_component` VARCHAR(255), IN `v_id_object` INT)
SELECT *
FROM cmmnt_comment 
WHERE component = v_component
and id_object = v_id_object
ORDER BY create_date$$
DELIMITER ;
 

------------Создание процедуры "cmmnt_proc_list_user"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cmmnt_proc_list_user` $$
CREATE PROCEDURE `cmmnt_proc_list_user`(IN `v_component` VARCHAR(255), IN `v_id_object` INT,IN `v_id_user` INT)
SELECT *
FROM cmmnt_comment 
WHERE component = v_component
and id_object = v_id_object
and id_user = v_id_user

ORDER BY create_date$$
DELIMITER ;
 

------------Создание процедуры "cmmnt_proc_module_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cmmnt_proc_module_list` $$
CREATE PROCEDURE `cmmnt_proc_module_list`(IN `v_id_module` INT)
    NO SQL
SELECT `cmmnt_comment`.*,`mdl_module_result`.`id_module_result`
FROM cmmnt_comment 
inner join mdl_module_result 
on cmmnt_comment.id_object = mdl_module_result.id_module
and cmmnt_comment.id_user = mdl_module_result.id_owner
WHERE component = 'Module'
and id_object = v_id_module
ORDER BY cmmnt_comment.update_date desc$$
DELIMITER ;
 
------------------------------------------------------
------------------Начинаются таблицы------------------
------------------------------------------------------

------------Создание таблицы "cmmnt_comment"------------
CREATE TABLE `cmmnt_comment` (
  `id_comment` int(11) unsigned NOT NULL AUTO_INCREMENT, 
  `component` varchar(255) DEFAULT NULL, 
  `id_user` int(11) unsigned DEFAULT NULL, 
  `id_parent` int(11) NOT NULL, 
  `id_object` int(11) unsigned NOT NULL, 
  `author` varchar(50) DEFAULT NULL, 
  `name` varchar(100) NOT NULL, 
  `text` varchar(4000) DEFAULT NULL, 
  `order` int(11) DEFAULT NULL, 
  `id_owner` int(11) DEFAULT NULL, 
  `create_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `id_session` varchar(255) NOT NULL, 
  PRIMARY KEY (`id_comment`), 
  KEY `id_parent` (`id_parent`), 
  KEY `id_user` (`id_user`), 
  KEY `component` (`component`), 
  KEY `id_object` (`id_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

