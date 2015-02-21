<div class="modal">
  <div class="modal-header">
    <a class="close" onclick='RemoveDlg("Attachment_upload_dlg");'>×</a>
    <h3>Завантаження файлу</h3>
  </div>
<div class="modal-body" style=" text-align:center;">


	<form action="Attachment/Upload" method = "post" id = "dropForm<?php echo $id_attachment; ?>" enctype = "multipart/form-data" name = "up" >
			<input type="hidden" name="attachment_upload_id" id="attachment_upload_id" value="<?php echo $id_attachment; ?>"/>
			<input type="hidden" name="isAjax" value="1"/>
			
			<iframe name="uploader" src="" style="display: none"></iframe>
			<div class = "dropZone">
			
					<span id="dropZoneText<?php echo $id_attachment; ?>">Для завантаження  перетягніть файл сюди,<br> або клікніть по цьому полю.</span>
					
					<input type = "file" class = "dropZone" id = "dropZoneButton" name = "attachment" value = "" onChange = "$('#dropZoneText<?php echo $id_attachment; ?>').html('Файл додано!<br><small>Натисніть на кнопку Завантажити<small>')" ondrop = "">
			</div>		
			<br/>
			<?php /*<input type="text" name="specifiedName" id="specifiedName" placeholder="Назва файлу" /> */ ?>
			

	</form>
</div>
  <div class="modal-footer">
    <a href="#" class="btn btn-success completed" onclick='RemoveDlg("Attachment_upload_dlg");'>Відміна</a>

    <a href="#" class="btn btn-success completed" onclick="$('#dropForm<?php echo $id_attachment; ?>').submit();RemoveDlg('Attachment_upload_dlg');update('Attachment/View/<?php echo $id_attachment;?>')">Завантажити</a>
  </div>
</div>