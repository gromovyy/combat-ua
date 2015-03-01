<?php
	//print_r($data); // Полученые даные из модуля.
	$view_update_link='Photo/PhotoType';
?>
<table class="table">
	<thead>
		<tr>
			<th><?php echo $_['0'];?>        </th>
			<th><?php echo $_['1'];?>    </th>
			<th><?php echo $_['2'];?>       </th>
			<th><?php echo $_['3'];?> </th>
			<th><?php echo $_['4'];?>   </th>
			<th><?php echo $_['5'];?>  </th>
			<th><?php echo $_['6'];?></th>
			<th><?php echo $_['7'];?>  </th>
			<th><?php echo $_['8'];?> </th>
			<th><?php echo $_['9'];?>      </th>
			<th><?php echo $_['10'];?>   </th>
			<th><?php echo $_['11'];?>    </th>
			<th><?php echo $_['12'];?>      </th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($types as $type){ ?>
		<tr>
			<td><?php echo $type['<?php echo $_['0'];?>'        ]; ?></td>
			<td><?php echo $type['<?php echo $_['1'];?>'    ]; ?></td>
			<td><?php echo $type['<?php echo $_['2'];?>'       ]; ?></td>
			<td><?php echo $type['<?php echo $_['3'];?>' ]; ?></td>
			<td><?php echo $type['<?php echo $_['4'];?>'   ]; ?></td>
			<td><?php echo $type['<?php echo $_['5'];?>'  ]; ?></td>
			<td><?php echo $type['<?php echo $_['6'];?>']; ?></td>
			<td><?php echo $type['<?php echo $_['7'];?>'  ]; ?></td>
			<td><?php echo $type['<?php echo $_['8'];?>' ]; ?></td>
			<td><?php echo $type['<?php echo $_['9'];?>'      ]; ?></td>
			<td><?php echo $type['<?php echo $_['10'];?>'   ]; ?></td>
			<td><?php echo $type['<?php echo $_['11'];?>'    ]; ?></td>
			<td><?php echo $type['<?php echo $_['12'];?>'      ]; ?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>