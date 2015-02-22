<div>
<?php 
  //$this->Menu->e_ShowMenu(); ?>
<?php
	$view_update_link = "Gnom/Test";
	$this->loadToolBox("gnom",null,null,null,null,array('add'), $view_update_link); ?>

	<?php foreach($gnoms as $gnom){ ?>
	      <div>
		 <?php
	     $this->Input('textarea', 'gnm_gnom', 'name', $gnom['id_gnom'], $gnom['name'], Array(),$gnom["id_owner"] );
	     $this->Input('checkmark', 'gnm_gnom', 'is_visible', $gnom['id_gnom'], $gnom['is_visible'], Array(),$gnom["id_owner"] );
		 $this->loadToolBox("gnom",$gnom['id_gnom'],null,null,null,array('delete'), $view_update_link);
		 ?>
		 </div>
		 <?php
	}
	?>
 
</div>