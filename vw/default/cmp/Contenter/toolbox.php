<div class="toolbox">
	<?php if (in_array("delete",$tool_list)) { ?>
		<div class="tool-delete" onclick="exec('<?php echo $component;?>/Delete/<?php echo $object."/".$id_object;?>','<?php echo $update_link;?>');"></div>
	<?php } ?>
	<?php if (in_array("edit",$tool_list)) { ?>
		<div class="tool-edit" onclick="loadDlg('<?php echo $component;?>/EditDlg/<?php echo $object."/".$id_object;?>');"></div>
	<?php } ?>
	<?php if (in_array("visible",$tool_list)) { ?>
		<div class="tool-visible" onclick="exec('<?php echo $component;?>/Show/<?php echo $object."/".$id_object;?>','<?php echo $update_link;?>');"></div>
	<?php } ?>
	<?php if (in_array("hidden",$tool_list)) { ?>
		<div class="tool-invisible" onclick="exec('<?php echo $component;?>/Show/<?php echo $object."/".$id_object;?>','<?php echo $update_link;?>');"></div>
	<?php } ?>
	<?php if (in_array("down",$tool_list)) { ?>
	<div class="tool-move-down" onclick="exec('<?php echo $component;?>/MoveRight/<?php echo $object."/".$id_object."/".$component_bind."/".$id_object_bind."/".$object_bind;?>','<?php echo $update_link;?>');"></div>
	<?php } ?>
	<?php if (in_array("up",$tool_list)) { ?>
	<div class="tool-move-up" onclick="exec('<?php echo $component;?>/MoveLeft/<?php echo $object."/".$id_object."/".$component_bind."/".$id_object_bind."/".$object_bind;?>','<?php echo $update_link;?>');"></div>
	<?php } ?>
	<?php if (in_array("right",$tool_list)) { ?>
	<div class="tool-move-right" onclick="exec('<?php echo $component;?>/MoveRight/<?php echo $object."/".$id_object."/".$component_bind."/".$id_object_bind."/".$object_bind;?>','<?php echo $update_link;?>');"></div>
	<?php } ?>
	<?php if (in_array("left",$tool_list)) { ?>
	<div class="tool-move-left" onclick="exec('<?php echo $component;?>/MoveLeft/<?php echo $object."/".$id_object."/".$component_bind."/".$id_object_bind."/".$object_bind;?>','<?php echo $update_link;?>');"></div>
	<?php } ?>
	<?php if (in_array("add",$tool_list)) { ?>
		<div class="tool-add" onclick="exec('<?php echo $component;?>/Insert/<?php echo $object."/".$component_bind."/".$id_object_bind."/".$object_bind;?>','<?php echo $update_link;?>');"></div>
	<?php } ?>
	<?php if (in_array("select",$tool_list)) { ?>
		<div class="tool-select" onclick="loadDlg('<?php echo $component;?>/SelectDlg/<?php echo $object."/".$component_bind."/".$id_object_bind."/".$object_bind;?>');"></div>
	<?php } ?>
</div>