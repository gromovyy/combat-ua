

------------Создание процедуры "emlpsath_proc_authenticate"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `emlpsath_proc_authenticate` $$
CREATE PROCEDURE `emlpsath_proc_authenticate`(IN `v_email` VARCHAR(255), IN `v_password` VARCHAR(255))
    NO SQL
BEGIN
  set @salt = 'pdzjnb';
  select id_user, role from `usr_user`
   where password=md5(concat(v_password, @salt)) 
   and email=v_email and status='active';
END$$
DELIMITER ;
 

------------Создание процедуры "emlpsath_proc_get_key"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `emlpsath_proc_get_key` $$
CREATE PROCEDURE `emlpsath_proc_get_key`(IN `v_id_user` INT)
    NO SQL
SELECT email_key from `usr_user`
WHERE id_user = v_id_user$$
DELIMITER ;
 

------------Создание процедуры "emlpsath_proc_get_user_by_email"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `emlpsath_proc_get_user_by_email` $$
CREATE PROCEDURE `emlpsath_proc_get_user_by_email`(IN `v_email` VARCHAR(255))
    NO SQL
SELECT id_user FROM `usr_user` 
WHERE email = v_email$$
DELIMITER ;
 

------------Создание процедуры "emlpsath_proc_register_user"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `emlpsath_proc_register_user` $$
CREATE PROCEDURE `emlpsath_proc_register_user`(IN `v_email` VARCHAR(255), IN `v_password` VARCHAR(255))
    NO SQL
BEGIN
  if (select count(*) from `usr_user` where email=v_email) = 0 then
    set @salt = 'pdzjnb';
    insert into `usr_user` (`role`, `email` , `password`, `email_key`,`status`)
      values("registered", v_email, md5(concat(v_password, @salt)), md5(v_email),'wait');
  end if;
END$$
DELIMITER ;
 

------------Создание процедуры "emlpsath_proc_set_active"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `emlpsath_proc_set_active` $$
CREATE PROCEDURE `emlpsath_proc_set_active`(IN `v_email_key` VARCHAR(100), IN `v_id_user` INT)
    NO SQL
BEGIN
UPDATE `usr_user` SET status = 'active' WHERE
email_key = v_email_key and id_user = v_id_user;

SELECT id_user FROM `usr_user`
WHERE id_user=v_id_user and status = 'active';
END$$
DELIMITER ;
 