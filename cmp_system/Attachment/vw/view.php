<?php 
//	$this->includeJS("attachment.js");
//	$this->includeCSS("system.css");
	$view_update_link = "Attachment/View/{$attachment["component"]}/{$attachment["id_object"]}";
?>
<div>
	<div class="attachment_file_block" id="a<?php echo $attachment["id_attachment"]?>">
		<?php if (!empty($attachment["file_name"])) { ?>
			<a class = "attachment_link" href="Attachment/Download/<?php echo $attachment["id_attachment_bind"]?>">
				Файл <?php echo $attachment["file_name"];?>
			</a>			
		<?php } 
			else  { ?>
			ФАЙЛ НЕ ПРИКРІПЛЕНО
		<?php } ?>
				
			
		<?php if ($this->is_delete($attachment["id_owner"]) and $attachment["is_user_delete"]) { ?>
				
				<div class="tool-delete" onclick="exec('Attachment/Delete/<?php echo $attachment["id_attachment_bind"];?>','<?php echo $parent_update_link;?>')"></div>
		<?php } ?>
		<?php if ($this->is_update($attachment["id_owner"])) { ?>
				<div class="tool-edit" onclick="loadDlg('Attachment/UploadDlg/<?php echo $attachment["id_attachment_bind"];?>')"></div>
		<?php } ?>
		
    </div>
</div>