<div class="modal">
  <div class="modal-header">
    <a class="close" onclick='RemoveDlg("Photo_upload_dlg");'><?php echo $_['16'];?></a>
    <h3><?php echo $_['17'];?></h3>
  </div>
<div class="modal-body" style="height:240px; text-align:center;">


	<form action="Photo/Upload" method = "post" id = "dropForm" enctype = "multipart/form-data" name = "up" >
			<input type="hidden" name="photo_upload_id" id="photo_upload_id" value="<?php echo $id_photo; ?>"/>
			<input type="hidden" name="isAjax" value="1"/>
			
			<iframe name="uploader" src="" style="display: none"></iframe>
			<div class = "dropZone" >
								

					<span id="dropZoneText"><?php echo $_['18'];?><br><?php echo $_['14'];?></span>
					
					<input type = "file"  id = "dropZoneButton" name = "photo" value = "" onChange = "$('#dropZoneText').html('Файл додано!<br><small>Не забудьте назвати ваш файл<small>')" ondrop = "">
			</div>		
			<br/>
			<input type="text" name="specifiedName" class="inline-text" id="specifiedName" placeholder="Назва фото" />
			<input type="submit" class="inline-sumbit" value="<?php echo $_['21'];?>"/>
			

	</form>
</div>
  <div class="modal-footer">
    <a href="#" class="btn btn-success completed" onclick='RemoveDlg("Photo_upload_dlg");'><?php echo $_['20'];?></a>

    <a href="#" class="btn btn-success completed" onclick="$('#dropForm').submit();RemoveDlg('Photo_upload_dlg');update('Photo/View/<?php echo $id_photo;?>')"><?php echo $_['21'];?></a>
  </div>
</div>