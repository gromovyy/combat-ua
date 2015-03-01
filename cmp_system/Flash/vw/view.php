<?php
	$this->includeJS("jquery.tools.min.js");
?>
<div class="flash-preview">
		<?php if ($this->is_delete($flash["id_owner"]) and $flash["is_user_delete"]) { ?>
				
				<div class="tool-delete" onclick="exec('Flash/Delete/<?php echo $flash["id_flash_bind"];?>','<?php echo $parent_update_link;?>')"></div>
		<?php } ?>
		<?php if ($this->is_update($flash["id_owner"])) { ?>
				<div class="toolbox"> 
					<div class="tool-edit" onclick="loadDlg('Flash/UploadDlg/<?php echo $flash["id_flash_bind"];?>')"></div>
				</div>	
		<?php } ?>
		<center>
			
			<div id="flash<?php echo $flash["id_flash_bind"];?>" style='width:100%; height:100%; background-color:#CCCCCC; position:relative;'
			   onclick = 'flashembed("flash<?php echo $flash["id_flash_bind"];?>", {src: "<?php echo $url_swf;?>?loop=0&autoplay=0&api=1", wmode: "opaque"}); $("#stop<?php echo $flash["id_flash_bind"];?>").css("display","block");' >
			</div>
			<div id="stop<?php echo $flash["id_flash_bind"];?>" onclick="$('#flash<?php echo $flash["id_flash_bind"];?>').html(''); $(this).css('display','none');" style="display:none; background-color:red; color:white; cursor:pointer">Зупинити FLASH-ролик</div>
			
			<?php if (!empty($flash["id_flash"])) { ?>
			<a class = "attachment_link" href="Flash/Download/swf/<?php echo $flash["id_flash_bind"];?>">Завантажити swf-файл</a>
			<a class = "attachment_link" href="Flash/Download/fla/<?php echo $flash["id_flash_bind"];?>">Завантажити fla-файл:</a>
			
			<?php } ?> 
			<?php /*
			<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab #version=5,0,0,0" width="100%">
				<param name=movie value="<?php echo $url_swf;?>">
				<param name=quality value=high>
				<embed src="<?php echo $url_swf;?>" quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash " type="application/x-shockwave-flash" width="100%" height = "400"></embed>
			</object>
			--> */ ?>
		</center>
				
</div>