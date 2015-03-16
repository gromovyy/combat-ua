<?php
	if (!$this->checkAccess(array(TSR_READER,TSR_ADMIN), __FILE__, __LINE__, __METHOD__)) return;
	
	$this->Tester->includeCSS('tester.css');
	$this->Tester->includeCSS('system.css');
	$this->Tester->includeJS('jquery.js');
	$this->Tester->includeJS('viewer.js');
?>
<div id="filecheck_description"><?php echo $description;?></div>
<table id = "result_table">
	<tr>
		<th id = "first_col"></th>
		<th id = "second_col">Ожидаемая структура файлов</th>
		<th id = "third_col">Реальная структура файлов на диске</th>
	</tr>
	<tr>
		<td class = "test_type">Дерево файлов</td>
		<td class = "<?php echo $class_correct_filelist;?>"><pre><?php echo $correct_filelist;?></pre></td>
		<td class = "<?php echo $class_current_filelist;?>"><pre><?php echo $current_filelist;?></pre></th>
	</tr>
	<?php if ($this->role == TSR_ADMIN) { ?>
	<tr>
		<td class = "td_invisible"></td>
		<td class = "td_invisible"><button onclick = "submitExecution('index.php?cmp=Tester&evt=delete_filelist_result', 'component=<?php echo $component;?>',0,1);loadContent('index.php?cmp=Tester&evt=load_filelist_result', '<?php echo $component;?>_filelist_result', 'component=<?php echo $component;?>', 0);">ОЧИСТИТЬ ПРАВИЛЬНЫЕ РЕЗУЛЬТАТЫ</button></td>
		<td class = "td_invisible"><button onclick = "submitExecution('index.php?cmp=Tester&evt=save_filelist_result', 'component=<?php echo $component;?>');loadContent('index.php?cmp=Tester&evt=load_filelist_result', '<?php echo $component;?>_filelist_result', 'component=<?php echo $component;?>', 0);">ЭТО ПРАВИЛЬНАЯ СТРУКТУРА ФАЙЛОВ</button></th>
	</tr>
	<?php }?>
</table>

