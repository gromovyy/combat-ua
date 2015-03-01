<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Девеловерская <?php echo $this->title ?></title>
		<?php

		
		
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
		
				
						<?php
							$this->loadPosition("position1", "workLayout");
						?>	


	    	
	    	
    </body>
</html>
