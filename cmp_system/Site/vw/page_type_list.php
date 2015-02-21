<?php
	// Ссылка для обновления данного вида.
	$view_update_link = "Site/PageType";
	
?>
<div>
<?php 	if ($this->is_insert()) { ?>
	<span class="btn-link" onclick="exec('Site/InsertPageType', '<?php echo $view_update_link;?>');">
		<i class="icon-plus"></i>
	</span>			
<? } ?>

	<table  class="table table-bordered">
		<caption>Типы страниц</caption>
		<thead>
			<tr>
				<th>№</th>
				<th>Назва</th>
				<th>Тема</th>
				<th>Базовий вид</th>
				<th>URL</th>
				<th>Р</th>
				<th  colspan="3">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php $i=1;foreach ($page_type_list as $page_type) { ?>
			<tr> 
				<td >
					<?php echo "$i";$i++;?>
				</td>
				<td>
					<?php $this->Input('text', 'st_page_type', 'name', $page_type['id_page_type'], $page_type['name'], Array(), $page_type['id_owner']);?>
				</td>
				<td>
					<?php $this->Input('combobox', 'st_page_type', 'theme', $page_type['id_page_type'], $page_type['theme'], Array('valueList'=>$combo_theme_view), $page_type['id_owner']);?>
				</td>
				<td>
					<?php $this->Input('combobox', 'st_page_type', 'base_view', $page_type['id_page_type'], $page_type['base_view'], Array('valueList'=>$combo_base_view), $page_type['id_owner']);?>
				</td>
				<td>
					<?php $this->Input('text', 'st_page_type', 'url', $page_type['id_page_type'], $page_type['url'], Array(), $page_type['id_owner']);?>
				</td>
				<td>
				<?php $this->Input('checkmark', 'st_page_type', 'is_edit', $page_type['id_page_type'], $page_type['is_edit'], Array('class' => 'check-awesome'), $page_type['id_owner']);?>
				</td>
				<td style="width: 10px;">			
					<div  class="btn-link" onclick="$('#p<?php echo $page_type["id_page_type"];?>').slideToggle('fast');">
						<i class="icon-th-list"></i>
					</div>
				</td>
				<td style="width: 10px;">			
					<div class="btn-link" onclick="exec('Site/InsertPagePosition/<?php echo $page_type["id_page_type"];?>', '<?php echo $view_update_link;?>')">
						<i class="icon-plus"></i>
					</div>
				</td>
				<td style="width: 10px;">			
					<div class="btn-link" onclick="exec('Site/DeletePageType/<?php echo $page_type["id_page_type"];?>', '<?php echo $view_update_link;?>')">
						<i class="icon-remove"></i>
					</div>
				</td>
			</tr>
			<?php if (is_array($page_type['position']) and !empty($page_type['position'])) { ?>
			<tr id="p<?php echo $page_type["id_page_type"];?>" <?php if(true) { ?> style="display: none;" <?php }?>>
				<td colspan="9">
						<table class="table table-striped table-condensed">
				
					<?php $k=1; foreach( $page_type['position'] as $page_position) { ?>
									<tr>
										<td><?php echo "$k";$k++;?></td>
										<td><?php $this->Input('text', 'st_page_position', 'view', $page_position['id_page_position'], $page_position['view'], Array(), $page_position['id_owner']);?></td>
										<td><?php $this->Input('text', 'st_page_position', 'name', $page_position['id_page_position'], $page_position['name'], Array(), $page_position['id_owner']);?></td>
										<td><?php $this->Input('combobox', 'st_page_position', 'component', $page_position['id_page_position'], $page_position['component'], Array('valueList'=>$combo_component), $page_position['id_owner']);?></td>
										<td><?php $this->Input('text', 'st_page_position', 'function', $page_position['id_page_position'], $page_position['function'], Array(), $page_position['id_owner']);?></td>
										<td><?php $this->Input('text', 'st_page_position', 'p1', $page_position['id_page_position'], $page_position['p1'], Array(), $page_position['id_owner']);?></td>
										<td><?php $this->Input('text', 'st_page_position', 'p2', $page_position['id_page_position'], $page_position['p2'], Array(), $page_position['id_owner']);?></td>
										<td><?php $this->Input('text', 'st_page_position', 'p3', $page_position['id_page_position'], $page_position['p3'], Array(), $page_position['id_owner']);?></td>
										<td><?php $this->Input('text', 'st_page_position', 'p4', $page_position['id_page_position'], $page_position['p4'], Array(), $page_position['id_owner']);?></td>
										<td><?php $this->Input('text', 'st_page_position', 'p5', $page_position['id_page_position'], $page_position['p5'], Array(), $page_position['id_owner']);?></td>
										<td>			
											<div style="width: 10px;" class="btn-link" onclick="exec('Site/DeletePagePosition/<?php echo $page_position['id_page_position'];?>','<?php echo $view_update_link;?>');"><i class="icon-remove"></i></div>
										</td>
									</tr>
				<?php 	}   ?>
						</table>
					</td>
				</tr>
				<?php
					}
				} 
				?>
				</tbody>
	</table>
</div>