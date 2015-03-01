<div data-id='description-<?php echo $task['id_task'];?>' style="display:block">
	<div class = "row">
		<div class="col-xs-2"><?php echo $_['38'];?></div>
		<div class="col-xs-10"><?php echo $task['description'];?></div>
	</div>
	<?php foreach($task['works'] as $work) { 
			if (empty($work['comment'])) continue;?>
	<div class = "row">
		<div class="col-xs-2"><?php echo $this->formatDate($work['work_stop']); ?> :</div>
		<div class="col-xs-9"><?php echo $work['comment']; ?></div>
		<div class="col-xs-1 right">
			<?php if (!empty($work['id_attachment'])) { ?>
				<a class="attachment_link" href="Attachment/Download/<?php echo $work['id_attachment']; ?>">
				<i class="glyphicon glyphicon-paperclip"></i>
				</a>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
</div>