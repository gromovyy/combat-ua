<div class="container-fluid">
		<div class="row">
			
			<div class="col-xs-3 navigation">
				<?php $this->Tracker->loadView('left_menu', $data); ?>
			</div>
			<div class="col-xs-9">
				<div class="loggedin">Вы вошли как <a href="<?php// echo $this->Member->href();?>"><?php echo $this->Member->getName();?></a><a href="login"><i ="glyphicon glyphicon-log-out"></i></a>
				</div>
				<?php if ($id_user != $this->User->getId()) {?>
				<div id="user-name">Cтраничка пользователя <a><?php echo $this->Member->getName($id_user);?></a></div>
				<?php } ?>
				<h3>Проект : <?php $this->Input('text', 'prjct_project', 'name', $id_project, $project["name"], Array(),$project["id_owner"] );?></h3>
				<?php $this->Project->e_ProjectAccess($id_project);?>
				<?php $this->Task->e_ProjectList($id_project, false, $id_user); ?>
			</div>
		</div>
</div>
<?php $this->Tracker->e_AddComment();?>
<?php $this->Task->e_ModalForm(NULL, $id_project, $id_user); ?>