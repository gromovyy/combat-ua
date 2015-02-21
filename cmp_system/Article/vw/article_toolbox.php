<?php 
	$view_update_link = 'Article/View/'.$id_article;
?>
<?php if ($this->is_update(null, $id_article)) { ?>
<div class="article-toolbox">
	<span>FLASH</span>
	<div class="tool-add" onclick = "exec('Article/InsertFlashContent/<?php echo $id_article;?>','<?php echo $view_update_link;?>')"></div>
	<span>ВІДЕО</span>
	<div class="tool-add" onclick = "exec('Article/InsertVideoContent/<?php echo $id_article;?>','<?php echo $view_update_link;?>')"></div>
	<span>МАЛЮНОК</span>
	<div class="tool-add" onclick = "exec('Article/InsertPhotoContent/<?php echo $id_article;?>','<?php echo $view_update_link;?>')"></div>
	<span>ТЕКСТ</span>
	<div class="tool-add" onclick = "exec('Article/InsertTextContent/<?php echo $id_article;?>','<?php echo $view_update_link;?>')"></div>
</div>
<?php } ?>
