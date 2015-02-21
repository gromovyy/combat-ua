<div class="modal">
  <div class="modal-header">
    <a class="close" onclick='RemoveDlg("Flash_upload_dlg");'>×</a>
    <h3>Завантаження фотографії</h3>
  </div>
<div class="modal-body">

	<form action="Flash/Upload/" method = "post" id = "dropForm"  enctype = "multipart/form-data" >
	
			<input type="hidden" name="id_flash" id="id_flash" value="<?php echo $id_flash; ?>"/>
			<input type="hidden" name="isAjax" value="1"/>
				<div class = "dropZone" >
						<span id="dropZoneText1">
						Скомпільований файл (SWF)
						</span>
						<input type = "file" id="dropZoneButton" name = "flash_swf" value = "" onChange = "$('#dropZoneText1').html('Скомпільований файл (SWF)<br/>			<small>Файл додано!</small>');" ondrop = "">		
				</div>			
<br/>				
				<div class = "dropZone" >
						<span id="dropZoneText2">
						Вихідний файл (FLA)
						</span>
						<input type = "file" id="dropZoneButton" name = "flash_fla" value = "" onChange = "$('#dropZoneText2').html('Вихідний файл (FLA)<br/>						<small>Файл додано!</small>');" ondrop = "">		
				</div>	
	</form>
</div>
  <div class="modal-footer">
    <a href="#" class="btn btn-success completed" onclick='RemoveDlg("Flash_upload_dlg");'>Відміна</a>

    <a href="#" class="btn btn-success completed" onclick="$('#dropForm').submit();RemoveDlg('Flash_upload_dlg');update('Flash/View/<?php echo $id_flash;?>')">Завантажити</a>
  </div>
</div>