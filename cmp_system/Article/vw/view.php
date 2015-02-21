<?php $view_update_link = 'Article/View/'.$view['id_article']; ?>

<div class="clearfix">

<div class="page-header">
  <h4>
		<?php $this->Input('text', 'artcl_article', 'title', $view['id_article'], $view["title"], Array(),$view["id_owner"] );?> 
		<small class="pull-right"><?php $this->Input('date', 'artcl_article_bind', 'create_date', $view['id_article_bind'], substr($view["create_date"],0,11), Array(),$view["id_owner"] );?></small>
	</h4>
</div>
	<div>
		<?php $this->Input('textarea', 'artcl_article', 'content', $view['id_article'], $view["content"], Array(),$view["id_owner"]);?> 
	</div>
	
	<?php foreach ($article_content as $content) { ?>
		<div class='article-content'>
		<?php if ($this->is_update($content['id_owner'],$content['id_article'])): ?>
		<?php $this->loadToolBox('article_content', $content['id_article_content'], 'Article', 'article', $view['id_article'], Array('up','down','delete'), $view_update_link);?>
		<?php endif ?>
		<?php	
			switch ($content['content_type']) {
				case 'photo': $this->Photo->e_View($content['id_content']); break;
				case 'video': $this->Video->e_View($content['id_content']); break;
				case 'flash': $this->Flash->e_View($content['id_content']); break;
				case 'text' : $this->Input('textarea', 'artcl_article_content', 'content', $content['id_article_content'], $content["content"], Array(),$content["id_owner"]);break; 				
			} ?>
		</div>
	<?php } ?>
	<?php $this->loadView('article_toolbox', Array('id_article'=>$view['id_article']));?>
	
</div>
