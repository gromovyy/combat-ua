<div class="modal fade in">
	<div class="modal-dialog modal-lg modal-content">
		<div class="modal-header">
			<a class="close" onclick="$(this).parent().parent().parent().modal('hide');"><?php echo $_['11'];?></a>
			<h3><?php echo $_['12'];?></h3>
		</div>
		<div class="modal-body">
			<br />
			<div class="col-md-12"><?php echo $_['13'];?><strong><?php echo $this->Member->getName($id_user);?></strong>	?</div>
			<br />
		</div>
		<div class="modal-footer">
			<br />
			<button class="btn btn-success right" onclick="reload('Member/Delete/<?php echo $id_user;?>');"><?php echo $_['14'];?></button>
		</div>
	</div>
</div>
