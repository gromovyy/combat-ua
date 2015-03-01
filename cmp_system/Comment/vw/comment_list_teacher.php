
<?php
$view_update_link = "Comment/ModuleComments/$id_module"
?>

<div>
	<div class="content-menu">
		<div class="content-menu-button content-menu-button-question button-active">
					<a>
						<div class="content-menu-circle circle-mid active"></div>
						Запитання (більше 9000)
					</a>
		</div>
	</div>
<div class="window-answer clearfix">
	<p class="name-student muted">Запитати учня</p>
	<p class="name-discipline muted">
		<select  class="window-ansver-select" id="select-user">
			<option selected disabled value="" >Виберіть Учня</option>
			<?php foreach($students as $student){?>
			<option value="<?php echo $student['id_owner'];?>">Учень <?php echo $student['id_owner'];?></option>

			   <?php } ?>

			<option disabled> - - - </option>
			<option value="all">Усім учням</option>
			
		</select>
	</p>
	<p class="number-comment muted"></p>
	<textarea id="text" rows="5" class="span12 comment-textarea"></textarea>
	<div class="comment-send pull-right btn-link" onclick="
	if ((!($.trim($('#text').val())==''))&&($('#select-user').val()!='')) 
		{
		exec('Comment/Insert/<?php echo "Module/$id_module";?>/' + ($('#select-user').val()),'<?php echo $view_update_link;?>', {text:$('#text').val()})
		}
		
	">Відправити</div> 
</div>
<hr>
<?php  foreach ( $comments as  $comment) { ?>
<div>
	<div class="question">
		<div class="date muted">
			<?php echo $comment['create_date'];?>	
			<a href="Модуль-студента/<?php echo $comment['id_module_result'];?>">Лінк на модуль користувача.</a>
			
		</div>
		<?php echo $comment['text'];?>
	</div>
	<?php if(!empty($comment['children'])){ foreach($comment['children'] as $answer){?>
	<div class="answer">
		<div class="date muted"><?php echo $answer['create_date'];?></div>
		<?php echo $answer['text'];?>
	</div>
	<?php }} ?>
	<div class="window-answer clearfix">
	<textarea id="text<?php  echo $comment['id_comment'];?>" rows="2" style="display:none;" class="span12 comment-textarea" ></textarea>
	<div class="comment-send pull-right btn-link" onclick="
	if (!($.trim($('#text<?php echo $comment['id_comment'];?>').val())=='')) 
		{exec('Comment/Insert/<?php echo "Module/$id_module/{$comment['id_user']}/{$comment['id_comment']}";?>','<?php echo $view_update_link;?>', {text:$('#text<?php echo $comment['id_comment'];?>').val()});$('#text<?php echo $comment['id_comment'];?>').val('');}else{$('#text<?php echo $comment['id_comment'];?>').slideToggle(400)}
	">Відповісти</div> 
</div>
</div>
<hr/>
<?php } ?>

<script>
	//window.setInterval( "update('<?php echo $view_update_link;?>')", 2000);
</script>
</div>