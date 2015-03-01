<!-- Modal -->
<div>
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<form action="Tracker/StopWork/<?php echo $id_tracker;?>" id='add-comment' method = "POST" enctype = "multipart/form-data">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span>
							<span class="sr-only">Закрити</span>
						</button>
						<h4 class="modal-title" id="myModalLabel">Що зроблено</h4>
					</div>
					
					<div class="modal-body">
							<input type="hidden" name="id_tracker" id="id_tracker" value="">
							<textarea name="work" id="work" cols="30" rows="10" class="form-control"></textarea>
							<div class="files">
								<div class="list-group">
								</div>
								<a onclick="addFile(this)"><i class="glyphicon glyphicon-plus-sign"></i>Додати файл</a>
							</div>
						
					</div>
					<div class="modal-footer">
						<span class="error">Мінімальна довжина коментаря - 5 букв</span>
						<button type="button" class="btn btn-default" data-dismiss="modal">Закрити</button>
						<button type="button" class="btn btn-primary" onclick="$('#work').val(CKEDITOR.instances.work.getData()); if($('#work').val().length <=4) $('.modal-footer .error').fadeIn(); else $('#add-comment').submit();">Зберегти</button>
					</div>
				</div>
			</div>
		</form>
	</div><!-- /#myModal -->
</div>