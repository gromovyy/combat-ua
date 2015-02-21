<div>
<div class="modal fade" id="task-modal-form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span>
						<span class="sr-only"><?php echo $_['21'];?></span>
					</button>
					<h4 class="modal-title" id="task-modal-label"><?php echo $_['22'];?></h4>
				</div>
				<div class="modal-body">
					<form action="Task/EditForm" method="POST" class="row new-issue" id="task-form" enctype = "multipart/form-data">
						<input type="hidden" id="id-task" name="id_task" <?php if (!empty($task['id_task'])) echo 'value="'.$task['id_task'].'"' ?>>
						<div class="col-xs-6">
							<label for="issue-name"><?php echo $_['23'];?></label>
							<input class="form-control" type="text" name="name" id="issue-name">
							<label for="desc"><?php echo $_['24'];?></label>
							<textarea name="description" id="desc" cols="30" rows="8" class="form-control"></textarea>
							
							<div class="files">
								<div class="list-group">
								</div>
								<a onclick="addFile(this)"><i class="glyphicon glyphicon-plus-sign"></i><?php echo $_['25'];?></a>
							</div>
						</div>
						<div class="col-xs-6 form-horizontal details">
						<div class="form-group">
							<label for="status" class="col-sm-4 control-label"><?php echo $_['19'];?></label>
							<div class="col-sm-8">
								<select name="state" id="status"  class="form-control">
									<option value="open"><?php echo $_['27'];?></option>
									<option value="control"><?php echo $_['28'];?></option>
									<option value="closed"><?php echo $_['8'];?></option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="proj"><?php echo $_['3'];?></label>
							<div class="col-sm-8">
								<select class="form-control" name="id_project" id="id_project">
									<option value="">—</option>
									<?php foreach($combo_projects as $row) { ?>
									<option value="<?php echo $row['id'];?>" <?php if ($task['id_project'] == $row['id']) {?> selected="selected" <?php } ?>><?php echo $row['v'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="priority" class="col-sm-4 control-label"><?php echo $_['31'];?></label>
							<div class="col-sm-8">
								<input name="priority" id="priority" type="text" class="form-control" value="10">
								<!--<p>Приоритет задачи (выше приоритет — выше в списке).</p> -->
							</div>
						</div>
						<div class="form-group">
							<label for="from-date" class="col-sm-4 control-label"><?php echo $_['32'];?></label>
							<div class="col-sm-8">
								<input name="start_date" id="date-from" type="datetime" class="form-control" value="<?php echo $task['start_date'];?>" disabled>
								<!--<p>Дата вида ГГГГ/ММ/ДД, Г — год, М — месяц, Д — день</p> -->
							</div>
						</div>
						<div class="form-group">
							<label for="to-date" class="col-sm-4 control-label"><?php echo $_['33'];?></label>
							<div class="col-sm-8">
								<input name="finish_date" id="date-to" type="datetime" class="form-control" value="<?php  echo $task['finish_date']; ?>">
								<!--<p>Дата вида ГГГГ/ММ/ДД, Г — год, М — месяц, Д — день</p>-->
							</div>
						</div>
						<div class="form-group">
							<label for="asignee" class="col-sm-4 control-label"><?php echo $_['10'];?></label>
							<div class="col-sm-8">
								<select name="id_worker" id="asignee"  class="form-control">
									<option value="0">—</option>
									<?php foreach($combo_members as $row) { ?>
									<option value="<?php echo $row['id'];?>"><?php echo $row['v'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="controller" class="col-sm-4 control-label"><?php echo $_['11'];?></label>
							<div class="col-sm-8">
								<select name="id_owner" id="controller"  class="form-control">
									<option value="0">—</option>
									<?php foreach($combo_members as $row) { ?>
									<option value="<?php echo $row['id'];?>" <?php if ($task['id_owner'] == $row['id']) {?> selected="selected" <?php } ?>><?php echo $row['v'];?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $_['36'];?></button>
					<button type="button" class="btn btn-primary" onclick="$('#task-form').submit()" ><?php echo $_['37'];?></button>
				</div>
			</div>
		</div>
	</div><!-- /#addProject -->
</div>
