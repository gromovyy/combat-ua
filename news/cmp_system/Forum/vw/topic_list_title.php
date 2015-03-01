<?php
	// Ссылка для обновления данного вида.
	$view_update_link = "Forum/TopicList/".$forum["id_forum"];
?>
<div>
	<div class="page-title"><a href="Форум">Список форумів НСАУ</a> >> <?php echo $forum["name"];?>
	</div>
	<?php 	if ($this->is_insert_topic($forum["id_forum"])) { ?>
		<button class="btnAddTopic" onclick="exec('Forum/InsertTopic/<?php echo $forum["id_forum"];?>','<?php echo $view_update_link;?>')">Додати нову тему</button>			
	<? } ?>
</div>
