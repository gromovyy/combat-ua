

------------Создание процедуры "updtr_proc_alter_table"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `updtr_proc_alter_table` $$
CREATE PROCEDURE `updtr_proc_alter_table`(IN `v_table` VARCHAR(200), IN `v_column` VARCHAR(200), IN `v_query` VARCHAR(200))
    NO SQL
BEGIN
 SET @table = v_table;
 SET @column = v_column;
 SET @query = v_query;
 SET @database = DATABASE();
   IF NOT EXISTS(
       SELECT * FROM information_schema.COLUMNS
       WHERE TABLE_NAME=@table AND TABLE_SCHEMA= @database AND COLUMN_NAME = @column
   )
   THEN
      set @s = CONCAT('ALTER TABLE ',@table,'  ADD ', @column, ' ', @query);
   prepare stmt from @s;
   execute stmt;
   deallocate prepare stmt;
   END IF;
END$$
DELIMITER ;
 

------------Создание процедуры "updtr_proc_get_procedures"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `updtr_proc_get_procedures` $$
CREATE PROCEDURE `updtr_proc_get_procedures`()
BEGIN
-- Переменная для имени процедуры
DECLARE vProcName VARCHAR(50);
-- переменная hadlera
DECLARE done integer default 0;
-- Объявление курсора
DECLARE ProcCursor Cursor for SELECT ROUTINE_NAME
FROM INFORMATION_SCHEMA.ROUTINES
WHERE ROUTINE_TYPE="PROCEDURE"
AND ROUTINE_SCHEMA=DATABASE();

DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done=1;
-- /* открытие курсора */
Open ProcCursor;
/*извлекаем данные */
WHILE done = 0 DO
FETCH ProcCursor INTO vProcName;
SET @s = CONCAT('SHOW CREATE PROCEDURE ',vProcName);

prepare stmt from @s;
execute stmt;
deallocate prepare stmt;
END WHILE;
-- /*закрытие курсора */
Close ProcCursor;
END$$
DELIMITER ;
 

------------Создание процедуры "updtr_proc_get_tables"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `updtr_proc_get_tables` $$
CREATE PROCEDURE `updtr_proc_get_tables`()
BEGIN
-- Переменная для имени процедуры
DECLARE vTableName VARCHAR(50);
-- переменная hadlera
DECLARE done integer default 0;
-- Объявление курсора
DECLARE TableCursor Cursor for SELECT TABLE_NAME
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA=DATABASE();

DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done=1;
-- /* открытие курсора */
Open TableCursor;
/*извлекаем данные */
WHILE done = 0 DO
FETCH TableCursor INTO vTableName;
SET @s = CONCAT('SHOW CREATE Table ',vTableName);

prepare stmt from @s;
execute stmt;
deallocate prepare stmt;
END WHILE;
-- /*закрытие курсора */
Close TableCursor;
END$$
DELIMITER ;
 