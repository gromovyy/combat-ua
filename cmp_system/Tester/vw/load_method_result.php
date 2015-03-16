<?php
	if (!$this->checkAccess("0,3", __FILE__, __LINE__, __FUNCTION__)) return;
	
	$this->Tester->includeCSS('tester.css');
	$this->Tester->includeJS('jquery.js');
	$this->Tester->includeJS('viewer.js');
?>
<table id = "result_table">
	<tr>
		<th id = "first_col"></th>
		<th id = "second_col">Правильные результаты</th>
		<th id = "third_col">Текущие результаты</th>
	</tr>
	<tr>
		<td class = "test_type">Возвращаемое значение функции</td>
		<td class = "<?php echo $class_function_correct;?>"><div class = "div_container"><xmp><?php echo $result_function_correct;?></xmp></div></td>
		<td class = "<?php echo $class_function_current;?>"><div class = "div_container"><xmp><?php echo $result_function_current;?></xmp></div></td>
	</tr>
	<tr>
		<td class = "test_type">Вывод в браузер</td>
		<td class = "<?php echo $class_browser_correct;?>"><div class = "div_container"><xmp><?php echo $result_browser_correct;?></xmp></div></td>
		<td class = "<?php echo $class_browser_current;?>"><div class = "div_container"><xmp><?php echo $result_browser_current;?></xmp></div></td>
	</tr>
	<tr>
		<td class = "test_type">SQL запрос</td>
		<td class = "<?php echo $class_bd_correct;?>"><div class = "div_container"><xmp><?php echo $result_bd_correct;?></xmp></div></td>
		<td class = "<?php echo $class_bd_current;?>"><div class = "div_container"><xmp><?php echo $result_bd_current;?></xmp></div></td>
	</tr>
	<?php if ($this->role == TSR_ADMIN) { ?>
	<tr>
		<td class = "td_invisible"></td>
		<td class = "td_invisible"><button onclick = "submitExecution('index.php?cmp=Tester&evt=delete_method_result', 'file_name=<?php echo $file_name;?>&component=<?php echo $component;?>');loadContent('index.php?cmp=Tester&evt=load_method_result', '<?php echo $file_name;?>', 'file_name=<?php echo $file_name;?>&component=<?php echo $component;?>');">ОЧИСТИТЬ ПРАВИЛЬНЫЕ РЕЗУЛЬТАТЫ</button></td>
		<td class = "td_invisible"><button onclick = "submitExecution('index.php?cmp=Tester&evt=save_method_result', 'file_name=<?php echo $file_name;?>&component=<?php echo $component;?>');loadContent('index.php?cmp=Tester&evt=load_method_result', '<?php echo $file_name;?>', 'file_name=<?php echo $file_name;?>&component=<?php echo $component;?>', 0);">ЭТО ПРАВИЛЬНЫЙ РЕЗУЛЬТАТ</button></th>
	</tr>
	<?php }?>
</table>
