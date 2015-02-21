

------------Создание процедуры "bs_proc_get_events"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `bs_proc_get_events` $$
CREATE PROCEDURE `bs_proc_get_events`()
    NO SQL
SELECT ip.`IP`,cmp.`component`,cmp.`version`,st.`site`,m.`message`,ufrom.`url` as url_from,uto.`url` as url_to,e.*
FROM `bs_event` e
LEFT JOIN `bs_ip` ip on ip.id_IP = e.id_IP
LEFT JOIN `bs_component` cmp on cmp.`id_component` = e.`id_component`
LEFT JOIN `bs_site` st on st.`id_site` = e.`id_site`
LEFT JOIN `bs_message` m on m.`id_message` = e.`id_message`
LEFT JOIN `bs_url` ufrom on ufrom.`id_url` = e.`id_url_from`
LEFT JOIN `bs_url` uto on uto.`id_url` = e.`id_url_to`
WHERE 1
ORDER BY e.`create_date` DESC$$
DELIMITER ;
 

------------Создание процедуры "bs_proc_get_row_owner"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `bs_proc_get_row_owner` $$
CREATE PROCEDURE `bs_proc_get_row_owner`(IN `v_table` VARCHAR(255), IN `v_row_id` INT)
    NO SQL
BEGIN

select `COLUMN_NAME` from  `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
        where `TABLE_SCHEMA` = database() && `TABLE_NAME` = v_table && CONSTRAINT_NAME = 'PRIMARY'
        into @table_pk;
  	
set @s = concat(
        'select id_owner from `',v_table,
        '` where `',@table_pk,'` = ',v_row_id,';');
      prepare stmt from @s;
      execute stmt;
      deallocate prepare stmt;
 END$$
DELIMITER ;
 

------------Создание процедуры "bs_proc_insert_event"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `bs_proc_insert_event` $$
CREATE PROCEDURE `bs_proc_insert_event`(IN `v_site` VARCHAR(255), IN `v_component` VARCHAR(255), IN `v_version` VARCHAR(255), IN `v_IP` VARCHAR(255), IN `v_message` VARCHAR(255), IN `v_type` INT, IN `v_url_from` VARCHAR(255), IN `v_url_to` VARCHAR(255), IN `v_notes` VARCHAR(255))
    COMMENT 'Stored Proc name: bs_proc_insert_event'
BEGIN
      DECLARE v_id_site INT DEFAULT NULL;
    	DECLARE v_id_url_from INT DEFAULT NULL;
    	DECLARE v_id_url_to INT DEFAULT NULL;
    	DECLARE v_id_component INT DEFAULT NULL;
    	DECLARE v_id_IP INT DEFAULT NULL;
    	DECLARE v_id_message INT DEFAULT NULL;
    	
    	IF (v_site IS NOT NULL) THEN
    		INSERT IGNORE INTO `bs_site` (`id_site` , `site`) VALUES('',  `v_site`);
    		SELECT id_site FROM bs_site where site = v_site into v_id_site;
    	END IF;
    	
    	IF (v_url_from IS NOT NULL) THEN
    		INSERT IGNORE INTO `bs_url` (`id_url` , `url`) VALUES('',  `v_url_from`);
    		SELECT id_url FROM bs_url where url = v_url_from into v_id_url_from;
    	END IF;
    	
    	IF (v_url_to IS NOT NULL) THEN
    		INSERT IGNORE INTO `bs_url` (`id_url` , `url`) VALUES('',  `v_url_to`);
    		SELECT id_url FROM bs_url where url = v_url_to into v_id_url_to;
    	END IF;
    	
    	IF (v_component IS NOT NULL) THEN
    		INSERT IGNORE INTO `bs_component`(`id_component`, `component`, `version`) VALUES('',  `v_component`, `v_version`);
    		SELECT id_component FROM `bs_component` where (component = v_component and version = v_version) into v_id_component;
    	END IF;
    	
    	IF (v_IP IS NOT NULL) THEN
    		INSERT IGNORE INTO `bs_ip` (`id_IP` ,`IP`) VALUES('',  `v_IP`);
    		SELECT id_IP FROM bs_ip where IP = v_IP into v_id_IP;
    	END IF;
    	
    	IF (v_message IS NOT NULL) THEN
    		INSERT IGNORE INTO `bs_message` (`id_message` ,`message`) VALUES('',  `v_message`);
    		SELECT id_message FROM bs_message where message = v_message into v_id_message;
    	END IF;
    	
    	INSERT INTO  `bs_event` (`id_site` ,`id_component` ,`id_IP` ,`type` ,`id_message` ,`id_url_from` ,`id_url_to` ,`notes`)
    	VALUES (  v_id_site,  v_id_component,  v_id_IP,  v_type,  v_id_message, v_id_url_from,  v_id_url_to, v_notes);
END$$
DELIMITER ;
 
-- ---------------------------------------------------
-- ---------------Начинаются таблицы------------------
-- ---------------------------------------------------

-- ---------Создание таблицы "bs_component"------------
CREATE TABLE IF NOT EXISTS `bs_component` (
  `id_component` int(11) unsigned NOT NULL AUTO_INCREMENT, 
  `component` varchar(255) NOT NULL, 
  `version` varchar(50) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  PRIMARY KEY (`id_component`), 
  UNIQUE KEY `component_unique` (`component`, `version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "bs_country_all"------------
CREATE TABLE IF NOT EXISTS `bs_country_all` (
  `id_country` tinyint(4) unsigned NOT NULL AUTO_INCREMENT, 
  `name` varchar(50) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  PRIMARY KEY (`id_country`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "bs_event"------------
CREATE TABLE IF NOT EXISTS `bs_event` (
  `id_event` int(11) NOT NULL AUTO_INCREMENT, 
  `id_site` int(11) unsigned DEFAULT '0', 
  `id_component` int(11) unsigned DEFAULT NULL, 
  `id_IP` int(11) unsigned DEFAULT NULL, 
  `id_message` int(11) unsigned DEFAULT NULL, 
  `id_url_from` int(11) unsigned DEFAULT NULL, 
  `id_url_to` int(11) unsigned DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `type` int(11) DEFAULT NULL, 
  `notes` varchar(1000) DEFAULT NULL, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_owner` int(11) NOT NULL, 
  `id_updater` int(11) NOT NULL, 
  PRIMARY KEY (`id_event`), 
  KEY `id_url_from` (`id_url_from`), 
  KEY `id_url_to` (`id_url_to`), 
  KEY `id_component` (`id_component`), 
  KEY `id_message` (`id_message`), 
  KEY `id_IP` (`id_IP`), 
  KEY `id_site` (`id_site`)
  
  
  
  
  
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "bs_ip"------------
CREATE TABLE IF NOT EXISTS `bs_ip` (
  `id_IP` int(11) unsigned NOT NULL AUTO_INCREMENT, 
  `id_country` int(11) unsigned NOT NULL, 
  `id_city` int(11) unsigned NOT NULL, 
  `IP` varchar(50) CHARACTER SET cp1251 DEFAULT NULL, 
  `type` int(11) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  PRIMARY KEY (`id_IP`), 
  UNIQUE KEY `IP_unique` (`IP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "bs_message"------------
CREATE TABLE IF NOT EXISTS `bs_message` (
  `id_message` int(11) unsigned NOT NULL AUTO_INCREMENT, 
  `message` varchar(255) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  PRIMARY KEY (`id_message`), 
  UNIQUE KEY `message_unique` (`message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "bs_site"------------
CREATE TABLE IF NOT EXISTS `bs_site` (
  `id_site` int(11) unsigned NOT NULL AUTO_INCREMENT, 
  `site` varchar(255) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  PRIMARY KEY (`id_site`), 
  UNIQUE KEY `site_uniqju` (`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "bs_url"------------
CREATE TABLE IF NOT EXISTS `bs_url` (
  `id_url` int(11) unsigned NOT NULL AUTO_INCREMENT, 
  `url` varchar(255) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `id_owner` int(11) NOT NULL, 
  PRIMARY KEY (`id_url`), 
  UNIQUE KEY `url_unique` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `bs_event`  ADD CONSTRAINT `bs_event_ibfk_1` FOREIGN KEY (`id_component`) REFERENCES `bs_component` (`id_component`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bs_event_ibfk_2` FOREIGN KEY (`id_site`) REFERENCES `bs_site` (`id_site`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bs_event_ibfk_3` FOREIGN KEY (`id_IP`) REFERENCES `bs_ip` (`id_IP`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bs_event_ibfk_4` FOREIGN KEY (`id_message`) REFERENCES `bs_message` (`id_message`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bs_event_ibfk_5` FOREIGN KEY (`id_url_from`) REFERENCES `bs_url` (`id_url`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bs_event_ibfk_6` FOREIGN KEY (`id_url_to`) REFERENCES `bs_url` (`id_url`) ON DELETE CASCADE ON UPDATE CASCADE
;

