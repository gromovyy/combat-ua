

------------Создание процедуры "mmbr_proc_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `mmbr_proc_list` $$
CREATE PROCEDURE `mmbr_proc_list`()
    NO SQL
SELECT *, (ph.url_photo="default.png")as is_photo FROM mmbr_member AS a
LEFT JOIN ( SELECT url_photo, title_photo, id_object, default_folder
FROM pht_photo p LEFT JOIN pht_photo_type t
ON p.photo_type = t.photo_type
WHERE p.component =  "Member"
GROUP BY id_object
) AS ph
ON a.id_person = ph.id_object
WHERE a.is_visible = 1
ORDER BY is_photo, a.surname ASC$$
DELIMITER ;
 

------------Создание процедуры "mmbr_proc_payments"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `mmbr_proc_payments` $$
CREATE PROCEDURE `mmbr_proc_payments`()
    NO SQL
SELECT * FROM `usr_payment`
ORDER BY `usr_payment`.`create_date` DESC$$
DELIMITER ;
 

------------Создание процедуры "mmbr_proc_teacher_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `mmbr_proc_teacher_list` $$
CREATE PROCEDURE `mmbr_proc_teacher_list`()
    NO SQL
SELECT id_person, id_owner, name,  surname FROM `mmbr_member`
WHERE is_teacher = 1$$
DELIMITER ;
 

------------Создание процедуры "mmbr_proc_user_payment"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `mmbr_proc_user_payment` $$
CREATE PROCEDURE `mmbr_proc_user_payment`(IN `v_id_user` INT)
    NO SQL
SELECT * FROM `usr_payment` WHERE id_user = v_id_user$$
DELIMITER ;
 

------------Создание процедуры "mmbr_proc_view"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `mmbr_proc_view` $$
CREATE PROCEDURE `mmbr_proc_view`(IN `v_id_person` INT)
    NO SQL
SELECT * FROM mmbr_member AS a
LEFT JOIN ( SELECT id_photo, url_photo, title_photo,
id_object, default_folder
FROM pht_photo p LEFT JOIN pht_photo_type t
ON p.photo_type = t.photo_type
WHERE p.component =  "Member"
) AS ph
ON a.id_photo = ph.id_photo
WHERE a.id_person = v_id_person
ORDER BY a.create_date DESC$$
DELIMITER ;
 

------------Создание процедуры "mmbr_proc_view_by_user_id"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `mmbr_proc_view_by_user_id` $$
CREATE PROCEDURE `mmbr_proc_view_by_user_id`(IN `v_id_user` INT)
    NO SQL
SELECT * from mmbr_member
WHERE id_user = v_id_user$$
DELIMITER ;
 
-- ---------------------------------------------------
-- ---------------Начинаются таблицы------------------
-- ---------------------------------------------------

-- ---------Создание таблицы "mmbr_member"------------
CREATE TABLE IF NOT EXISTS `mmbr_member` (
  `id_member` int(11) unsigned NOT NULL AUTO_INCREMENT, 
  `id_country` tinyint(4) unsigned DEFAULT NULL, 
  `id_user` int(11) unsigned DEFAULT NULL, 
  `id_owner` int(11) DEFAULT NULL, 
  `id_team` smallint(6) unsigned DEFAULT NULL, 
  `id_master` smallint(6) unsigned DEFAULT NULL, 
  `id_photo` int(11) DEFAULT NULL, 
  `name` varchar(100) NOT NULL DEFAULT 'ІМ''Я', 
  `surname` varchar(100) NOT NULL DEFAULT 'ПРІЗВИЩЕ', 
  `patronymic` varchar(100) NOT NULL, 
  `nickname` varchar(50) DEFAULT 'НІК', 
  `birthday` date NOT NULL, 
  `region` varchar(100) DEFAULT NULL, 
  `city` varchar(100) DEFAULT 'Київ', 
  `index` varchar(255) DEFAULT NULL, 
  `street` varchar(100) DEFAULT NULL, 
  `house` varchar(100) DEFAULT NULL, 
  `room` varchar(100) DEFAULT NULL, 
  `organization` varchar(255) DEFAULT NULL, 
  `phone` varchar(20) DEFAULT NULL, 
  `email` varchar(100) NOT NULL DEFAULT 'EMAIL', 
  `site` varchar(200) DEFAULT NULL, 
  `annotation` varchar(1024) NOT NULL DEFAULT 'Коротка інформація  про себе', 
  `jury_rating` smallint(6) unsigned DEFAULT NULL, 
  `role` varchar(100) DEFAULT 'player', 
  `create_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP, 
  `team_confirmation` tinyint(4) DEFAULT NULL, 
  `is_jury` int(11) DEFAULT '0', 
  `is_teacher` tinyint(1) NOT NULL DEFAULT '0', 
  `is_visible` int(11) DEFAULT '1', 
  `order` int(11) NOT NULL, 
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `id_updater` int(11) NOT NULL, 
  PRIMARY KEY (`id_member`), 
  KEY `id_owner` (`id_owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

