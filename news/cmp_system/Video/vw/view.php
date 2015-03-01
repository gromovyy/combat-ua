<?php
//		$this->includeJS("jquery.fancybox.pack.js");
//		$this->includeCSS("jquery.fancybox.css");
?>
<div class="video-preview">
		<?php if ($this->is_delete($video["id_owner"]) and $video["is_user_delete"]) { ?>
				<div class="tool-delete" onclick="exec('Video/Delete/<?php echo $video["id_video_bind"];?>','<?php echo $parent_update_link;?>')"></div>
		<?php } ?>
		<?php if ($this->is_update($video["id_owner"])) { ?>
			<div class="toolbox">
				<div class="tool-edit" onclick="loadDlg('Video/UploadDlg/<?php echo $video["id_video_bind"];?>')"></div>
			</div>
		<?php } ?>		
		<iframe width="100%" <?php if (!empty($video["url_video"]))		
			echo 'src="//www.youtube.com/embed/'.$video["url_video"].'?wmode=transparent"';?> 
			frameborder="0" allowfullscreen wmode="Opaque" style="background-color: #DDDDDD">
		</iframe>			
</div>