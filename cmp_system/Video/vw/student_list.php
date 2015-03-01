<?php if (!empty($video_list)) { ?>
<div>
	<h4>Відео</h4>
	<ol class="statti">	
		<?php foreach($video_list as $video) 
					if ($video['is_visible_bind']) { ?>
						<li>
							<a href="<?php $this->Module->studentLink($id_module_result, 'теорія-відео', $video['id_video_bind']);?>">
								<?php echo $video["title"];	?>
							</a>
						</li>
		<?php } ?>
	</ol>
</div>
<?php } ?>
