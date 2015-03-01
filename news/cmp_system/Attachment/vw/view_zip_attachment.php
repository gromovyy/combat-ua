<?php 
//	$this->includeJS("attachment.js");
//	$this->includeCSS("system.css");
	$view_update_link = "Attachment/ZipAttachment/$id_object/$component";
?>
<div>
	<div class="attachment_file_block" id="a<?php echo $id_object;?>">
		<?php if (!empty($id_attachment)) { ?>
			<a class = "attachment_link" href="Attachment/ZipDownload/<?php echo $id_object."/".$component;?>">
				<i class="glyphicon glyphicon-paperclip"></i>
				<span class="attachment">task_attachment_<?php echo $id_object;?>.zip</span>
			</a>			
		<?php } 
			else  { ?>
			<span class="no-attachment">
				ФАЙЛ НЕ ПРИКРЕПЛЕН
			</span>
		<?php } ?>
				
			
		<?php /*if ($this->is_delete($attachment["id_owner"]) and $attachment["is_user_delete"]) { ?>
				
				<div class="tool-delete" onclick="exec('Attachment/DeleteAttachment/<?php echo $id_object;?>','<?php echo $view_update_link;?>')"></div>
		<?php } ?>
		<?php if ($this->is_update($attachment["id_owner"])) { ?>
				<div class="tool-edit" onclick="loadDlg('<?php echo $component;?>/UploadAttachmentDlg/<?php echo $id_object;?>', {'height':'300px'})"></div>
		<?php }*/ ?>
	</div>
</div>