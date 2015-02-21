<?php
	$view_update_link = "Forum/LastComments";
?>
<div>
	  <?php  foreach ( $comments as  $row) { ?>
		
		<div class="comment-text" id="c<?php echo $row["id_comment"];?>">
					<ul class="info">
<?php 	if ($this->is_delete($row["id_owner"], $row["id_comment"])) { ?>
						<li class="forum-link"><div class="btnDell" onclick="exec('Comment/Delete/<?php echo $row["id_comment"];?>', '<?php echo $view_update_link;?>')"></div></li>
<?php 	} ?>
						<li class="forum-link"><a href="<?php echo $this->href($row["id_forum"], $row["forum_name"]);?>"><?php echo $row["forum_name"];?></a> >> <a href="<?php echo $this->href_topic($row['id_topic'],  $row["topic_name"]);?>"><?php echo mb_substr($row["topic_name"],0,50,'utf-8'); ?></a></li>
						<li class="comment-author"><a href="<?php echo $this->Member->href($row["id_person"],$row["name"], $row["surname"]); ?>"><?php echo $row["FIO"];?></a></li>
						<li class="comment-time"><?php echo substr($row["create_date"],0,16);?></li>
					</ul> 
					<a href ="<?php echo $this->href_topic($row['id_topic'],  $row["topic_name"]);?>#c<?php echo $row["id_comment"];?>">
						<?php echo $row["text"];?>
					</a>
				</div>
		<?php } ?>
</div>