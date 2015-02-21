<?php 
	$view_update_link = 'Project/ProjectAccess/'.$id_project;
	
?>
<div class="project-access">	
	<div> Доступы : 
	<?php $this->loadToolBox("project_user",null,'project',null,$id_project,array('add'), $view_update_link) ?>
	</div>
	<?php foreach ($project_access['rows'] as $access) {  ?>
		<div> 
		<?php $this->Input('combobox', 'prjct_project_user', 'id_user', $access['id_project_user'], $access["id_user"], Array('valueList'=>$combo_members),$access["id_owner"] );?>
		<?php $this->loadToolBox("project_user",$access['id_project_user'],null,null,null,array('delete'), $view_update_link) ?>
		</div>
		<?php  } ?>
</div>