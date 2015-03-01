<?php
	// Ссылка для обновления данного вида.
	$view_update_link = "Forum/TopicList/".$forum["id_forum"];
	$component = $topic["component"];
?>
<div>
	<div class="page-title"><a href="Форум">Перейти до списку форумів</a> >> <a href ="<?php echo $this->href($forum["id_forum"], $forum["name"]);?>"><?php echo $forum["name"];?></a> >> <?php echo $topic["name"]; ?></span>
	<?php if (!empty($forum["is_automatic"])) { ?>
	<a class="object-link" href="<?php echo $this->$component->href($topic["id_object"], $topic["name"]);?>">Перейти до сторінки обговорення</a>
	<?php } ?>
	</div>
</div>
