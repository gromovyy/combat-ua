<?php
	// Ссылка для обновления данного вида.
	$view_update_link = "Role/List";
?>
<table class="table table-bordered">
	<caption>Роли пользователей
					<?php 	if ($this->is_insert()) { ?>
						<span class="btn-link" onclick="exec('Role/Insert_role', '<?php echo $view_update_link;?>')">
							<i class="icon-plus"></i>
						</span>			
					<? } ?>
	</caption>
	<thead>
		<tr>
			<th>№</th>
			<th>Назва</th>
			<th></th>
		<tr>
	</thead>
	<tbody>
	<?php 	$i=1; foreach ($roles as $row) { ?>
		<tr>
			<td><?php echo "$i";$i++;?></td>
			<td><?php $this->Input('text', 'rl_role', 'role', $row["id_role"], $row["role"]);?></td>
			<td style="width:10px;">
				<div class="btn-link" onclick="exec('Role/Delete_role/<?php echo $row["id_role"];?>','<?php echo $view_update_link;?>')"><i class="icon-remove"></i></div>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>