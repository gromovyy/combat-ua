<div class="clearfix">

<div class="page-header">
  <h4>
		<?php $this->Input('text', 'artcl_article', 'title', $view['id_article'], $view["title"], Array(),$view["id_owner"] );?> 
		<small class="pull-right"><?php $this->Input('date', 'artcl_article', 'create_date', $view['id_article'], substr($view["create_date"],0,11), Array(),$view["id_owner"] );?></small>
	</h4>
</div>
	<div>
		<?php $this->Input('textarea', 'artcl_article', 'content', $view['id_article'], $view["content"], Array(),$view["id_owner"]);?> 
	</div>
</div>
