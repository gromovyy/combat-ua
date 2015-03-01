<table class="table">
	<thead>
		<tr>
		<?php foreach ($keys as $key): ?>
			<th><?php echo $key; ?></th>
		<?php endforeach ?>
			<th>
				<?php $this->loadToolBox($object,null,null,null,null,array('add'),$this->component.'/List/'.$object) ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($list as $value): ?>
			<tr>

					<?php foreach ($keys as $key): ?>
						<td data-title="<?php echo "$key";?>"><?php $this->Input('text',$table,$key,$value["id_$object"],$value[$key],array(),$value['id_owner']); ?></td>
					<?php endforeach ?>
				<td data-title="cms tools"><?php $this->loadToolBox($object,$value["id_$object"],null,null,null,array('delete'),$this->component.'/List/'.$object) ?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
