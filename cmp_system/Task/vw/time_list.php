<div>
	<form action="#" id="form-period" class="period" >
		<label for="date-from"></label>
		<input class="form-control" type="date" name="date_from" id="from" value="<?php echo $date_from;?>">
		<input class="form-control" type="date" name="date_to" id="to" value="<?php echo $date_to;?>">
		<button class="btn btn-default" type="button" id="show" value="Показать за период" onclick="update('Task/TimeList',$('#form-period').serializeJSON());">
			<i class="glyphicon glyphicon-search"></i>
		</button>
	</form>
	<table class="table task-list">
		<thead>
			<tr>
				<th><?php echo $_['39'];?></th>
				<th><?php echo $_['3'];?></th>
				<th><?php echo $_['4'];?></th>
				<th><?php echo $_['42'];?></th>
				<th><?php echo $_['43'];?></th>
				<th><?php echo $_['44'];?></th>
				<th><?php echo $_['45'];?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="5"><?php echo $_['46'];?></th>
				<th><?php echo $days['full_time']; ?></th>
				<th></th>
			</tr>
		</tfoot>
		<tbody>
			<?php  
				  if (!empty($days['rows'])) foreach ($days['rows'] as $date=>$day ) { 
						$i = 1;
						if(!empty($day['rows'])) foreach ($day['rows'] as $work) { ?>
						<tr>
							<?php if ($i == 1) { ?>
								<td rowspan="<?php echo count($day['rows']);?>"><strong><?php echo $this->formatDate($date, 'date');?></strong></td>
							<?php  } ?>
							<td><?php echo $work['project_name'];?></td>
							<td><?php echo $work['task_name'];?></td>
							<td><?php $this->Input('datetime', 'trckr_tracker', 'work_start', $work['id_tracker'], $work['work_start'], Array('dateFormat'=>'time'), $task['id_owner']);?></td>
							<td><?php $this->Input('datetime', 'trckr_tracker', 'work_stop', $work['id_tracker'], $work['work_stop'], Array('dateFormat'=>'time'), $task['id_owner']);?></td>
							<td><?php echo $work['full_time'];?></td>
							<td><?php echo $work['comment'];?></td>
						</tr>
					<?php $i++; }  ?>
						<tr class="summary">
							<td colspan="5"><?php echo $_['47'];?></td>
							<td><?php echo $day['full_time'];?></td>
							<td></td>
						</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php /* $this->Pagination($days_count, $id_page); ?>
	<!-- /.paginate -->
	<form action="#" class="num">
		<label for="num"><?php echo $_['48'];?></label>
		<select class="form-control" name="task_page_count" onchange="reload('Task/SetTaskOnPage/'+$(this).val())">
			<option value="25" <?php if ($limit_count==25) echo 'selected="selected"';?>>25</option>
			<option value="50" <?php if ($limit_count==50) echo 'selected="selected"';?>>50</option>
			<option value="100" <?php if ($limit_count==100) echo 'selected="selected"';?>>100</option>
			<option value="150"<?php if ($limit_count==150) echo 'selected="selected"';?>>150</option>
			<option value="200"<?php if ($limit_count==200) echo 'selected="selected"';?>>200</option>
			<option value="all" <?php if ($limit_count==1000) echo 'selected="selected"';?>><?php echo $_['49'];?></option>
		</select>
		<!--<input class="form-control" type="text" name="num" id="num" value="50">
		<label for="num">задач на странице</label>
		<input class="btn btn-default" type="button" id="show" value="Показать">-->
	</form> <?php */ ?>
</div>