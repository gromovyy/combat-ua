<?php 
	$view_update_link = 'Article/ModuleList/'.$id_module;
?>
<div class="list">
	<h4>Статті
		<?php $this->loadToolBoxBind("Module", "module", $id_module, Array("add"), $view_update_link);?>
	</h4>
	<ol class="statti">	
		<?php foreach($article_list as $article) { ?>
		<li>
			<a href="<?php echo $main_page.'/'.$id_module.'/теорія-стаття/'.$article['id_article_bind'];?>"><?php echo $article['title'];?></a>
			<?php 
				$visible = $this->getVisibility($article["is_visible_bind"]);
				$this->loadToolBoxBind("Module", "module", $id_module, Array('up', 'down', $visible, 'delete'), $view_update_link, $article['id_article_bind']);
			?>
		</li>
		<?php } ?>
	</ol>
</div>