

------------Создание процедуры "cntntr_proc_chk_cell"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_chk_cell` $$
CREATE PROCEDURE `cntntr_proc_chk_cell`(IN `v_table_name` VARCHAR(64), IN `v_field_name` VARCHAR(64), OUT  `v_row_id` INT, IN `v_value` TEXT)
BEGIN
  DECLARE v_type varchar(64);
    SELECT `DATA_TYPE` 
     FROM `INFORMATION_SCHEMA`.`COLUMNS` 
     WHERE TABLE_SCHEMA = DATABASE() && TABLE_NAME = v_table_name && COLUMN_NAME = v_field_name && COLUMN_COMMENT LIKE  '%lang%'
     INTO v_type;
    SET @s = CONCAT(
      'SELECT row_id FROM lng_',v_type,' ',
      'WHERE `table_name` = \'',v_table_name,'\' ',
      'and `field_name` = \'',v_field_name,'\' ',
      'and `',@current_lang,'` = \'',v_value,'\' ',
      'INTO @v_row_id;');
    PREPARE stmt FROM @s;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    SET `v_row_id` = @v_row_id;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_country_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_country_list` $$
CREATE PROCEDURE `cntntr_proc_country_list`()
    NO SQL
SELECT id_country as `id`, name as `v`
FROM bs_country_all
ORDER BY name$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_create_row"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_create_row` $$
CREATE PROCEDURE `cntntr_proc_create_row`(IN `v_table_name` VARCHAR(64), IN `v_foreign_fild_name` VARCHAR(64), IN `v_foreign_id` INT)
    NO SQL
BEGIN
  if v_foreign_fild_name is null or v_foreign_id is null then
    set @s = concat(
      'insert into `',v_table_name,'`() ',
      'values();');
  else
    set @s = concat(
      'insert into `',v_table_name,'`(`',v_foreign_fild_name,'`) ', 
      'values(',v_foreign_id,');');
  end if;
  
  prepare stmt from @s;
  execute stmt;
  deallocate prepare stmt;
  select last_insert_id();
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_delete_row"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_delete_row` $$
CREATE PROCEDURE `cntntr_proc_delete_row`(IN `v_table_name` VARCHAR(64), IN `v_row_id` INT)
    NO SQL
BEGIN
  select `COLUMN_NAME` from `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
   where `TABLE_SCHEMA` = database() && `TABLE_NAME` = v_table_name && CONSTRAINT_NAME = 'PRIMARY'
   into @table_pk;
  delete from lng_varchar
   where table_name=v_table_name && row_id=v_row_id;
  delete from lng_text
   where table_name=v_table_name && row_id=v_row_id;
  set @s = concat(
   'delete from ',v_table_name,
   ' where ',@table_pk,'=',v_row_id);
  prepare stmt from @s;
  execute stmt;
  deallocate prepare stmt;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_get_bind"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_get_bind` $$
CREATE PROCEDURE `cntntr_proc_get_bind`(IN `v_table_parent` VARCHAR(50), IN `v_id_parent` INT, IN `v_table_child` VARCHAR(50), IN `v_id_child` INT)
    NO SQL
SELECT * from cntntr_bind 
WHERE   table_parent = v_table_parent AND
	id_parent = v_id_parent AND
        table_child = v_table_child AND
        id_child = v_id_child
LIMIT 1$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_get_cell"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_get_cell` $$
CREATE PROCEDURE `cntntr_proc_get_cell`(IN `v_table_name` VARCHAR(64), IN `v_field_name` VARCHAR(64), IN `v_row_id` INT)
BEGIN
      select REPLACE(v_table_name, SUBSTRING_INDEX(v_table_name,  '_', 1 ),'id') into @table_pk;
       set @s = concat(
          'select `',v_field_name,'` ',
          'from `',v_table_name,'` ',
          'where `',@table_pk,'` = ',v_row_id,' ;');
    
    prepare stmt from @s;
    execute stmt;
    deallocate prepare stmt;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_get_child_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_get_child_list` $$
CREATE PROCEDURE `cntntr_proc_get_child_list`(IN `v_table_parent` VARCHAR(50), IN `v_id_parent` INT, IN `v_table_child` VARCHAR(50))
    NO SQL
BEGIN
select `COLUMN_NAME` from  `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
        where `TABLE_SCHEMA` = database() && `TABLE_NAME` = v_table_child && CONSTRAINT_NAME = 'PRIMARY'
        into @table_pk;
  	
set @s = concat(
        'select t.*, b.order as `bind_order` from `',v_table_child,'` t ',
        'inner join `cntntr_bind` b ',
        'on t.', @table_pk, 
        ' = b.id_child ',
        'where b.table_parent = \'',v_table_parent,'\' AND ',
        'b.id_parent = ',v_id_parent,' ',
        'order by `bind_order`;');
      prepare stmt from @s;
      execute stmt;
      deallocate prepare stmt;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_get_child_order"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_get_child_order` $$
CREATE PROCEDURE `cntntr_proc_get_child_order`(IN `v_table_child` VARCHAR(50), IN `v_id_child` INT, IN `v_table_parent` VARCHAR(50), IN `v_id_parent` INT)
    NO SQL
BEGIN
IF (v_table_parent ='') THEN
	SELECT `table_parent` from `cntntr_bind` 
	WHERE  table_child = v_table_child AND
        id_child = v_id_child
        LIMIT 1 INTO v_table_parent;
        
        SELECT `id_parent` from `cntntr_bind` 
	WHERE  table_child = v_table_child AND
        id_child = v_id_child
        LIMIT 1 INTO v_id_parent;

END IF;
                        
SELECT COUNT(*)+1 as `child_order` from cntntr_bind 
WHERE   table_parent = v_table_parent AND
	id_parent = v_id_parent AND
        table_child = v_table_child AND
        `order` < (SELECT `order` from `cntntr_bind` 
		 WHERE  table_parent = v_table_parent AND
			id_parent = v_id_parent AND
        		table_child = v_table_child AND
        		id_child = v_id_child);
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_get_order"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_get_order` $$
CREATE PROCEDURE `cntntr_proc_get_order`(IN `v_table` VARCHAR(50), IN `v_id_object` INT, IN `v_column_filter` VARCHAR(50), IN `v_filter` VARCHAR(255))
    NO SQL
BEGIN

	select `COLUMN_NAME` from  `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
        where `TABLE_SCHEMA` = database() && `TABLE_NAME` = v_table && CONSTRAINT_NAME = 'PRIMARY'
        into @table_pk;
		
		set @s = concat(
			'SELECT `order` FROM ',v_table,' ',
			'WHERE (`', @table_pk,'` = ',v_id_object,') ');
			
		IF (v_column_filter IS NOT NULL AND TRIM(v_column_filter) <> '') THEN
			set @s = concat(@s, ' and (', v_column_filter,'=\'',v_filter,'\') ');
		END IF;
		
		set @s = concat(@s, ' INTO @v_order;');
		
		prepare stmt from @s;
		execute stmt;
		deallocate prepare stmt;
  
		set @s = concat(  ' SELECT COUNT(*)+1 as `order`',
				  ' FROM ',v_table,
				  ' WHERE  (`order` < ',@v_order,') ');
		
		IF (v_column_filter IS NOT NULL AND TRIM(v_column_filter) <> '') THEN
			set @s = concat(@s, ' and (', v_column_filter,'=\'',v_filter,'\'); ');
		END IF;		
		
		
prepare stmt from @s;
execute stmt;
deallocate prepare stmt;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_get_row"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_get_row` $$
CREATE PROCEDURE `cntntr_proc_get_row`(IN `v_table_name` VARCHAR(50), IN `v_row_id` INT)
    NO SQL
BEGIN     
  SELECT `COLUMN_NAME` FROM  `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE` 
          WHERE `TABLE_SCHEMA` = database() && `TABLE_NAME` = v_table_name && CONSTRAINT_NAME = 'PRIMARY'
          INTO @table_pk;
          
  IF (@table_pk IS NOT NULL) THEN
  SET @s = concat(
          'select * ',
          'from `',v_table_name,'` ',
          'where `',@table_pk,'` = ',v_row_id);
    
  prepare stmt from @s;
  execute stmt;
  deallocate prepare stmt;
  END IF;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_get_row_owner"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_get_row_owner` $$
CREATE PROCEDURE `cntntr_proc_get_row_owner`(IN `v_table` VARCHAR(255), IN `v_row_id` INT)
    NO SQL
BEGIN
select `COLUMN_NAME` from  `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
        where `TABLE_SCHEMA` = database() && `TABLE_NAME` = v_table && CONSTRAINT_NAME = 'PRIMARY'
        into @table_pk;
  	
set @s = concat(
        ' select id_owner from `',v_table,'`',
        ' where `',@table_pk,'` = ',v_row_id,';');
      prepare stmt from @s;
      execute stmt;
      deallocate prepare stmt;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_list"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_list` $$
CREATE PROCEDURE `cntntr_proc_list`(IN `v_table` VARCHAR(50), IN `v_id_owner` INT, IN `v_column_filter` VARCHAR(50), IN `v_filter` VARCHAR(255), IN `v_limit_offset` INT, IN `v_limit_count` INT)
    NO SQL
BEGIN
	set @s = concat(  	' SELECT *',
				' FROM ',v_table,' ');
		IF ((v_column_filter IS NULL OR TRIM(v_column_filter) = '') AND (v_id_owner IS NOT NULL AND v_id_owner <> '' AND  v_id_owner<>0)) THEN
		set @s = concat( @s, ' WHERE  (id_owner = ',v_id_owner,') ');
		END IF;					
		
		IF ((v_column_filter IS NOT NULL AND TRIM(v_column_filter) <> '') AND (v_id_owner IS NOT NULL AND v_id_owner <> '' AND  v_id_owner<>0)) THEN
		set @s = concat( @s, ' WHERE  (id_owner = \'',v_id_owner,'\') and (', v_column_filter,'=',v_filter,') ');
		END IF;	
			
		IF ((v_column_filter IS NOT NULL AND TRIM(v_column_filter) <> '') AND (v_id_owner IS NULL OR v_id_owner = '' OR  v_id_owner=0)) THEN
		set @s = concat( @s, ' WHERE  (', v_column_filter,'=\'',v_filter,'\') ');
		END IF;

		set @s = concat( @s, ' ORDER BY `order` ');
        
		IF ((v_limit_offset IS NOT NULL AND TRIM(v_limit_offset) <> '') AND (v_limit_count IS NOT NULL AND TRIM(v_limit_count)  <> 0)) THEN
		set @s = concat( @s, ' LIMIT ', v_limit_offset,',',v_limit_count);
		END IF;
		
	prepare stmt from @s;
	execute stmt;
	deallocate prepare stmt;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_list_by_parent"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_list_by_parent` $$
CREATE PROCEDURE `cntntr_proc_list_by_parent`(IN `v_table` VARCHAR(50), IN `v_id_owner` INT, IN `v_bind_table1` VARCHAR(50), IN `v_id_bind1` INT, IN `v_bind_table2` VARCHAR(50), IN `v_id_bind2` INT, IN `v_bind_table3` VARCHAR(50), IN `v_id_bind3` INT)
    NO SQL
BEGIN
	DECLARE v_bind_column_pk1 VARCHAR(50) DEFAULT NULL;
    	DECLARE v_bind_column_pk2 VARCHAR(50) DEFAULT NULL;
    	DECLARE v_bind_column_pk3 VARCHAR(50) DEFAULT NULL;
		
	IF (v_bind_table1 IS NOT NULL AND TRIM(v_bind_table1) <> '') THEN
	select `COLUMN_NAME` from  `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
        where `TABLE_SCHEMA` = database() && `TABLE_NAME` = v_bind_table1 && CONSTRAINT_NAME = 'PRIMARY'
        into v_bind_column_pk1;
	END IF;
	
	IF (v_bind_table2 IS NOT NULL AND TRIM(v_bind_table2) <> '') THEN
	select `COLUMN_NAME` from  `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
        where `TABLE_SCHEMA` = database() && `TABLE_NAME` = v_bind_table2 && CONSTRAINT_NAME = 'PRIMARY'
        into v_bind_column_pk2;
	END IF;
	
	IF (v_bind_table3 IS NOT NULL AND TRIM(v_bind_table3) <> '') THEN
	select `COLUMN_NAME` from  `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
        where `TABLE_SCHEMA` = database() && `TABLE_NAME` = v_bind_table3 && CONSTRAINT_NAME = 'PRIMARY'
        into v_bind_column_pk3;
	END IF;
	
			set @s = concat(  'SELECT t.* ');
		IF (v_bind_column_pk2 IS NOT NULL) THEN
			set @s = concat(@s,  ', tb2.', v_bind_column_pk2);
		END IF;
		IF (v_bind_column_pk3 IS NOT NULL) THEN
			set @s = concat(@s,  ', tb3.', v_bind_column_pk3);	
		END IF;
			set @s = concat(@s, ' FROM ',v_table,' t');
		IF (v_bind_column_pk1 IS NOT NULL) THEN
			set @s = concat(@s, ' INNER JOIN ',v_bind_table1,' tb1',
					' ON  t.',v_bind_column_pk1,' = tb1.',v_bind_column_pk1);
		END IF;
		IF (v_bind_column_pk2 IS NOT NULL) THEN
			set @s = concat(@s, ' INNER JOIN ',v_bind_table2,' tb2',
					' ON  tb1.',v_bind_column_pk2,' = tb2.',v_bind_column_pk2);
		END IF;
		IF (v_bind_column_pk3 IS NOT NULL) THEN
			set @s = concat(@s, ' INNER JOIN ',v_bind_table3,' tb3',
					' ON  tb2.',v_bind_column_pk3,' = tb3.',v_bind_column_pk3);
		END IF;
			set @s = concat( @s, ' WHERE  (1 = 1) ');
		IF ((v_id_owner IS NOT NULL AND v_id_owner <> '' AND  v_id_owner<>0)) THEN
			set @s = concat( @s, ' and  (t.id_owner = ',v_id_owner,') ');
		END IF;		
		IF ((v_id_bind1 IS NOT NULL AND v_id_bind1 <> '' AND  v_id_bind1<>0)) THEN
			set @s = concat( @s, ' and  (tb1.',v_bind_column_pk1,' =',v_id_bind1,') ');
		END IF;	
		IF ((v_id_bind2 IS NOT NULL AND v_id_bind2 <> '' AND  v_id_bind2<>0)) THEN
			set @s = concat( @s, ' and  (tb2.',v_bind_column_pk2,' =',v_id_bind2,') ');
		END IF;	
		IF ((v_id_bind3 IS NOT NULL AND v_id_bind3 <> '' AND  v_id_bind3<>0)) THEN
			set @s = concat( @s, ' and  (tb3.',v_bind_column_pk3,' =',v_id_bind3,') ');
		END IF;	
                
                	set @s = concat( @s, ' ORDER BY `order`; ');
                prepare stmt from @s;
		execute stmt;
		deallocate prepare stmt;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_move_left"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_move_left` $$
CREATE PROCEDURE `cntntr_proc_move_left`(IN `v_table` VARCHAR(100), IN `v_id_object` INT, IN `v_column_filter` VARCHAR(50), IN `v_filter` VARCHAR(255))
    NO SQL
BEGIN
 select REPLACE(v_table, SUBSTRING_INDEX(v_table,  '_', 1 ),'id') into @table_pk;
 
	SET @s = concat(
        'SELECT `order` FROM ',v_table,' ',
        'WHERE (`', @table_pk,'` = ',v_id_object,') INTO @v_order;');	
	prepare stmt from @s;
	execute stmt;
	deallocate prepare stmt;
  
  
	set @s = concat(
        'SELECT ',@table_pk,' FROM ',v_table,' ', 
        'WHERE (`order` < ',@v_order,') ');
	
	IF (v_column_filter IS NOT NULL AND TRIM(v_column_filter) <> '' AND v_filter IS NOT NULL AND  TRIM(v_filter) <> '') THEN
		set @s = concat(@s, 'and (', v_column_filter,'=\'',v_filter,'\') ');
	END IF;

	IF (v_column_filter IS NOT NULL AND TRIM(v_column_filter) <> '') THEN
		set @s = concat(@s, 'and (', v_column_filter,' IS NULL ) ');
	END IF;
	
	set @s = concat(@s, 
        ' ORDER BY `order` DESC ',
        ' LIMIT 1 ',
        ' INTO @v_next_id_obj;');
	prepare stmt from @s;
	execute stmt;
	deallocate prepare stmt;
  
	IF (@v_next_id_obj IS NOT NULL) THEN
 	
        set @s = concat(
        'SELECT `order` FROM ',v_table,' ',
        'WHERE (`', @table_pk,'` = ',@v_next_id_obj,') INTO @v_new_order;');
        prepare stmt from @s;
        execute stmt;
        deallocate prepare stmt;
        
 	set @s = concat(
        'UPDATE `',v_table,'` SET `order` = ',@v_new_order,' ', 
        'WHERE (`', @table_pk,'` = ',v_id_object,');');
        prepare stmt from @s;
        execute stmt;
        deallocate prepare stmt;
        
        
        set @s = concat(
        'UPDATE `',v_table,'` SET `order` = ',@v_order,' ', 
        'WHERE (`', @table_pk,'` = ',@v_next_id_obj,');');
        prepare stmt from @s;
        execute stmt;
        deallocate prepare stmt;
END IF;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_move_right"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_move_right` $$
CREATE PROCEDURE `cntntr_proc_move_right`(IN `v_table` VARCHAR(100), IN `v_id_object` INT, IN `v_column_filter` VARCHAR(50), IN `v_filter` VARCHAR(255))
    NO SQL
BEGIN
 select REPLACE(v_table, SUBSTRING_INDEX(v_table,  '_', 1 ),'id') into @table_pk;
 
 
	SET @s = concat(
        'SELECT `order` FROM ',v_table,' ',
        'WHERE (`', @table_pk,'` = ',v_id_object,') INTO @v_order;');	
	prepare stmt from @s;
	execute stmt;
	deallocate prepare stmt;
  
  
	set @s = concat(
        'SELECT ',@table_pk,' FROM ',v_table,' ', 
        'WHERE (`order` > ',@v_order,') ');
	
	IF (v_column_filter IS NOT NULL AND TRIM(v_column_filter) <> '' AND v_filter IS NOT NULL AND  TRIM(v_filter) <> '') THEN
		set @s = concat(@s, 'and (', v_column_filter,'=\'',v_filter,'\') ');
	END IF;

	IF (v_column_filter IS NOT NULL AND TRIM(v_column_filter) <> '') THEN
		set @s = concat(@s, 'and (', v_column_filter,' IS NULL ) ');
	END IF;
	
	set @s = concat(@s, 
        ' ORDER BY `order` ASC ',
        ' LIMIT 1 ',
        ' INTO @v_next_id_obj;');
	prepare stmt from @s;
	execute stmt;
	deallocate prepare stmt;
  
	IF (@v_next_id_obj IS NOT NULL) THEN
 	
        set @s = concat(
        'SELECT `order` FROM ',v_table,' ',
        'WHERE (`', @table_pk,'` = ',@v_next_id_obj,') INTO @v_new_order;');
        prepare stmt from @s;
        execute stmt;
        deallocate prepare stmt;
        
 	set @s = concat(
        'UPDATE `',v_table,'` SET `order` = ',@v_new_order,' ', 
        'WHERE (`', @table_pk,'` = ',v_id_object,');');
        prepare stmt from @s;
        execute stmt;
        deallocate prepare stmt;
        
        
        set @s = concat(
        'UPDATE `',v_table,'` SET `order` = ',@v_order,' ', 
        'WHERE (`', @table_pk,'` = ',@v_next_id_obj,');');
        prepare stmt from @s;
        execute stmt;
        deallocate prepare stmt;
END IF;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_select"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_select` $$
CREATE PROCEDURE `cntntr_proc_select`(IN `v_table_name` VARCHAR(64), IN `v_field_name` VARCHAR(64), IN `v_output_table` VARCHAR(64))
    NO SQL
BEGIN
  declare v_type varchar(64);
    
    select `DATA_TYPE` 
     from `INFORMATION_SCHEMA`.`COLUMNS` 
     where table_schema = database() && table_name = v_table_name && column_name = v_field_name && column_comment like '%lang%'
     into v_type;
    if v_type is null then
      select `COLUMN_NAME` from  `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE` 
       where `TABLE_SCHEMA` = database() && `TABLE_NAME` = v_table_name && CONSTRAINT_NAME = 'PRIMARY'
       into @table_pk;
      set @s = concat(
        'select `',@table_pk,'` as id, `',v_field_name,'` as v ',
        'from `',v_table_name,'`');
    else
      set @s = concat(
        'select `row_id` as id, `',@current_lang,'` as v from `lng_',v_type,'` ',
        'where `table_name` = \'',v_table_name,'\' ',
        'and `field_name` = \'',v_field_name,'\';');
    end if;
    if v_output_table is not null then
      set @s = concat(
        'insert into `',v_output_table,'` ',
        @s);
    end if;
    prepare stmt from @s;
    execute stmt;
    deallocate prepare stmt;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_select_all"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_select_all` $$
CREATE PROCEDURE `cntntr_proc_select_all`(IN `v_table_name` VARCHAR(64), IN `v_output_table` VARCHAR(64))
    NO SQL
BEGIN
  declare v_type varchar(64);
  
  set @s = concat('select ',
   (select group_concat(sel_f) from
    (select concat('`',COLUMN_NAME,'`') as sel_f, ORDINAL_POSITION
     from INFORMATION_SCHEMA.COLUMNS
     where TABLE_SCHEMA = database() && TABLE_NAME = v_table_name && COLUMN_COMMENT not like '%lang%'
     union
     select concat(COLUMN_NAME,'_table.ru as `',COLUMN_NAME,'`'), ORDINAL_POSITION
     from INFORMATION_SCHEMA.COLUMNS
     where TABLE_SCHEMA = database() && TABLE_NAME = v_table_name && COLUMN_COMMENT like '%lang%'
     order by ORDINAL_POSITION)as t1),
   ' from ',v_table_name,' ',
   (select group_concat('inner join lng_',
    (select DATA_TYPE 
     from INFORMATION_SCHEMA.COLUMNS c
     where TABLE_SCHEMA = database() && TABLE_NAME = v_table_name && c.COLUMN_NAME=COLUMNS.COLUMN_NAME),
    ' as ', COLUMN_NAME,'_table on ',
    (select COLUMN_NAME 
     from  INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
     where TABLE_SCHEMA = database() && TABLE_NAME = v_table_name && CONSTRAINT_NAME = 'PRIMARY'),
    '=',COLUMN_NAME,'_table.row_id' separator ' ')
    from INFORMATION_SCHEMA.COLUMNS
    where TABLE_SCHEMA = database() && TABLE_NAME = v_table_name && COLUMN_COMMENT like '%lang%'),
   ' where ',
   (select group_concat(COLUMN_NAME,'_table.table_name=\'',v_table_name,'\' && ',
    COLUMN_NAME,'_table.field_name=\'',COLUMN_NAME,'\'' separator ' && ')
    from INFORMATION_SCHEMA.COLUMNS
    where TABLE_SCHEMA = database() && TABLE_NAME = v_table_name && COLUMN_COMMENT like '%lang%'));
    
  if v_output_table is not null then
    set @s = concat(
      'insert into `',v_output_table,'` ',
      @s);
  end if;
  prepare stmt from @s;
  execute stmt;
  deallocate prepare stmt;
END$$
DELIMITER ;
 

------------Создание процедуры "cntntr_proc_set_cell"------------

DELIMITER $$
DROP PROCEDURE IF EXISTS `cntntr_proc_set_cell` $$
CREATE PROCEDURE `cntntr_proc_set_cell`(IN `v_table_name` VARCHAR(64), IN `v_field_name` VARCHAR(64), IN `v_row_id` INT, IN `v_value` TEXT, IN `v_id_updater` INT)
BEGIN

select REPLACE(v_table_name, SUBSTRING_INDEX(v_table_name,  '_', 1 ),'id') into @table_pk;
  	
     set @s = concat(
        'update `',v_table_name,'`',
        'set `',v_field_name,'` = \'',v_value,'\' ',', update_date = NOW(), id_updater = \'',v_id_updater,'\' ',
        'where `',@table_pk,'` = ',v_row_id,';');
      prepare stmt from @s;
      execute stmt;
      deallocate prepare stmt;
 END$$
DELIMITER ;
 