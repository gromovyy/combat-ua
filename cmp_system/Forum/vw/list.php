<?php 
	$this->includeCSS("forum.css");	
	$view_update_link = "Forum/List";
?>


<div>
	<div id="container_full">
		<table class="forum-table">
			<tr>
				<th align="left">Список форумів НСАУ
					<?php 	if ($this->is_insert()) { ?>
						<div class="btnAdd" onclick="exec('Forum/Insert','<?php echo $view_update_link;?>')"></div>			
					<? } ?>
				</th>
				<th class="topic-count">Тем</th>
				<th class="comment-count">Повідомленнь</th>
				<th class="last-comment">Остання відповідь</th>
				<th class="forum-control">Управління</th>
			</tr>
	<?php foreach ($forums as $forum) { ?>
		<tr>
			<td class="forum-title" align="left">
				<?php if ($this->User->getMode() != "edit") {?> <a href ="<?php echo $this->href($forum['id_forum'],$forum["name"]); ?>"> <?php }?>
				<?php $this->Input('text', 'frm_forum', 'name',  $forum['id_forum'], $forum["name"], Array(), $forum["id_owner"]);?>
				<?php if ($this->User->getMode() != "edit") {?> </a> <?php }?>
			</td>
			<td class="topic-count">
				<?php echo $forum["topic_count"];?>
			</td>
			<td class="comment-count">
				<?php echo $forum["comment_count"];?>
			</td>
			<td class="last-comment">
				<?php echo substr($forum['last_comment_date'],0,16);?>
			</td>
			<?php if ($this->is_update($forum['id_owner'], $forum['id_forum']) and $this->User->getMode() =="edit") { ?>
			<td class="forum-control">
				
				<div class="forum-toolbox">
					<div class="forum-toolbox-icons">
						<a href="#" class="forum-icon delete" onclick="exec('Forum/Delete/<?php echo $forum['id_forum'];?>','Forum/List');"></a>
						<a href="#" class="forum-icon up" onclick="exec('Forum/MoveLeft/<?php echo $forum['id_forum'];?>','Forum/List');"></a>
						<a href="#" class="forum-icon down" onclick="exec('Forum/MoveRight/<?php echo $forum['id_forum'];?>','Forum/List');"></a>
						<?php if (!empty($forum['is_member_insert'])) { ?>
						<a href="#" class="forum-icon topic-insert-auto" onclick="exec('Forum/ChangeMemberInsert/<?php echo $forum['id_forum'];?>','Forum/List');"></a>
						<?php } else { ?>
						<a href="#" class="forum-icon topic-insert-admin" onclick="exec('Forum/ChangeMemberInsert/<?php echo $forum['id_forum'];?>','Forum/List');"></a>
						<?php } ?>
						<?php if (!empty($forum['is_automatic'])) { ?>
						<a href="#" class="forum-icon topic-insert-member" onclick="exec('Forum/ChangeTopicSource/<?php echo $forum['id_forum'];?>','Forum/List');"></a>
						<?php } else { ?>
						<a href="#" class="forum-icon topic-insert-manual" onclick="exec('Forum/ChangeTopicSource/<?php echo $forum['id_forum'];?>','Forum/List');"></a>
						<?php } ?>
					</div>
					<?php if (!empty($forum['is_automatic'])) { ?>
					<ul class="forum-toolbox-fields active-edit-mode">
						<li>Component: <?php $this->Input('text', 'frm_forum', 'component',  $forum['id_forum'], $forum["component"], Array(), $forum["id_owner"]);?></li>
						<li>Field1: <?php $this->Input('text', 'frm_forum', 'field_name1',  $forum['id_forum'], $forum["field_name1"], Array(), $forum["id_owner"]);?></li>
						<li>Field2: <?php $this->Input('text', 'frm_forum', 'field_name2',  $forum['id_forum'], $forum["field_name2"], Array(), $forum["id_owner"]);?></li>
					</ul>
					<?php } ?>
				</div>
			</td>
			<?php } ?>
		</tr>
	<?php } ?>
	</table>
	<div class="clear"></div>
	<br />
	<div class="article-header">ОБГОВОРЕННЯ	/ ОСТАННІ 50 ПОВІДОМЛЕНЬ</div>
	<div class="article-line"></div>	
	<div class="clear"></div>
	<?php $this->e_LastComments();?>
	<script>
		window.setInterval( "update('Forum/LastComments')", 30000);
	</script>
</div>