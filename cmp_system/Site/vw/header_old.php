<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>Tracker : IT-Factory</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="robots" content="noindex, nofollow"/>
		<link rel="shortcut icon" href="favicon.ico">
		<?php
		$this->includeCSS("bootstrap.min.css");
		$this->includeCSS("bootstrap-theme.min.css");
		$this->includeCSS("bootstrapValidator.min.css");
		$this->includeCSS("style.css");
		$this->includeCSS("font-awesome.min.css");
		
		$this->includeJS("bootstrap.js");
		$this->includeJS("bootstrapValidator.min.js");
		$this->includeJS("ru_RU.js");
		$this->includeJS("jquery.loadJSON.js");
		$this->includeJS("jquery-ui-timepicker-addon.js");
		$this->includeJS("jquery.cookie.js");
		$this->includeJS('//cdnjs.cloudflare.com/ajax/libs/ckeditor/4.2/ckeditor.js');
		$this->loadCSS();
		$this->loadJS();
		$this->loadJsData();
		?>
    </head>
    <body class="<?php echo($this->User->getMode()=="edit")?"active-edit-mode":"active-view-mode";?>"> 
		<?php // echo "Роль=".$this->User->getRole();?>
    	<div class="background-shy-image"></div>
		<?php
					$this->loadPosition("menu", "workLayout");
					//$this->loadPosition("authorization", "workLayout");
    	?>
