<?php
	$this->Tester->includeCSS('tester.css');
	$this->Tester->includeCSS('system.css');
	$this->Tester->includeJS('jquery.js');
	$this->Tester->includeJS('viewer.js');
?>  
	<h3>Проверка целостности структуры БД компонента <?php echo $component;?></h3>
	<div id="<?php echo $component;?>_db_check_result"></div>
<?php 
	  if ($result) 
			echo "<font color='green'>TABLE INTEGRITY SUCCESS: Структура базы данных компонента $component целостна!</font><br>";
	  else 
			echo "<font color='red'>FILE INTEGRITY FAIL: Целостность структуры базы данных компонента $component нарушена!</font><br>"; 
?>
	<button onclick = "loadContent('index.php?cmp=Tester&evt=load_db_check_result', '<?php echo $component;?>_db_check_result', 'component=<?php echo $component;?>', 0);">Просмотр результатов</button>
	<button onclick = "$('#<?php echo $component;?>_db_check_result').html('');">Скрыть результаты</button>
<hr>