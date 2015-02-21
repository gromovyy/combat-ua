

------------Создание процедуры "mssngr_proc_user_email_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `mssngr_proc_user_email_list` $$
CREATE PROCEDURE `mssngr_proc_user_email_list`()
    NO SQL
SELECT 
  prs.name, 
  prs.surname, 
  prs.id_owner, 
  usr.email 
FROM mmbr_member AS prs
INNER JOIN (SELECT id_user, email FROM usr_user WHERE role !="administrator") as usr
ON prs.id_owner = usr.id_user
GROUP BY usr.email
ORDER BY prs.surname ASC$$
DELIMITER ;
 
-- ---------------------------------------------------
-- ---------------Начинаются таблицы------------------
-- ---------------------------------------------------

-- ---------Создание таблицы "mssngr_messanger"------------
CREATE TABLE IF NOT EXISTS `mssngr_messanger` (
  `id_email` int(11) NOT NULL AUTO_INCREMENT, 
  `to` varchar(255) NOT NULL, 
  `from` varchar(255) NOT NULL, 
  `title` varchar(255) NOT NULL, 
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `id_owner` int(11) DEFAULT NULL, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  PRIMARY KEY (`id_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

