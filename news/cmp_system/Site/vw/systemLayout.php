<?php if ($this->User->getRole() != 'administrator') {
	die($this->redirect());
} ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Девеловерская <?php echo $this->title ?></title>
		<?php
		//$this->includeCSS('bootstrap3.css');
		$this->includeCSS('contenter.css');
		//$this->includeCSS('styles.css');
		$this->includeCSS('css-noise.css');
		$this->includeCSS('systemLayout.css');
		$this->includeCSS('//netdna.bootstrapcdn.com/font-awesome/4.0.1/css/font-awesome.css');
		$this->Contenter->includeCSS('typeahead.js-bootstrap.css');
		
		
		$this->loadCSS();

		$this->includeJS('upload_script.js');
		$this->includeJS('viewer.js');

		
		$this->loadJsData();
		$this->loadJS();
				
		?>
		<!--[if IE]> <style> * {font-family: "Comic Sans MS" !important;} </style> <![endif]-->
		<!--[if IE]> <style> * {font-family: "Comic Sans" !important;} </style> <![endif]-->

	</head>
    <body class=" <?php echo($this->User->getMode()=="edit")?"active-edit-mode":"active-view-mode";?>"> 
		<?php	include("inc/menu.php");?>
		
		
				
						<?php
							$this->loadPosition("position1", "systemLayout");
						?>	


	    	
	    	
    </body>
</html>
