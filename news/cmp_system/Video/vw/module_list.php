<?php 
	$view_update_link = 'Video/ModuleList/'.$id_module;
?>
<div>
	<h4>Відео
		<?php $this->loadToolBoxBind("Module", "module", $id_module, Array("add"), $view_update_link);?>
	</h4>
	<ol class="statti">	
		<?php foreach($video_list as $video) { ?>
		<li>
			<a href="<?php $this->Module->moduleLink($id_module, 'теорія-відео', $video['id_video_bind']);?>">
				<?php $this->Input('text', 'vd_video', 'title', $video['id_video'], $video["title"], Array(),$video["id_video_bind"] );	?>
			</a>
			<?php 
				$visible = $this->getVisibility($video["is_visible_bind"]);
				$this->loadToolBoxBind("Module", "module", $id_module, Array('up', 'down', $visible, 'delete'), $view_update_link, $video['id_video_bind']);
			?>
		</li>
		<?php } ?>
	</ol>
</div>