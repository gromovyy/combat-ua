<?php 
		$this->includeJS("jquery.fancybox.pack.js");
		$this->includeCSS("jquery.fancybox.css");
?>

<div class="photo-preview " id="t<?php echo $photo["id_photo_bind"]?>">
     <a class="photo-link fancybox" href="<?php echo $photo["default_folder"].$photo["url_photo"];?>" id="<?php echo $photo["id_photo_bind"];?>" rel="group" title="<?php echo $photo["title_photo"]?>" >
         <img alt="<?php echo $photo["title_photo"];?>" src="<?php echo $photo["default_folder"]."small/".$photo["url_photo"];?>"/>
     </a>
	 
	<div class='toolbox'>

<?php if ($photo["is_full_preview"]) { ?>
		<a class="btnFull" href="<?php echo $photo["default_folder"]."full/".$photo["url_photo"];?>"></a>	
<?php } ?>
		
<?php if ($this->is_delete($photo["id_owner"]) and $photo["is_user_delete"]) { ?>
		
		<div class="tool-delete" onclick="exec('Photo/Delete/<?php echo $photo["id_photo_bind"];?>','<?php echo $parent_update_link;?>')"></div>
<?php } ?>
<?php if ($this->is_update($photo["id_owner"])) { ?>
		<div class="tool-edit" onclick="loadDlg('Photo/UploadDlg/<?php echo $photo["id_photo_bind"];?>')"></div>
<?php } ?>
	</div>

</div>