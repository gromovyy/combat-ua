<table class="table table-bordered">
	<caption>События</caption>
	<thead>
		<tr>
			<th>IP</th>
			<th>cmp[ver]</th>
			<th>site</th>
			<th>message</th>
			<th>notes</th>
			<th>url_from</th>
			<th>url_to</th>
			<th>Время</th>
			<th>del</th>
		</tr>
	</thead>
	<tbody>
	<?php if(!empty($events))  foreach($events as $event){?>
		<tr class="<?php echo 'ev'.$types[$event['type']];?>">
			<td><?php echo $event['IP'];?></td>
			<td><?php echo $event['component'].'['.$event['version'].']';?></td>
			<td><?php echo $event['site'];?></td>
			<td><?php echo $event['message'];?></td>
			<td><?php echo $event['notes'];?></td>
			<td><?php echo $event['url_from'];?></td>
			<td><?php echo $event['url_to'];?></td>
			<td><?php echo $event['create_date'];?></td>
			<td>
				<div onclick="exec('Base/DeleteEvent/<?php echo $event['id_event'];?>','Site/Events/<?php echo $type;?>')">
					<i class="icon-remove"></i>
				</div>
			</td>
		</tr>
	<?php } ?>
	</tbody>
<table>