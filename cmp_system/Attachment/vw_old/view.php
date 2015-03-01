<?php 
	$this->includeJS("attachment.js");
	$this->includeCSS("system.css");
	$view_update_link = "Attachment/View/{$attachment["component"]}/{$attachment["id_object"]}";
?>
<div>
	<div class="attachment_file_block" id="a<?php echo $attachment["id_attachment"]?>">
		<?php if (!empty($attachment["file_name"])) { ?>
			<a class = "attachment_link" href="Attachment/Download/<?php echo $attachment["id_attachment"]?>">
				<img class = "attachment_icon" alt="<?php echo $attachment["title"];?>" src="lib/img/icon/download.png"/>
			</a>			
		<?php } ?>
			
			<span class='attachment-title'>
				<?php echo $attachment["title"];?>
			</span>
		
		<?php/* if ($this->is_delete($attachment["id_owner"], $attachment["id_attachment"])) { ?>
			<div class="btnDell" onclick="exec('Attachment/Delete/<?php echo $attachment["id_attachment"];?>','<?php echo $view_update_link;?>')"></div>	
		<?php }*/ ?>
			
		<?php if ($this->is_update($attachment["id_owner"], $attachment["id_attachment"])) { ?>					
			<div class="btnAttachment" onclick="$('#attachment_upload_id').val(<?php echo $attachment["id_attachment"];?>);$('#attachment-title').val('<?php echo $attachment["title"];?>');$('#attachment_upload_dialog').fadeIn();">Прикріпити файл</div>
		<?php } ?>
    </div>
</div>