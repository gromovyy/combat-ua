<div>
<?php 
  //$this->Menu->e_ShowMenu(); ?>
<?php
	$view_update_link = "Elf/Test";
	$this->loadToolBox("elf",null,null,null,null,array('add'), $view_update_link); ?>

	<?php foreach($elfs as $elf){ ?>
	      <div>
		 <?php
	     $this->Input('text', 'elf_elf', 'name', $elf['id_elf'], $elf['name'], Array(),$elf["id_owner"] );
	     $this->Input('checkmark', 'elf_elf', 'is_visible', $elf['id_elf'], $elf['is_visible'], Array(),$elf["id_owner"] );
		 $this->Input('textarea', 'elf_elf', 'description', $elf['id_elf'], $elf['name'], Array(),$elf["id_owner"]);
		 $this->Input('combobox', 'elf_elf', 'color', $elf['id_elf'], $elf['color'], Array(),$elf["id_owner"]);
		 $this->Input('date', 'elf_elf', 'data', $elf['id_elf'], $elf['data'], Array(),$elf["id_owner"]);
		 $this->loadToolBox("elf",$elf['id_elf'],null,null,null,array('delete'), $view_update_link);
		 ?>
		 </div>
		 <?php
	}
	?>
 
</div>