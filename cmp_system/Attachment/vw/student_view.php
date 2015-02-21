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
    </div>
</div>