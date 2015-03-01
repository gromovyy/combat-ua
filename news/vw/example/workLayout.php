<!DOCTYPE html>
<html >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $this->title ?></title>
		
		<?php
		// Подключаем файл vw/example/styles.css
		$this->includeCSS('styles.css');
		// Выводит <link rel="stylesheet" type="text/css" href="vw/example/css/styles.css">
		$this->loadCSS();

		$this->includeJS('main.js');
		// <script type="text/javascript" src="vw/example/js/main.js"></script>
		$this->loadJsData();
		$this->loadJS();
				
		?>

	</head>
	<body class=" <?php echo($this->User->getMode()=="edit")?"active-edit-mode":"active-view-mode";?>"> 
		<?php	
			// Позиция для контента. Контент добавляется в панели управления.
			$this->loadPosition("position1", "workLayout");	
		?>	  	
	</body>
</html>
