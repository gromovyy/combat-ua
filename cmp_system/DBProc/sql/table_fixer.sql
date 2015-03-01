DELIMITER $$
CREATE PROCEDURE `table_fix`(IN `v_table` VARCHAR(50))
    NO SQL
BEGIN
  SET @database = DATABASE();
  SET @table = v_table;
		-- `is_visible`
    IF NOT EXISTS(
        SELECT * FROM information_schema.COLUMNS
        WHERE COLUMN_NAME='is_visible' AND TABLE_NAME=@table AND TABLE_SCHEMA=@database
    )
    THEN
       set @s = CONCAT('ALTER TABLE ',@table,'  ADD `is_visible` int(11) NOT NULL;');
    prepare stmt from @s;
    execute stmt;
    deallocate prepare stmt;
    END IF;
		-- `order`    
		IF NOT EXISTS(
        SELECT * FROM information_schema.COLUMNS
        WHERE COLUMN_NAME='order' AND TABLE_NAME=@table AND TABLE_SCHEMA=@database
    )
    THEN
      set @s = CONCAT('ALTER TABLE ',@table,'  ADD COLUMN `order` int(11) NOT NULL AFTER `is_visible`;');
			prepare stmt from @s;
			execute stmt;
			deallocate prepare stmt;
    END IF;
		-- `id_owner`    
		IF NOT EXISTS(
        SELECT * FROM information_schema.COLUMNS
        WHERE COLUMN_NAME='id_owner' AND TABLE_NAME=@table AND TABLE_SCHEMA=@database
    )
    THEN
      set @s = CONCAT('ALTER TABLE ',@table,' ADD COLUMN `id_owner` int(11) NOT NULL AFTER `order`;');
			prepare stmt from @s;
			execute stmt;
			deallocate prepare stmt;
    END IF;
		-- `id_updater`    
		IF NOT EXISTS(
        SELECT * FROM information_schema.COLUMNS
        WHERE COLUMN_NAME='id_updater' AND TABLE_NAME=@table AND TABLE_SCHEMA=@database
    )
    THEN
      set @s = CONCAT('ALTER TABLE ',@table,' ADD COLUMN `id_updater` int(11) NOT NULL AFTER `id_owner`;');
			prepare stmt from @s;
			execute stmt;
			deallocate prepare stmt;
    END IF;
		-- `create_date`    
		IF NOT EXISTS(
        SELECT * FROM information_schema.COLUMNS
        WHERE COLUMN_NAME='create_date' AND TABLE_NAME=@table AND TABLE_SCHEMA=@database
    )
    THEN
      set @s = CONCAT('ALTER TABLE ',@table,' ADD COLUMN `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `id_updater`;');
			prepare stmt from @s;
			execute stmt;
			deallocate prepare stmt;
    END IF;
		-- `update_date`    
		IF NOT EXISTS(
        SELECT * FROM information_schema.COLUMNS
        WHERE COLUMN_NAME='update_date' AND TABLE_NAME=@table AND TABLE_SCHEMA=@database
    )
    THEN
      set @s = CONCAT('ALTER TABLE ',@table,' ADD COLUMN `update_date` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\' AFTER `create_date`');
			prepare stmt from @s;
			execute stmt;
			deallocate prepare stmt;
    END IF;
		
END$$

DELIMITER ;