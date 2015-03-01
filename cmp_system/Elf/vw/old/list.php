<div>
<table class="table">
		<thead>
			<tr>
				<th class="sortable" onclick="reload('Task/SetTaskOrder/project_name')"><div  class='project-column'><?php echo $_['3'];?><?php 
					if ($order_field =='project_name') echo '<i class="glyphicon glyphicon-arrow-'.$order_direction.'"></i>';?>
				</div></th>
				<th class="sortable" onclick="reload('Task/SetTaskOrder/name')"><div  class='task-column'><?php echo $_['4'];?><?php 
					if ($order_field =='name') echo '<i class="glyphicon glyphicon-arrow-'.$order_direction.'"></i>';?></div></th>
				<th><i class="glyphicon glyphicon-paperclip"></i></th>
				<th class="sortable" onclick="reload('Task/SetTaskOrder/priority')"><?php echo $_['5'];?><?php 
					if ($order_field =='priority') echo '<i class="glyphicon glyphicon-arrow-'.$order_direction.'"></i>';?></th>
				<th class="sortable" onclick="reload('Task/SetTaskOrder/start_date')"><div  class='date-column'><?php echo $_['6'];?><?php 
					if ($order_field =='start_date') echo '<i class="glyphicon glyphicon-arrow-'.$order_direction.'"></i>';?></div></th>
				<th class="sortable" onclick="reload('Task/SetTaskOrder/finish_date')"><div  class='date-column'><?php echo $_['7'];?><?php 
					if ($order_field =='finish_date') echo '<i class="glyphicon glyphicon-arrow-'.$order_direction.'"></i>';?></div></th>
				<?php if ($is_show_closed) { ?><th class="sortable" onclick="reload('Task/SetTaskOrder/close_date')"><div  class='date-column'><?php echo $_['8'];?><?php 
					if ($order_field =='close_date') echo '<i class="glyphicon glyphicon-arrow-'.$order_direction.'"></i>';?></div></th><?php } ?>
				<th><?php echo $_['9'];?></th>
				<?php if (!$is_show_closed) { ?><th><div class='button-column'><i class="glyphicon glyphicon-play"></i></div></th><?php } ?>
				<th class="sortable" onclick="reload('Task/SetTaskOrder/worker_name')"><?php echo $_['10'];?><?php 
					if ($order_field =='worker_name') echo '<i class="glyphicon glyphicon-arrow-'.$order_direction.'"></i>';?></th>
				<th class="sortable" onclick="reload('Task/SetTaskOrder/controller_name')"><?php echo $_['11'];?><?php 
					if ($order_field =='controller_name') echo '<i class="glyphicon glyphicon-arrow-'.$order_direction.'"></i>';?></th>
				<th><i class="glyphicon glyphicon-ok"></i></th>
				<?php if (!$is_show_closed) { ?>
					<th><i class="glyphicon glyphicon-edit"></i></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php /*print_r($tasks_in_work);*/ if (!empty($tasks)) foreach ($tasks as $task ) { ?>
			<tr class="task <?php echo $task['state']; echo ($task['is_new'])? " new " : ""; echo (empty($tasks_in_work[$task['id_task']]))?"":" work-".$tasks_in_work[$task['id_task']]['state']; echo (empty($task['full_time']))?"":" started"; ?>" data-id="<?php echo $task['id_task'];?>">
				<td><?php echo $task['project_name']; ?><?php // $this->Input('combobox', 'tsk_task', 'id_project', $task['id_task'], $task['id_project'], Array('valueList'=>$combo_projects), $task['id_owner']);?></td>
				<td><?php $this->Input('text', 'tsk_task', 'name', $task['id_task'], $task['name'], Array(), $task['id_owner']);?></td>
				<td>
					<?php if (!empty($task['id_attachment'])) { ?>
					<a class = "attachment_link" href="Attachment/ZipDownload/<?php echo $task['id_task'];?>/Task">
						<i class="glyphicon glyphicon-paperclip"></i>
					</a>
					<?php } ?>
				</td>
				<td><?php $this->Input('text', 'tsk_task', 'priority', $task['id_task'], $task['priority'], Array(), $task['id_owner']);?></td>
				<td><?php //echo $this->formatDate($task['start_date']) ; 
						$this->Input('datetime', 'tsk_task', 'start_date', $task['id_task'], $task['start_date'], Array('dateFormat'=>'datetime'), $task['id_owner']);?></td>
				<td <?php 
						if(!$is_show_closed) 
							echo (($task['finish_date']!='0000-00-00') && (strtotime($task['finish_date']) < time()) && $task['state'] == 'open')?' class="overtime"':''; 
						else 
							echo ((strtotime($task['finish_date']) < strtotime($task['close_date'])) && $task['state'] == 'closed')?' class="overtime"':'';
				?>><?php //echo $this->formatDate($task['finish_date']);// 
						$this->Input('datetime', 'tsk_task', 'finish_date', $task['id_task'], $task['finish_date'], Array('dateFormat'=>'datetime'), $task['id_owner']);?></td>
				<?php if ($is_show_closed) { ?><td><?php echo $this->formatDate($task['close_date']) ;?></td><?php } ?>
				<td <?php if ($tasks_in_work[$task['id_task']]['state'] == 'open') { echo 'id="timer-'.uniqid().'" data-time="'.$task['full_time_second'].'"'; }?>><?php echo $task['full_time']?></td>				
				<?php if (!$is_show_closed) { ?>
				<td>
						<?php if($this->User->getId()== $task['id_worker'] ) {
								if(empty($tasks_in_work[$task['id_task']]) and !$is_user_in_work) { ?>
								<a onclick="reload('Tracker/StartWork/<?php echo $task['id_task'];?>')"><i class="glyphicon glyphicon-play"></i></a>
						<?php } else 
									if($is_user_in_work and ($tasks_in_work[$task['id_task']]['state'] == 'open')) { ?>
								<a onclick="$('#id_tracker').val(<?php echo $tasks_in_work[$task['id_task']]['id_tracker'];?>)"><i class="glyphicon glyphicon-stop" data-toggle="modal" data-target="#myModal"></i></a>
								<a onclick="reload('Tracker/PauseWork/<?php echo $tasks_in_work[$task['id_task']]['id_tracker'];?>');"><i class="glyphicon glyphicon-pause"></i></a>
						<?php } else
									if($tasks_in_work[$task['id_task']]['state'] == 'paused' and !$is_user_in_work){ ?>
									<a onclick="reload('Tracker/StartWork/<?php echo $task['id_task'];?>')"><i class="glyphicon glyphicon-play"></i></a>
										
						<?php }	} ?>
				</td>
				<?php } ?>
				<td><?php echo $task['worker_name']; ?><?php // $this->Input('combobox', 'tsk_task', 'id_worker', $task['id_task'], $task['id_worker'], Array('valueList'=>$combo_members), $task['id_owner']);?></td>
				<td><?php echo $task['controller_name']; ?><?php // $this->Input('combobox', 'tsk_task', 'id_owner', $task['id_task'], $task['id_owner'], Array('valueList'=>$combo_members), $task['id_owner']);?></td>
				<td class="submit-work"><?php // file_put_contents('test.txt', print_r($task, true), FILE_APPEND);
					if (empty($tasks_in_work[$task['id_task']])) 
						$this->e_StateSwitcher($task['id_task'], $task['state']);
						//$this->Input('checkmark', 'tsk_task', 'state', $task['id_task'], $task['state'], Array('valueList'=>array('closed','open'),'mode'=>'edit'), $task['id_owner']); }
					?>
				</td>
				<!--<a><i class="glyphicon glyphicon-ok"></i></a> -->
				<!--<td> 
				</td> -->
				<?php if (!$is_show_closed) { ?>
					<td>
					<?php if ($this->User->getId()== $task['id_owner'] or $this->User->getRole() == 'administrator') { ?>
					<a><i class="glyphicon glyphicon-pencil" onclick="loadFormJSON('Task/getTask/<?php echo $task['id_task'];?>','task-modal-form');"></i></a>
					<?php } ?>
					<?php $this->loadToolBox("task",$task["id_task"],null,null,null,array('delete'), $view_update_link) ?>
					</td>
				<?php } ?>
			</tr>
			<tr class="description">
				<td colspan="12">
					<div data-id='description-<?php echo $task['id_task'];?>'>
						<div class = "row">
							<div class="col-xs-2"><?php echo $_['12'];?></div>
							<div class="col-xs-10"> <?php echo (empty($task['description']))?"&nbsp;": $task['description'];?></div>
						</div>
						<?php if (is_array($task['works']))
								foreach($task['works'] as $work) { 
								if (empty($work['comment'])) continue;?>
						<div class = "row">
							<div class="col-xs-2"><?php echo $this->formatDate($work['work_stop']); ?> :</div>
							
							<?php if (!empty($work['id_attachment'])) { ?>
							<div class="col-xs-9"><?php echo $work['comment']; ?></div>
							<div class="col-xs-1 right">
								<a class = "attachment_link" href="Attachment/ZipDownload/<?php echo $work['id_tracker'];?>/Tracker">
									<i class="glyphicon glyphicon-paperclip"></i>
								</a>
							</div>
							<?php } else { ?>
							<div class="col-xs-10"><?php echo $work['comment']; ?></div>
							<?php } ?>
						</div>
						<?php } ?>
					</div>
				</td>
				<?php// $this->Input('textarea', 'tsk_task', 'description', $task['id_task'], $task['description'], Array(), $task['id_owner']);?>
			<?php } ?>
		</tbody>
	</table>
	<?php $this->Pagination($tasks_count, $id_page); ?>
	<!-- /.paginate -->
	<form action="#" class="num">
		<label for="num"><?php echo $_['13'];?></label>
		<select class="form-control" name="task_page_count" onchange="reload('Task/SetTaskOnPage/'+$(this).val())">
			<option value="25" <?php if ($limit_count==25) echo 'selected="selected"';?>>25</option>
			<option value="50" <?php if ($limit_count==50) echo 'selected="selected"';?>>50</option>
			<option value="100" <?php if ($limit_count==100) echo 'selected="selected"';?>>100</option>
			<option value="150"<?php if ($limit_count==150) echo 'selected="selected"';?>>150</option>
			<option value="200"<?php if ($limit_count==200) echo 'selected="selected"';?>>200</option>
			<option value="all" <?php if ($limit_count==1000) echo 'selected="selected"';?>><?php echo $_['14'];?></option>
		</select>
		<!--<input class="form-control" type="text" name="num" id="num" value="50">
		<label for="num">задач на странице</label>
		<input class="btn btn-default" type="button" id="show" value="Показать">-->
	</form>
</div>
