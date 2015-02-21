<?php $view_update_link = 'Article/View/'.$view['id_article']; ?>

<div class="clearfix">

<div class="page-header">
  <h4>
		<?php echo $view["title"];?> 
		<small class="pull-right"><?php echo substr($view["create_date"],0,11);?></small>
	</h4>
</div>
	<div>
		<?php echo $view["content"];?> 
	</div>
	
	<?php foreach ($article_content as $content) { ?>
		<div class='article-content'>
		<?php	
			switch ($content['content_type']) {
				case 'photo': $this->Photo->e_StudentView($content['id_content']); break;
				case 'video': $this->Video->e_StudentView($content['id_content']); break;
				case 'flash': $this->Flash->e_StudentView($content['id_content']); break;
				case 'text' : echo $content["content"];break; 				
			} ?>
		</div>
	<?php } ?>
</div>
