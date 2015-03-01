

------------Создание процедуры "rl_proc_get_cmp_rule"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `rl_proc_get_cmp_rule` $$
CREATE PROCEDURE `rl_proc_get_cmp_rule`(IN `v_role` VARCHAR(100))
    NO SQL
SELECT * from rl_rule
WHERE role = v_role$$
DELIMITER ;
 

------------Создание процедуры "rl_proc_get_rules"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `rl_proc_get_rules` $$
CREATE PROCEDURE `rl_proc_get_rules`(IN `v_role` VARCHAR(100))
    NO SQL
SELECT * from rl_view_rule
WHERE role = v_role$$
DELIMITER ;
 

------------Создание процедуры "rl_proc_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `rl_proc_list` $$
CREATE PROCEDURE `rl_proc_list`()
BEGIN
  SELECT * FROM rl_role;
END$$
DELIMITER ;
 

------------Создание процедуры "rl_proc_role"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `rl_proc_role` $$
CREATE PROCEDURE `rl_proc_role`(IN `v_id_role` INT)
BEGIN
  SELECT role FROM rl_role WHERE id_role=v_id_role;
END$$
DELIMITER ;
 

------------Создание процедуры "rl_proc_rule_table"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `rl_proc_rule_table` $$
CREATE PROCEDURE `rl_proc_rule_table`()
BEGIN
  SELECT * FROM rl_view_rule;
END$$
DELIMITER ;
 
-- ---------------------------------------------------
-- ---------------Начинаются таблицы------------------
-- ---------------------------------------------------

-- ---------Создание таблицы "rl_role"------------
CREATE TABLE IF NOT EXISTS `rl_role` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT, 
  `role` varchar(50) NOT NULL DEFAULT 'Название роли', 
  `id_owner` int(11) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  `is_visible` int(11) NOT NULL, 
  `order` int(11) NOT NULL, 
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "rl_rule"------------
CREATE TABLE IF NOT EXISTS `rl_rule` (
  `id_rule` int(11) NOT NULL AUTO_INCREMENT, 
  `component` varchar(100) NOT NULL, 
  `role` varchar(100) NOT NULL, 
  `select` int(11) NOT NULL DEFAULT '1', 
  `insert` int(11) NOT NULL DEFAULT '1', 
  `update` int(11) NOT NULL DEFAULT '1', 
  `delete` int(11) NOT NULL DEFAULT '1', 
  `visibility` int(11) NOT NULL DEFAULT '1', 
  `order` int(11) NOT NULL, 
  `id_owner` int(11) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  PRIMARY KEY (`id_rule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------Создание таблицы "rl_view_rule"------------
CREATE TABLE IF NOT EXISTS `rl_view_rule` (
  `id_view_rule` int(11) NOT NULL AUTO_INCREMENT, 
  `name` varchar(100) DEFAULT NULL, 
  `role` varchar(100) DEFAULT NULL, 
  `component` varchar(100) DEFAULT NULL, 
  `view` varchar(100) DEFAULT NULL, 
  `access` int(11) DEFAULT '-1', 
  `order` int(11) NOT NULL, 
  `id_owner` int(11) DEFAULT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  PRIMARY KEY (`id_view_rule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

