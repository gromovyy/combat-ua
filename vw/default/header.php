<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>IT-University - <?php echo $this->title ?></title>
		<?php
		//$this->includeCSS('bootstrap.css');
		//$this->includeCSS('bootstrap-responsive.css');
		$this->includeCSS('bootstrap-combined.no-icons.min.css');
		$this->includeCSS('styles.css');
		//$this->includeCSS('fancybox.css');
		$this->includeCSS('contenter.css');


		

		$this->loadCSS();
		$this->includeJS('bootstrap.min.js');
		$this->includeJS('upload_script.js');
		
		$this->loadJsData();
		$this->loadJS();
	
		?>


<script>

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-41991110-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

	</head>
    <body class="<?php echo($this->User->getMode()=="edit")?"active-edit-mode":"active-view-mode";?>">
		<?php
					$this->loadPosition("position_menu", "header");
					//$this->Menu->e_ShowMenu();
		?>

