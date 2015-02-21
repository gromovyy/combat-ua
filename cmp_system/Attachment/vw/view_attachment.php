<?php 
//	$this->includeJS("attachment.js");
//	$this->includeCSS("system.css");
	$view_update_link = "Attachment/OneAttachment/$id_object";
?>
<div>
	<div class="attachment_file_block" id="a<?php echo $id_object;?>">
		<?php if (!empty($attachment["file_name"])) { ?>
			<a class = "attachment_link" href="Attachment/Download/<?php echo $attachment["id_attachment"]?>">
				<i class="glyphicon glyphicon-paperclip"></i>
				<span class="attachment"><?php echo $attachment["file_name"];?></span>
			</a>			
		<?php } 
			else  { ?>
			<span class="no-attachment">
				ФАЙЛ НЕ ПРИКРІПЛЕНО
			</span>
		<?php } ?>
				
			
		<?php if ($this->is_delete($attachment["id_owner"]) and $attachment["is_user_delete"]) { ?>
				
				<div class="tool-delete" onclick="exec('<?php echo $component;?>/DeleteAttachment/<?php echo $id_object;?>','<?php echo $view_update_link;?>')"></div>
		<?php } ?>
		<?php if ($this->is_update($attachment["id_owner"])) { ?>
				<div class="tool-edit" onclick="loadDlg('<?php echo $component;?>/UploadAttachmentDlg/<?php echo $id_object;?>', {'height':'300px'})"></div>
		<?php } ?>
	</div>
</div>