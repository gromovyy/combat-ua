CREATE TABLE IF NOT EXISTS `attchmnt_attachment` (
  `id_attachment` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'Новий додаток',
  `component` varchar(100) NOT NULL,
  `id_object` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `is_user_delete` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_attachment`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DELIMITER $$

CREATE  PROCEDURE `attchmnt_proc_delete_object`(IN `v_component` VARCHAR(100), IN `v_id_object` INT)
    NO SQL
DELETE FROM attchmnt_attachment
WHERE component = v_component AND id_object=v_id_object$$

CREATE  PROCEDURE `attchmnt_proc_list`(IN `v_component` VARCHAR(100), IN `v_id_object` INT)
    NO SQL
select * from attchmnt_attachment 
where (component = v_component) and 
      (id_object = v_id_object)$$

DELIMITER ;