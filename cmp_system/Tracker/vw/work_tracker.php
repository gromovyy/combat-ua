<div class="time-tracker">
	<?php if ($work_status == 'working') { ?>
		<div class="working" onclick="exec('Tracker/Stop','Tracker/WorkTracker');"></div>
	<?php } else { ?>
		<div class="offline" onclick="exec('Tracker/Start','Tracker/WorkTracker');"></div>
	<?php } ?>
		
		<div class="work-time">
			<?php foreach($periods as  $period) { ?>
				<div><?php echo $period['work_start'];?> - <?php echo $period['work_end'];?></div>
			<?php } ?>
				<div>Всего за сегодня: <?php echo $full_time; ?></div>
		</div>
		<div class="work-log">
			<?php foreach($works as  $work) { ?>
				<div><a href = "project/<?php echo $work['id_project'];?>"><?php echo $work['id_project'];?></a> : <?php echo $work['log'];?></div>
			<?php } ?>
				<div>Всего работ за сегодня: <?php echo $work_count; ?></div>
		</div>
		<div class="open"></div>
</div>
