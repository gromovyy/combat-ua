<?php 
	$view_update_link = 'Project/List';
?>
<div>
	<div class="list-group collapse <?php if ($this->User->getRole() != 'administrator') echo 'in';?>" id="projects">
		<a class="list-group-item <?php if (empty($id_project_active)) echo 'active';?>" href="">
				Все проекты 
				<span class="delimiter"></span> 
		</a>
		<?php foreach ($projects['rows'] as $project) { //print_r($member); ?>
			<a class="list-group-item <?php if ($project['id_project'] == $id_project_active) echo 'active';?>" href="project/<?php echo $project['id_project']; ?>">
				<?php $this->loadToolBox("project",$project["id_project"],null,null,null,array('delete'), $view_update_link) ?>
				<?php $this->Input('text', 'prjct_project', 'name', $project['id_project'], $project["name"], Array(),$project["id_owner"] );?> 
				<span class="delimiter"></span> 
				<!--<span class="project-owner">
					<?php $this->Input('combobox', 'prjct_project', 'id_owner', $project['id_project'], $project["id_owner"], Array('valueList'=>$combo_members),$project["id_owner"] );?>
				</span> -->
			</a>
		<?php  } ?>
	</div>
</div>