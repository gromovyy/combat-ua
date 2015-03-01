<?php 
	$view_update_link = "Task/UserList/$id_project";
	$data['view_update_link'] = $view_update_link;
	
?>
<div>
	<div class="add"> 
		<a onclick="addFormJSON('task-modal-form');"><i class="glyphicon glyphicon-plus-sign"></i><?php echo $_['0'];?></a>
		<!--<a onclick = "exec('Task/InsertUser/<?php echo $id_user; ?>','Task/List/<?php echo $id_user; ?>/0');" style="cursor:pointer" <?php //data-toggle="modal" data-target="#addProject" ?>><i class="glyphicon glyphicon-plus-sign"></i>Добавить задачу</a> -->
		<?php if (!$is_show_closed) { ?>
		<a onclick = "reload('Task/SetShowClosed/1');" style="cursor:pointer"><?php echo $_['1'];?></a>
		<?php } else { ?>
		<a onclick = "reload('Task/SetShowClosed/0');" style="cursor:pointer"><?php echo $_['2'];?></a>
		<?php } ?>
	</div>
	<?php $this->e_List($tasks, $id_page); ?>
	<script>//setInterval(function() {}, 1000);</script>
</div>