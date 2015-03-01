<?php 
	$view_update_link = "Task/FullList/$id_project";
?>
<div>
	<div class="add">
		<a onclick="addFormJSON('task-modal-form');"><i class="glyphicon glyphicon-plus-sign"></i><?php echo $_['0'];?></a>
		<!--<a onclick = "exec('Task/Insert','<?php echo $view_update_link; ?>');" style="cursor:pointer" <?php //data-toggle="modal" data-target="#addProject" ?>><i class="glyphicon glyphicon-plus-sign"></i>Добавить задачу</a> -->
		<?php if (!$is_show_closed) { ?>
		<a onclick = "reload('Task/SetShowClosed/1');" style="cursor:pointer"><?php echo $_['1'];?></a>
		<?php } else { ?>
		<a onclick = "reload('Task/SetShowClosed/0');" style="cursor:pointer"><?php echo $_['2'];?></a>
		<?php } ?>
	</div>
	<?php $this->e_List($tasks, $id_page); ?>
</div>