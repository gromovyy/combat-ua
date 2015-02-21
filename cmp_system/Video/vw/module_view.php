<div class='lesson-video clearfix'>
	<h4>
		<?php $this->Input('text', 'vd_video', 'title', $video['id_video_bind'], $video["title"], Array(),$video["id_video_bind"] );	?>
	</h4>
	<?php $this->DirectView($video); ?>
	<br />
</div>