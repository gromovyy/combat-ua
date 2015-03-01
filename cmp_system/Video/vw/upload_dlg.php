<div class="modal">
  <div class="modal-header">
    <a class="close" onclick='RemoveDlg("Video_upload_dlg");'>×</a>
    <h3>Завантаження відео</h3>
  </div>
<div class="modal-body" style="">


	<form action="Video/SetVideo" method = "post" id = "dropForm" enctype = "multipart/form-data" name = "up" >
			<input type="hidden" name="video_id" id="video_id" value="<?php echo $id_video; ?>"/>
			<input type="hidden" name="isAjax" value="1"/>
			
					
			<center>
			<input type="text" name="url_video" id="url_video" placeholder="Лінк на відео" /><br/>
			<input type="text" name="title_video" placeholder="Ім`я"/>
			</center>
	</form>
	<small>Лінк на відео повинен містити посилання на завантажене відео на YouTube	</small>
</div>
  <div class="modal-footer">
    <a href="#" class="btn btn-success completed" onclick='RemoveDlg("Video_upload_dlg");'>Відміна</a>

    <a href="#" class="btn btn-success completed" onclick="$('#dropForm').submit();RemoveDlg('Video_upload_dlg');update('Video/View/<?php echo $id_video;?>')">Завантажити</a>
  </div>
</div>