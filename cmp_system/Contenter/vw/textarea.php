<?php 
if ($is_update) {
	// Проверка на то можем ли мы подлючтить CkEditor
	$this->includeJS('//cdnjs.cloudflare.com/ajax/libs/ckeditor/4.2/ckeditor.js', 4);
}
?>
<!-- <ckeditor> -->
<div <?php echo ($is_update)?"id='$id' class='contenter-input' onclick=\"textarea('$id');return false;\"":"";?> >
	<?php echo ($value=="")?"&nbsp":$value; ?>
</div>