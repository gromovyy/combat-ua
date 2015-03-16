<?php
	$this->Tester->includeCSS('tester.css');
	$this->Tester->includeCSS('system.css');
	$this->Tester->includeJS('jquery.js');
	$this->Tester->includeJS('viewer.js');
?>
<h3>Тестирование метода <?php echo $component."->".$method;?>:</h3>
<?php foreach ($check_method_result as $test_num => $test) {   ?>
<div id = "<?php echo $test["file_name"];?>"></div>
<div class = "<?php echo $test["method_result"];?>">№ <?php echo $test["test_number"];?> Параметры: <xmp><?php echo  substr($test["params"],0,200);?></xmp> Результат: <?php echo $test["method_result_description"];?>
	<div class = "buttons">
		<button onclick = "loadContent('index.php?cmp=Tester&evt=load_method_result', '<?php echo $test["file_name"];?>', 'file_name=<?php echo $test["file_name"];?>&component=<?php echo $component;?>', 0);">Просмотр результатов</button>
		<button onclick = "$('#<?php echo $test["file_name"];?>').html('');">Скрыть результаты</button>
	</div>
</div>
<?php } ?>
<hr>
