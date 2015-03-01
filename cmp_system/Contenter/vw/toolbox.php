<!-- <span> -->
<div class="toolbox-wrapper">
<div class="toolbox">
	<?php if (in_array("delete",$tool_list)) { ?>
		<span class="tool-delete" onclick="event.stopPropagation();exec('<?php echo $component;?>/Delete/<?php echo $object."/".$id_object;?>','<?php echo $update_link;?>' <?php if (empty($update_link)) {?>, {} , true <?php } ?>);">
			<i class="fa fa-times"></i>
		</span>
	<?php } ?>	
	<?php if (in_array("delete-dlg",$tool_list)) { ?>
		<span class="tool-delete-dlg" onclick="event.stopPropagation();loadDlg('<?php echo $component;?>/DeleteDlg/<?php echo $object."/".$id_object."/".$update_link;?>');">
			<i class="fa fa-times-circle-o"></i>
		</span>
	<?php } ?>
	
	<?php if (in_array("edit",$tool_list)) { ?>
		<span class="tool-edit" onclick="event.stopPropagation();loadDlg('<?php echo $component;?>/EditDlg/<?php echo $object."/".$id_object;?>');">
			<i class="fa fa-edit"></i>
		</span>
	<?php } ?>
	<?php if (in_array("visible",$tool_list)) { ?>
		<span class="tool-visible" onclick="event.stopPropagation();exec('<?php echo $component;?>/Show/<?php echo $object."/".$id_object;?>','<?php echo $update_link;?>');">
				<i class="fa fa-eye-open"></i>
		</span>
	<?php } ?>
	<?php if (in_array("hidden",$tool_list)) { ?>
		<span class="tool-hidden" onclick="event.stopPropagation();exec('<?php echo $component;?>/Show/<?php echo $object."/".$id_object;?>','<?php echo $update_link;?>');">
			<i class="fa fa-eye-close"></i>
		</span>
	<?php } ?>
	<?php if (in_array("down",$tool_list)) { ?>
	<span class="tool-move-down" onclick="event.stopPropagation();exec('<?php echo $component;?>/MoveRight/<?php echo $object."/".$id_object."/".$component_bind."/".$id_object_bind."/".$object_bind;?>','<?php echo $update_link;?>');">
		<i class="fa fa-arrow-down"></i>
	</span>
	<?php } ?>
	<?php if (in_array("up",$tool_list)) { ?>
	<span class="tool-move-up" onclick="event.stopPropagation();exec('<?php echo $component;?>/MoveLeft/<?php echo $object."/".$id_object."/".$component_bind."/".$id_object_bind."/".$object_bind;?>','<?php echo $update_link;?>');">
		<i class="fa fa-arrow-up"></i>
	</span>
	<?php } ?>
	<?php if (in_array("right",$tool_list)) { ?>
	<span class="tool-move-right" onclick="event.stopPropagation();exec('<?php echo $component;?>/MoveRight/<?php echo $object."/".$id_object."/".$component_bind."/".$id_object_bind."/".$object_bind;?>','<?php echo $update_link;?>');">
		<i class="fa fa-arrow-right"></i>
	</span>
	<?php } ?>
	<?php if (in_array("left",$tool_list)) { ?>
	<span class="tool-move-left" onclick="event.stopPropagation();exec('<?php echo $component;?>/MoveLeft/<?php echo $object."/".$id_object."/".$component_bind."/".$id_object_bind."/".$object_bind;?>','<?php echo $update_link;?>');">
		<i class="fa fa-arrow-left"></i>
	</span>
	<?php } ?>
	<?php if (in_array("add",$tool_list)) { ?>
		<span class="tool-add" onclick="event.stopPropagation();exec('<?php echo $component;?>/Insert/<?php echo $object."/".$component_bind."/".$id_object_bind."/".$object_bind;?>','<?php echo $update_link;?>');">
			<i class="fa fa-plus"></i>
		</span>
	<?php } ?>
	<?php if (in_array("select",$tool_list)) { ?>
		<span class="tool-select" onclick="event.stopPropagation();loadDlg('<?php echo $component;?>/SelectDlg/<?php echo $object."/".$component_bind."/".$id_object_bind."/".$object_bind;?>');">
		
		</span>
	<?php } ?>
</div>

</div>