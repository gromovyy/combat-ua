<?php 
	$view_update_link = "Task/ProjectList/$id_project/$is_show_finished";
?>
<div>
	<div class="add">
		<a onclick="addFormJSON('task-modal-form');"><i class="glyphicon glyphicon-plus-sign"></i><?php echo $_['0'];?></a>
		<!--<a onclick = "exec('Task/InsertProject/<?php echo $id_project;?>','<?php echo $view_update_link; ?>');" style="cursor:pointer" <?php //data-toggle="modal" data-target="#addProject" ?>><i class="glyphicon glyphicon-plus-sign"></i>Добавить задачу</a>-->
		<?php if (!$is_show_finished) { ?>
		<a onclick = "update('Task/ProjectList/<?php echo $id_project; ?>/1');" style="cursor:pointer"><?php echo $_['1'];?></a>
		<?php } else { ?>
		<a onclick = "update('Task/ProjectList/<?php echo $id_project; ?>/0');" style="cursor:pointer"><?php echo $_['2'];?></a>
		<?php } ?>
	</div>
	<?php $this->e_List($tasks, $id_page, $is_show_finished); ?>
</div>