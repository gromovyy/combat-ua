<?php

?>

<div class="container-fluid">
		<div class="row">
			<div class="col-xs-3 navigation">
				<?php $this->loadView('left_menu', $data); ?>
			</div>
			<div class="col-xs-9">
				<div class="row top-row">
					<div class="col-xs-3">
						<?php if (empty($id_project)) { ?>
							Проекти : <b>всі</b>
						<?php } else { ?>
							Проект : <b><?php echo $project_name; ?></b>
							<a href=""><i class="glyphicon glyphicon-log-out" style="color:black;"></i></a>
						<?php } ?>
					</div>
					<div class="col-xs-5">
						<?php if ($this->User->is_user_changed()) {?>
						<span>Користувач : <a><?php echo $this->Member->getName($this->User->getId());?></a>
						<a onclick="reload('User/RestoreRole');"><i class="glyphicon glyphicon-log-out"></i></a></span>
				<?php } ?>
					</div>
					<div class="col-xs-4 right">
						Ви зайшли як <a href="<?php// echo $this->Member->href();?>"><?php echo $this->Member->getName($this->User->getRealId());?> </a><a onclick="return exit()"><i class="glyphicon glyphicon-log-out"></i></a>
					</div>
				</div>
				<?php if (!empty($id_project) and $this->User->getRole() == 'administrator') { ?>
				<div class="row">
					<div class="col-xs-12">
						<?php $this->Project->e_ProjectAccess($id_project);?>
					</div>
				</div>
				<?php } ?>
				<div class="tabs <?php echo ($this->User->is_role_changed())?'changed-role':''; ?>">
					<ul class="nav nav-tabs restore-active" role="tablist" id="restore-tab">
						<li class="active" id='tab-all'><a href="#issues" role="tab" data-toggle="tab">Всі задачі</a>
						<?php if (!empty($statistics['all'])){?><div class="numberCircle"><?php echo $statistics['all'];?></div><?php } else {?><div></div><?php } ?>
						</li>
						<li id='tab-worker'><a href="#control" role="tab" data-toggle="tab">Мої задачі</a>
						<?php if (!empty($statistics['worker'])){?><div class="numberCircle"><?php echo $statistics['worker'];?></div><?php } else {?><div></div><?php } ?>
						</li>
						<li id='tab-controller'><a href="#projs" role="tab" data-toggle="tab">Контроль</a><div></div>
						<?php if (!empty($statistics['controller'])){?><div class="numberCircle"><?php echo $statistics['controller'];?></div><?php } else {?><div></div><?php } ?>
						</li>
						<li><a href="#time" role="tab" data-toggle="tab">Час</a></li>
						<?php if ($this->User->getRealRole() == "administrator") { ?>
							<li><a href="#profile" role="tab" data-toggle="tab">Профіль</a></li>
						<?php } ?>
						
						<!--<li><a href="#analytics" role="tab" data-toggle="tab">Отчет</a>
						</li> -->
					</ul>
					<div class="tab-content">
						<div class="tab-pane fade in active" id="issues">
							<?php $this->Task->e_FullList($id_project); ?>
						</div>
						<!-- /#issues -->
						<div class="tab-pane fade" id="control">
							<?php $this->Task->e_UserList($id_project); ?>		
						</div>
							<!-- /#control -->
						<div class="tab-pane fade" id="projs">
							<?php $this->Task->e_ControlList($id_project); ?>
						</div>
								<!-- /#projs -->
						<div class="tab-pane fade" id="time">
							<?php $this->Task->e_TimeList($id_project);?>
						</div>
						<!-- 
						<div class="tab-pane fade" id="analytics">
							<?php // $this->Task->e_ProjectTimeList($id_project);?>
						</div>
						-->
						<?php if ($this->User->getRealRole() == "administrator") { ?>
							<div class="tab-pane fade" id="profile">
								<?php $this->Member->e_Profile();?>
							</div>
						<?php } ?>
					</div>
					<!-- /.tab-content -->
				</div>
				<!-- /.tabs -->
			</div>
		</div>
	</div>
	<input type="hidden" id="id_proejct" value = "<?php echo $id_project;?>"/> 
	<?php $this->Tracker->e_AddComment();?>
	<?php $this->Task->e_ModalForm(NULL, $id_project); ?>
		<?php // $this->e_WorkTracker(); ?>