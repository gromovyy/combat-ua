<div>
<?php 
	$view_update_link = "Gnom/Test";
	$this->loadToolBox("gnom",null,null,null,null,array('add'), $view_update_link);?>
	
		<?php foreach($gnoms as $gnom){
			echo '<div>'.$gnom['name'].'<div>';
			
		}
	?>
	</div>
	
	
	