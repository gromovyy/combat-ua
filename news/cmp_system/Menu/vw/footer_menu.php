<div>
<ul id="footer-inform">


	
<?php	foreach($footer_menu as $key=>$menu) {	?>
	<a href="<?php echo $this->getUrlEncoded($menu["url"]); ?>" id="a<?php echo $key;?>">
		<?php 
			echo ($key)?'&nbsp|&nbsp':''; 
			echo mb_strtolower($menu["menu"],"UTF-8"); ?></a>
<?php	} ?>	








	
</div>