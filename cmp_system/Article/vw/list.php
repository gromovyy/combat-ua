<?php 	
	$view_update_link = "Article/List/$component/$id_object";
?>
<div>
	<div class="container-head-small">СТАТТІ

		<?php $this->loadToolBoxBind($component, $object, $id_object, Array('add'), $view_update_link);?>
	</div>
	<br />
	<?php 	foreach ( $view as $row) { ?>
		<div class="article-short">

		<?php 
				$visible = $this->getVisibility($row["is_visible_bind"]);
				$this->loadToolBoxBind($component, $object, $id_object, Array('up','down',$visible,'delete'), $view_update_link, $row["id_article_bind"]);
		?>
					<div class="article-text">
					<?php if (!empty($row["url_photo"])) { ?>
						<a href="<?php echo "Стаття/".$row["id_article_bind"]."/".$this->getUrlEncoded($row["title"]);?>">
							<img alt="<?php echo $row["title_photo"];?>" src="<?php echo $row["default_folder"]."small/".$row["url_photo"];?>">
						</a>
					<?php } ?>
						<b>
					<?php $this->Input('date', 'artcl_article', 'create_date', $row['id_article_bind'], substr($row["create_date"],0,11));?> | 
							<a href="<?php echo "Стаття/".$row["id_article_bind"]."/".$this->getUrlEncoded($row["title"]);?>"><?php echo $row["title"];?></a>
						</b>
						<br />
						<?php echo $this->cutText($row["content"],true);?>
					</div>
		</div>		
		<div class="article-line"></div>
	<?php } ?>
</div>

