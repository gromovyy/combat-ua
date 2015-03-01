
-- ---------------------------------------------------
-- ---------------Начинаются таблицы------------------
-- ---------------------------------------------------

-- ---------Создание таблицы "mn_menu"------------
CREATE TABLE IF NOT EXISTS `mn_menu` (
  `id_menu` int(11) NOT NULL AUTO_INCREMENT, 
  `menu` varchar(100) NOT NULL DEFAULT 'Нове меню' COMMENT 'lang', 
  `url` varchar(512) NOT NULL, 
  `id_parent_menu` int(11) DEFAULT NULL, 
  `order` int(11) NOT NULL, 
  `access` int(11) NOT NULL DEFAULT '2', 
  `id_owner` int(11) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `is_visible` int(11) NOT NULL, 
  PRIMARY KEY (`id_menu`), 
  KEY `id_parent_menu` (`id_parent_menu`)
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `mn_menu`  ADD CONSTRAINT `mn_menu_ibfk_2` FOREIGN KEY (`id_parent_menu`) REFERENCES `mn_menu` (`id_menu`) ON DELETE CASCADE
;

