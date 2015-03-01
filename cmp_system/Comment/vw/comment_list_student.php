<?php
$view_update_link = "Comment/StudentComments/$id_module_result"
?>

<div>
	<div class="content-menu">
		<div class="content-menu-button content-menu-button-question button-active">
					<a>
						<div class="content-menu-circle circle-mid active"></div>
						Запитання
					</a>
		</div>
	</div>
	
<div class="window-answer clearfix">
	<p class="name-student muted">Створити нове запитання</p>
	<p class="name-discipline muted"></p>
	<p class="number-comment muted"></p>
	<textarea id="text" rows="5" class="span12 comment-textarea"></textarea>
	<div class="comment-send pull-right btn-link" onclick="
	if (!($.trim($('#text').val())=='')) 
		{exec('Comment/Insert/<?php echo "Module/$id_module/$id_user";?>','<?php echo $view_update_link;?>', {text:$('#text').val()});$('#text').val('');}
	">Відправити</div> 
</div>

<?php  foreach ( $comments as  $comment) { ?>
<div>
	<div class="question">
		<div class="date muted"><?php echo $comment['create_date'];?>	</div>
		<?php echo $comment['text'];?>
	</div>
	<?php if(!empty($comment['children'])){ foreach($comment['children'] as $answer){?>
	<div class="answer">
		<div class="date muted"><?php echo $answer['create_date'];?></div>
		<?php echo $answer['text'];?>
	</div>
	<?php }} ?>
	<div class="window-answer clearfix">
	<textarea id="text<?php  echo $comment['id_comment'];?>" rows="2" style="display:none;" class="span12 comment-textarea"></textarea>
	<div class="comment-send pull-right btn-link" onclick="
	if (!($.trim($('#text<?php echo $comment['id_comment'];?>').val())=='')) 
		{exec('Comment/Insert/<?php echo "Module/$id_module/{$comment['id_user']}/{$comment['id_comment']}";?>','<?php echo $view_update_link;?>', {text:$('#text<?php echo $comment['id_comment'];?>').val()});$('#text<?php echo $comment['id_comment'];?>').val('');}else{$('#text<?php echo $comment['id_comment'];?>').slideToggle(400)}
	">Відповісти</div> 
</div>
</div>
<hr/>
<?php } ?>
<script>
//	window.setInterval( "update('<?php echo $view_update_link;?>')", 2000);
</script>
</div>
