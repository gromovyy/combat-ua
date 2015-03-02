<div>
<?php 
  //$this->Menu->e_ShowMenu(); ?>
<?php
	$view_update_link = "bear/Test";
	$this->loadToolBox("bear",null,null,null,null,array('add'), $view_update_link); ?>

	<?php foreach($bears as $bear){ ?>
	      <div>
		 <?php
	     $this->Input('textarea', 'br_bear', 'name', $bear['id_bear'], $bear['name'], Array(),$bear["id_owner"] ); //поле для введення задачі
	     $this->Input('checkmark', 'br_bear', 'is_visible', $bear['id_bear'], $bear['is_visible'], Array(),$bear["id_owner"] ); //галочки
		 $this->loadToolBox("bear",$bear['id_bear'],null,null,null,array('delete'), $view_update_link);
		 ?>
		 </div>
		 <?php
	}
	?>
 
</div>