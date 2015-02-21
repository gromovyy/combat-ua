<?php 
	$view_update_link = 'Article/StudentList/'.$id_module_result; 
	if (!empty($article_list)) {
?>
<div class="list">
	<h4>Статті	</h4>
	<ol class="statti">	
		<?php foreach($article_list as $article) 
				if ($article['is_visible_bind']) {?>
					<li>
						<a href="<?php $this->Module->studentLink($id_module_result, 'теорія-стаття',$article['id_article_bind']);?>"><?php echo $article['title'];?></a>
					</li>
		<?php } ?>
	</ol>
</div>
<?php } ?>