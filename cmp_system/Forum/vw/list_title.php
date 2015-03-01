<?php
	// Ссылка для обновления данного вида.
	$view_update_link = "Forum/List";
?>
<div>
	<span class="text-title">ФОРУМ</span>

<?php 	if ($this->is_insert()) { ?>
	<div class="btnAdd" onclick="exec('Forum/Insert','<?php echo $view_update_link;?>')"></div>			
<? } ?>
</div>