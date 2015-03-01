<?php 
	// Формируем массивы компонентов и ролей
	//print_r($rules);
	$components = array_keys($rules);
	if (empty($components)) {
		echo "Правил доступа ни для одного компонента не задано";
		return;
	}
	$this->includeCSS('Rule.css');
	$roles = array_keys($rules[$components[0]]);
?>
<table class="table table-bordered">
	<caption>Права доступа</caption>
	<thead>
		<tr>
			<th>№</th>
			<th>Компоненти</th>
			<?php 	$i=1;
			foreach ($roles as $role) {?>
			<th><?php echo $role;?></th>
			<?php } ?>
		<tr>
	</thead>
	<tbody>

				<?php foreach ($components as $component) { ?>
				<tr>
					<td class="tr-first"><?php echo "$i";$i++;?></td>
					<td><?php echo $component?></td>
					<?php 	foreach ($roles as $role) { ?>
						
					<td><?php 
						$this->Input('select-checkmark',     'rl_rule', 'select',     $rules[$component][$role]['id_rule'], $rules[$component][$role]["select"]    );	
						$this->Input('insert-checkmark',     'rl_rule', 'insert',     $rules[$component][$role]['id_rule'], $rules[$component][$role]["insert"]    );
						$this->Input('update-checkmark',     'rl_rule', 'update',     $rules[$component][$role]['id_rule'], $rules[$component][$role]["update"]    );
						$this->Input('delete-checkmark',     'rl_rule', 'delete',     $rules[$component][$role]['id_rule'], $rules[$component][$role]["delete"]    );
						$this->Input('visibility-checkmark', 'rl_rule', 'visibility', $rules[$component][$role]['id_rule'], $rules[$component][$role]["visibility"]);	
						?></td>
					<?php }?>
				</tr>
				<?php }?>
	</tbody>
</table>