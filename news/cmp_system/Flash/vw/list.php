<?php 
	$view_update_link = "Video/List/$component/$id_object";
	if (empty($video_list) and ($this->User->getMode()=="view")) return;
?>
<div class="main">	
		<div id="videoReport">
			<span class="header-table1"><?php echo $title;?></span>
			<?php if ($this->is_insert()) { ?>
					<div class="btnAdd" onclick="exec('Video/Insert/<?php echo $component."/".$id_object."/".$object;?>','<?php echo $view_update_link;?>')"></div>
			<?php }	?>
			<div style="clear:both;"></div>
			<div id="videos">		
				<div class="arowLeft" onclick="MoveRight('videos', 828, <?php echo count($video_list)*207;?>)"></div>
				<div class="wrapper">
					<div class="slider">
				<?php 
						foreach ($video_list as $video) { 
							$this->loadView('view',array("video"=> $video,"parent_update_link"=>$view_update_link));
						}
				?>	
					</div>
				</div>
				<div class="arowRight" onclick="MoveLeft('videos', 828, <?php echo count($video_list)*207;?>);"></div>
			</div>		
		</div>	
</div>	