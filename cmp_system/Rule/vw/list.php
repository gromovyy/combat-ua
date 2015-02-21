<table class="table table-bordered">
	<caption>Права доступа к видам</caption>
	<thead>
		<tr>
					<th>№</th>
					<th>Види</th>
					<?php 	
							foreach ($rules as $component) {break;};
							foreach ($component as $view) {break;};
							foreach ($view as $role => $roles) {?>
					<th><?php 	echo $role;?></th>
					<?php }
						$colspan=count($view)+2;?>
		<tr>
	</thead>
	<tbody>
				<?php 	foreach ($rules as $component => $component1) {
							if(isset($component)) {?>
					<tr>
						<td class="line-table2" colspan="<?php echo $colspan;?>"><?php echo $component; $i=1;$comp=$component;?></td>
					</tr>
				<?php } foreach ($component1 as $view => $view1) { ?>
				<tr>
					<td class="tr-first"><?php echo "$i";$i++;?></td>
					<td><?php echo $view?></td>
					<?php 	
							foreach ($rules as $component) {break;};
							foreach ($component as $view) {break;};
							foreach ($view as $role => $roles) {
								foreach ($view1 as $row) {
								if($role==$row["role"]){?>
					<td><?php $this->Input('checkmark', 'rl_view_rule', 'access', $row['id_view_rule'], $row['access'],array('class' => 'check-awesome'),$row['id_owner']);break;?></td>
					<?php }}}?>
				</tr>
				<?php }}?>
	</tbody>
</table>


<div>
<span class="header-table1"></span>
	<div class="clear"></div>	
		<table class="table1">
				<tr class="table-header">
					
				</tr>

		</table>
<br/><br/>
</div>