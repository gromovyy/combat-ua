<div class="row upper">
	<div class="col-xs-12 col-md-7">
		<div class="col-xs-12 image">
			<img src="img/section/project/tank.png" alt="">
		</div>
		<div class="col-xs-12 h">
		<!--<h2 class="text-center">Назва проекту</h2> -->
		</div>
		<div class="col-xs-12 col-md-6 image">
			<img src="img/section/project/tank.png" alt="">
		</div>
		<div class="col-xs-12 col-md-6 image">
		<img src="img/section/project/tank.png" alt="">
		</div>
	</div>
	<div class="col-xs-12 col-md-4">
		<h3><?php  $this->Input('text', 'prjct_project', 'name', $project['id_project'], $project["name"], Array(),$project["id_owner"] );?> </h3>
		<h4>51 осіб</h4>
		<small>вже зробили внесок</small><br>
		<h4>27468 грн</h4>
		<small>з <?php  $this->Input('text', 'prjct_project', 'budget', $project['id_project'], $project["budget"], Array(),$project["id_owner"] );?> грн необхідних</small><br>
		<h4><?php echo $project['current_percent'];?>% зібрано</h4>
		<small>оновлено 09.02.2015 о 17:26</small><br>
		<div class="progress">
			<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $project['current_percent'];?>" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"><?php echo $project['current_percent'];?>%
			</div>
		</div>
		<p class="clearfix"><a href="donate/<?php echo $project['id_project'];?>" class="btn btn-primary" role="button">Зробити внесок</a></p>
		<div class="share">
			
		</div>
	</div>
</div>