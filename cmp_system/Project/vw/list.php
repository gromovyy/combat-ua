	<div>
		<?php if (!empty($actual_projects['rows'])) { ?>
		<div class="row">
			<div class="actual clearfix">
					<h2 class="text-center">НАЙБІЛЬШ АКТУАЛЬНІ ПРОЕКТИ</h2>
					<?php foreach ($actual_projects['rows'] as $project) { ?>
					<div class="thumbnail project <?php echo $project['state'];?> col-xs-12 col-md-6">
						<a href="project/<?php echo $project['id_project'];?>">
							<h3 class="text-center"><?php  $this->Input('text', 'prjct_project', 'name', $project['id_project'], $project["name"], Array(),$project["id_owner"] );?> </h3>
							<?php echo $this->Photo->e_View($project['id_photo']);?>
							<!--<img src="/combat-ua/img/section/project/teplovisor.png" alt=""> -->
							<p class="description"><?php echo $project['description'];?>
							</p>
						</a>
						<div class="polosa">
							<div class="progress">
								<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $project['current_percent'];?>" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">
								</div>
							</div>
							<small><?php echo $project['current_percent'];?>% зібрано</small>
						</div>
						<div class="summa">
							<span><?php  $this->Input('text', 'prjct_project', 'budget', $project['id_project'], $project["budget"], Array(),$project["id_owner"] );?> грн</span>
							<small>потрібно</small>
						</div>	
						<p><a href="donate/<?php echo $project['id_project'];?>" class="btn btn-primary" role="button">Підтримати</a></p>
					</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
		<?php if (!empty($projects['rows'])) { ?>
		<div class="row">
				<div class="normal clearfix">
					<?php foreach ($projects['rows'] as $project) { ?>
					<div class="thumbnail project <?php echo $project['state'];?> col-xs-12 col-md-6">
						<a href="project/<?php echo $project['id_project'];?>">
							<h3 class="text-center"><?php  $this->Input('text', 'prjct_project', 'name', $project['id_project'], $project["name"], Array(),$project["id_owner"] );?> </h3>
							<?php echo $this->Photo->e_View($project['id_photo']);?>
							<!--<img src="/combat-ua/img/section/project/teplovisor.png" alt=""> -->
							<p class="description"><?php echo $project['description'];?>
							</p>
						</a>
						<div class="polosa">
							<div class="progress">
								<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $project['current_percent'];?>" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">
								</div>
							</div>
							<small><?php echo $project['current_percent'];?>% зібрано</small>
						</div>
						<div class="summa">
							<span><?php  $this->Input('text', 'prjct_project', 'budget', $project['id_project'], $project["budget"], Array(),$project["id_owner"] );?> грн</span>
							<small>потрібно</small>
						</div>	
						<p><a href="donate/<?php echo $project['id_project'];?>" class="btn btn-primary" role="button">Підтримати</a></p>
					</div>
					<?php } ?>
				</div>
		</div>
		<?php } ?>
</div>