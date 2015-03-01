<?php 
$view_update_link = "Photo/View/$id_photo"
?>

<div class="photo-preview" >
	<center>
	<div>
		<form action="Photo/Upload" method = "post" id = "dropForm<?php echo $photo['id_photo_bind'];?>"  enctype = "multipart/form-data" name = "up" 
		onsubmit="AIM.submit(this,{'onComplete' : function(){update('<?php echo $view_update_link;?>')}});">
				<input type="hidden" name="photo_upload_id" id="photo_upload_id" value="<?php echo $photo["id_photo_bind"];?>"/>
				<input type="hidden" name="isAjax" value="1"/>
				<div class = "dropZone" >
						<span id="dropZoneText<?php echo $photo["id_photo_bind"];?>"><?php echo $_['13'];?><br><?php echo $_['14'];?>
						</span>
						<input type = "file" id="dropZoneButton" name = "photo" onChange = "$('#dropForm<?php echo $photo['id_photo_bind'];?>').submit();"
<?php  /*onChange = " 

update('<?php echo $view_update_link;?>')
update('Photo/View/<?php echo $photo["id_photo_bind"];?>')

						exec('Photo/Upload','<?php echo $view_update_link;?>', $('#dropForm<?php echo $photo["id_photo_bind"];?>').serializeArray());
						" */?><?php echo $_['15'];?>		
				</div>		
		</form>
	</div>
	 </center>
	<div class='toolbox'>
		
		<?php if ($this->is_delete($photo["id_owner"]) and $photo["is_user_delete"]) { ?>		
			<div class="tool-delete" onclick="exec('Photo/Delete/<?php echo $photo["id_photo_bind"];?>','<?php echo $parent_update_link;?>')"></div>
		<?php } ?>

	</div>

</div>