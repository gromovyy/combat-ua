<div>
	<form action="#" class="period" id="form-project-period">
										<label for="date-from"></label>
										<input class="form-control" type="date" name="date_from" id="from" value="<?php echo $date_from;?>">
										<input class="form-control" type="date" name="date_to" id="to" value="<?php echo $date_to;?>">
										<button class="btn btn-default" type="button" id="show" value="Показать за период" onclick="update('Task/TimeList',$('#form-project-period').serializeJSON());">
											<i class="glyphicon glyphicon-search"></i>
										</button>
										<button class="btn btn-default" type="button" id="download" value="Показать за период">
											<i class="glyphicon glyphicon-download-alt"></i>
										</button>
									</form>

									<table class="table">
										<thead>
											<tr>
												<th><?php echo $_['3'];?></th>
												<th><?php echo $_['16'];?></th>
												<th><?php echo $_['4'];?></th>
												<th><?php echo $_['18'];?></th>
												<th><?php echo $_['19'];?></th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th><?php echo $_['20'];?></th>
												<th><?php echo $projects['full_time']; ?></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
										</tfoot>
										<tbody>
											<?php foreach ($projects['rows'] as $project ) { 
													if (!is_array($project)) continue; 
											?>
											<tr>
												<th colspan="5"><?php echo $project['name'];?> </th>
											</tr>
											<?php  foreach ($project as $id_task => $task ) { 
													if (!is_array($task)) continue; 
													?>
											<tr>
												<td></td>
												<td></td>
												<td><?php echo $task['name'];?></td>
												<td><?php echo $task['full_time'];//$task_time[$id_task];?></td>
												<td class="wip"><?php echo ($task['state']=="open")?"в работе":"завершена";?></td>
											</tr>
											<?php } // End task ?>
																					<tr class="foot">
												<th></th>
												<th><?php echo $project['full_time'];?></th>
												<th></th>
												<th></th>
												<th></th>
											</tr>
										<?php } // End project ?>
										</tbody>
									</table>
</div>