--
-- Структура таблицы `vd_video`
--

CREATE TABLE IF NOT EXISTS `vd_video` (
  `id_video` int(11) NOT NULL AUTO_INCREMENT,
  `component` varchar(100) NOT NULL,
  `id_object` int(11) NOT NULL,
  `title_video` varchar(255) NOT NULL,
  `url_video` varchar(255) NOT NULL DEFAULT 'default.jpg',
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id_video`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `vd_video`
--

-- --------------------------------------------------------

DELIMITER $$

CREATE  PROCEDURE `vd_proc_delete_object`(IN `v_component` VARCHAR(100), IN `v_id_object` INT)
    NO SQL
DELETE FROM vd_video
WHERE component = v_component AND id_object=v_id_object$$

CREATE  PROCEDURE `vd_proc_list`(IN `v_component` VARCHAR(100), IN `v_id_object` INT)
    NO SQL
select * from vd_video
where (component = v_component) and 
      (id_object = v_id_object)$$


CREATE  PROCEDURE `vd_proc_view`(IN `v_id_video` INT)
    NO SQL
select * from vd_video 
where (id_video = v_id_video)$$


DELIMITER ;