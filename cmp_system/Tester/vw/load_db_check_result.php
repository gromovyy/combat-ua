<?php
	if (!$this->checkAccess(array(TSR_READER,TSR_ADMIN), __FILE__, __LINE__, __FUNCTION__)) return;
	
	$this->Tester->includeCSS('tester.css');
	$this->Tester->includeCSS('system.css');
	$this->Tester->includeJS('jquery.js');
	$this->Tester->includeJS('viewer.js');
?>
<table id = "result_table">
	<tr>
		<th id = "first_col"></th>
		<th id = "second_col">Структура БД из файла</th>
		<th id = "third_col">Структура БД в СУБД</th>
	</tr>
	<tr>
		<td class = "test_type">Таблицы</td>
		<td class = "<?php echo $class_file_tables;?>"><?php echo $file_tables;?></td>
		<td class = "<?php echo $class_db_tables;?>"><?php echo $db_tables;?></th>
	</tr>
	<tr>
		<td class = "test_type">Виды</td>
		<td class = "<?php echo $class_file_views;?>"><?php echo $file_views;?></td>
		<td class = "<?php echo $class_db_views;?>"><?php echo $db_views;?></th>
	</tr>
	<tr>
		<td class = "test_type">Процедуры</td>
		<td class = "<?php echo $class_file_procedures;?>"><?php echo $file_procedures;?></td>
		<td class = "<?php echo $class_db_procedures;?>"><?php echo $db_procedures;?></th>
	</tr>
	<?php if ($this->role == TSR_ADMIN) { ?>
	<tr>
		<td class = "td_invisible"></td>
		<td class = "td_invisible"></td>
		<td class = "td_invisible"><button onclick = "submitExecution('index.php?cmp=Tester&evt=update_db_structure', 'component=<?php echo $component;?>',0,1);loadContent('index.php?cmp=Tester&evt=load_db_check_result', '<?php echo $component;?>_db_check_result', 'component=<?php echo $component;?>', 0);">ОБНОВИТЬ СТРУКТУРУ БД</button></th>
	</tr>
	<?php }?>
</table>
