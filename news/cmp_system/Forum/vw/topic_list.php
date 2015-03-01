<?php 
	$this->includeCSS("forum.css");	
?>

<div>
	<table class="forum-table">
		<tr>
			<th align="left">Тема</th>
			<th class="comment-count">Повідомленнь</th>
			<th class="comment-count">Автор</th>
			<th class="last-comment">Остання відповідь</th>
			<th class="forum-control">Управління</th>
		</tr>
	<?php foreach ($topics as $topic) {  if (empty($topic["name"]) and ($this->User->getRole()!='administrator')) continue;?>
		<tr>
			<td align="left">
				<a href ="<?php echo $this->href_topic($topic['id_topic'],  $topic["name"]);?>">
					<span class="topic-name"><?php $this->Input('text', 'frm_topic', 'name',  $topic['id_topic'], $topic["name"], Array(), $topic["id_owner"]);?></span>
				</a>
			</td>
			<td class="comment-count">
				<?php echo $topic['count_comment'];?>
			</td>
			<td class="forum-author">
				<a href ="<?php echo $this->Member->href($topic['author_id'],  $topic['author_name'], $topic['author_surname']);?>">
					<?php echo $topic['author_surname'];?>
				</a>
			</td>
			<td class="last-comment">
				<a href ="<?php echo $this->href_topic($topic['id_topic'],  $topic["name"]);?>#c<?php echo $topic['last_comment_id'];?>">
				<?php echo substr($topic['last_comment_date'],0,16);?></a><br />
				<?php /*<a href ="<?php echo $this->Member->href($topic['commenter_id'],  $topic['commenter_name'], $topic['commenter_surname']);?>">
					<?php echo $topic['commenter_surname'];?>
				</a>*/
				?>
			</td>
			<td class="forum-control">
				<?php if ($this->is_update($topic['id_owner'], $topic['id_topic'],null,"frm_topic") and $this->User->getMode() =="edit") { ?>
				<div class="topic-toolbox">
					<div class="forum-toolbox-icons">
						<a href="#" class="forum-icon delete" onclick="exec('Forum/DeleteTopic/<?php echo $topic['id_topic'];?>','Forum/TopicList/<?php echo $topic['id_forum'];?>');"></a>
					</div>	
				</div>
				<?php } ?>
			</td>

	</tr>
	<?php } ?>
	</table>
</div>