<?php
	$this->Tester->includeCSS('tester.css');
	$this->Tester->includeCSS('system.css');
	$this->Tester->includeJS('jquery.js');
	$this->Tester->includeJS('viewer.js');
?>
<div id="filelist_integrity_result">
	<h3>Проверка целостности структуры файлов компонента <?php echo $component;?></h3>
	<div id="<?php echo $component;?>_filelist_result"></div>
<?php 

		if ($result)
			echo "<font color='green'>FILE INTEGRITY SUCCESS: Все необходимые файлы и папки найдены!</font><br>";
		else
			echo "<font color='red'>FILE INTEGRITY FAIL: Целостность файлов нарушена!</font><br>";
			
		if ($result_additional)
			echo "<font color='green'>FILE ADDITIONAL SUCCESS: Дополнительных файлов и папок нет.</font><br>";
		else
			echo "<font color='orange'>FILE ADDITIONAL WARNING: Найдены дополнительные файлы и папки.</font><br>";
?>
	
	<button onclick = "loadContent('index.php?cmp=Tester&evt=load_filelist_result', '<?php echo $component;?>_filelist_result', 'component=<?php echo $component;?>', 0);">Просмотр результатов</button>
	<button onclick = "$('#<?php echo $component;?>_filelist_result').html('');">Скрыть результаты</button>
</div>
<hr>