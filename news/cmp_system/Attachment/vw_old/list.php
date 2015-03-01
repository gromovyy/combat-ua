<?php
	if ((empty($list) and ($this->User->getMode()=="view"))or( empty($list) and !$this->is_insert())) return;
	// Ссылка для обновления данного вида.
	$view_update_link = "Attachment/List/$component/$id_object/$object";

//	if ($this->V($list)) {
		// Подключаем библиотеки, ноебходимые для фотогаллереи
?>
<div>
		<div>
			<span class="header-table1"><?php echo $title;?></span>
			<?php if ($this->is_insert()) { ?>
			<div class="btnAdd" onclick="exec('Attachment/Insert/<?php echo "$component/$id_object/$object";?>','<?php echo $view_update_link;?>')"></div>
			<?php } ?>
			<div class="clear"></div>
<?php 
		foreach ($list as $attachment) { 
			$this->LoadView("view",array("attachment"=>$attachment,"parent_update_link"=>$view_update_link));
		}
?>	
		<br />
		</div>
<?php// } ?>
</div>
<div style="clear:both"></div>
<br />