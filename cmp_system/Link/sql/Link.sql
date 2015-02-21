
------------------------------------------------------
------------------Начинаются таблицы------------------
------------------------------------------------------

------------Создание таблицы "lnk_link"------------
CREATE TABLE `lnk_link` (
  `id_link` int(11) NOT NULL AUTO_INCREMENT, 
  `name` varchar(100) NOT NULL, 
  `number` int(11) NOT NULL, 
  `id_module` int(11) NOT NULL, 
  `description` text NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  `id_updater` int(11) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  PRIMARY KEY (`id_link`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

------------Создание таблицы "lnk_link_connection"------------
CREATE TABLE `lnk_link_connection` (
  `id_link_connection` int(11) NOT NULL AUTO_INCREMENT, 
  `name` varchar(100) NOT NULL, 
  `number` int(11) NOT NULL, 
  `id_module` int(11) NOT NULL, 
  `description` text NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  `id_updater` int(11) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  PRIMARY KEY (`id_link_connection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

