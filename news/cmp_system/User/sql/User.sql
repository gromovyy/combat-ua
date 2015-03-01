

------------Создание процедуры "usr_proc_add_user_into_group"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `usr_proc_add_user_into_group` $$
CREATE PROCEDURE `usr_proc_add_user_into_group`(IN `v_id_user` INT UNSIGNED, IN `v_group_name` VARCHAR(64) CHARSET utf8)
    NO SQL
BEGIN
  declare v_id_group int unsigned;
    select id_group from usr_group
      where name = v_group_name
      into v_id_group;
    if v_id_group is not null then
      insert ignore into usr_group_to_user
        values(v_id_group, v_id_user);
    end if;
END$$
DELIMITER ;
 

------------Создание процедуры "usr_proc_delete"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `usr_proc_delete` $$
CREATE PROCEDURE `usr_proc_delete`(IN `v_id_user` INT)
    NO SQL
DELETE FROM usr_User
WHERE id_user = v_id_user$$
DELIMITER ;
 

------------Создание процедуры "usr_proc_get_parent_action_resource_row_id"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `usr_proc_get_parent_action_resource_row_id` $$
CREATE PROCEDURE `usr_proc_get_parent_action_resource_row_id`(IN `v_id_action` INT UNSIGNED, INOUT `v_resource_row_id` INT UNSIGNED)
    NO SQL
BEGIN
  set v_resource_row_id = null;
END$$
DELIMITER ;
 

------------Создание процедуры "usr_proc_get_role"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `usr_proc_get_role` $$
CREATE PROCEDURE `usr_proc_get_role`(IN `v_id_user` INT)
    NO SQL
SELECT role FROM usr_user
WHERE id_user = v_id_user$$
DELIMITER ;
 

------------Создание процедуры "usr_proc_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `usr_proc_list` $$
CREATE PROCEDURE `usr_proc_list`()
    NO SQL
select id_user as id, email, role
from `usr_user`$$
DELIMITER ;
 

------------Создание процедуры "usr_proc_list_user_data"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `usr_proc_list_user_data` $$
CREATE PROCEDURE `usr_proc_list_user_data`()
    NO SQL
BEGIN
  SELECT `COLUMN_NAME` 
  FROM `INFORMATION_SCHEMA`.`COLUMNS` 
  WHERE TABLE_SCHEMA = DATABASE() && TABLE_NAME = 'usr_user' && 
  COLUMN_NAME != 'id_user';
END$$
DELIMITER ;
 

------------Создание процедуры "usr_proc_login_from_cookie"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `usr_proc_login_from_cookie` $$
CREATE PROCEDURE `usr_proc_login_from_cookie`(IN `v_cookie` VARCHAR(256), IN `v_IP` VARCHAR(50))
    NO SQL
begin
declare v_id_ip int;

IF (v_IP IS NOT NULL) THEN
  INSERT IGNORE INTO `bs_ip` (`id_IP` ,`IP`) VALUES('',  `v_IP`);
  SELECT id_IP FROM bs_ip where IP = v_IP into v_id_ip;
END IF;

select id_user as id, auth_component as auth, role
from `usr_user`
where cookie = v_cookie;
end$$
DELIMITER ;
 

------------Создание процедуры "usr_proc_save_to_cookie"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `usr_proc_save_to_cookie` $$
CREATE PROCEDURE `usr_proc_save_to_cookie`(IN `v_IP` VARCHAR(50), IN `v_auth` VARCHAR(64), IN `v_id_user` INT)
    NO SQL
begin
declare v_cookie varchar(256);
declare v_id_ip int;
set v_cookie = MD5(RAND());

IF (v_IP IS NOT NULL) THEN
  INSERT IGNORE INTO `bs_ip` (`id_IP` ,`IP`) VALUES('',  `v_IP`);
  SELECT id_IP FROM bs_ip where IP = v_IP into v_id_ip;
END IF;

update `usr_user`
set cookie = v_cookie, cookie_id_ip = v_id_ip, auth_component=v_auth
where id_user = v_id_user;

select v_cookie;
end$$
DELIMITER ;
 

------------Создание процедуры "usr_proc_set_current_id_user"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `usr_proc_set_current_id_user` $$
CREATE PROCEDURE `usr_proc_set_current_id_user`(IN `id_user` INT UNSIGNED)
    NO SQL
BEGIN
  set @current_id_user = id_user;
END$$
DELIMITER ;
 

------------Создание процедуры "usr_proc_set_user_data"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `usr_proc_set_user_data` $$
CREATE PROCEDURE `usr_proc_set_user_data`(IN `v_id_user` INT, IN `v_key` VARCHAR(68) CHARSET utf8, IN `v_value` TEXT CHARSET utf8)
    NO SQL
BEGIN
  SET @s = CONCAT('update `usr_user` set `',v_key,'` = '',v_value,'' where `id_user` = ',v_id_user,';');
    PREPARE stmt FROM @s;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$
DELIMITER ;
 

------------Создание процедуры "usr_proc_view"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `usr_proc_view` $$
CREATE PROCEDURE `usr_proc_view`(IN `v_id_user` INT)
    NO SQL
select id_user as id, email, role
from `usr_user`
where id_user=v_id_user$$
DELIMITER ;
 

------------Создание процедуры "usr_proc_view"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `usr_proc_view` $$
CREATE PROCEDURE `usr_proc_view`(IN `v_id_user` INT)
    NO SQL
select id_user as id, email, role
from `usr_user`
where id_user=v_id_user$$
DELIMITER ;
 
-- ---------------------------------------------------
-- ---------------Начинаются таблицы------------------
-- ---------------------------------------------------

-- ---------Создание таблицы "usr_payment"------------
CREATE TABLE IF NOT EXISTS `usr_payment` (
  `id_payment` int(11) unsigned NOT NULL AUTO_INCREMENT, 
  `id_owner` int(11) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `id_user` int(11) NOT NULL, 
  `comment` varchar(500) NOT NULL, 
  `value` int(11) NOT NULL, 
  `invoice` varchar(250) NOT NULL, 
  PRIMARY KEY (`id_payment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "usr_user"------------
CREATE TABLE IF NOT EXISTS `usr_user` (
  `id_user` int(11) unsigned NOT NULL AUTO_INCREMENT, 
  `cookie` varchar(128) DEFAULT NULL, 
  `cookie_id_ip` int(11) NOT NULL, 
  `auth_component` varchar(64) DEFAULT NULL, 
  `role` varchar(100) DEFAULT 'registered', 
  `email` varchar(255) NOT NULL, 
  `password` varchar(255) NOT NULL, 
  `email_key` varchar(100) DEFAULT NULL, 
  `status` varchar(100) NOT NULL DEFAULT 'wait', 
  `id_owner` int(11) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "usr_user"------------
CREATE TABLE IF NOT EXISTS `usr_user` (
  `id_user` int(11) unsigned NOT NULL AUTO_INCREMENT, 
  `cookie` varchar(128) DEFAULT NULL, 
  `cookie_id_ip` int(11) NOT NULL, 
  `auth_component` varchar(64) DEFAULT NULL, 
  `role` varchar(100) DEFAULT 'registered', 
  `email` varchar(255) NOT NULL, 
  `password` varchar(255) NOT NULL, 
  `email_key` varchar(100) DEFAULT NULL, 
  `status` varchar(100) NOT NULL DEFAULT 'wait', 
  `id_owner` int(11) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

