<?php 
	class ProjectModel extends DBProc {
	
	public function getProjects($id_user = NULL) {
		if (empty($id_user)) $id_user = $this->User->getId();
		//echo 'Task count'.$this->limit_count;
		if ($this->User->getRole() == 'administrator') {
		$params = array ( 'tables' => 'prjct_project',
						  'fields' => array('*'),
						  'order' => array( 'name'=> 'ASC')
						);
		} else {
			$params = array ( 'tables' => array( 'p' => 'prjct_project',
												array('pu' => 'prjct_project_user',
												  'join' => 'inner',
												  'on_left' => 'p.id_project',
												  'on_right' => 'pu.id_project'
												 )),
								'fields' => array('p.*'),
								'where' => array( 
									'field'=>'pu.id_user',
									'operator' => '=',
									'value' => $id_user),
								'order' => array( 'name'=> 'ASC')
						);
			}
	return $this->Select($params);
	}
	
	public function getProjectAccess($id_project) {
		$params = array ( 'tables' => 'prjct_project_user',
						  'fields' => array('*'),
						  'where' => array( 
											'field'=>'id_project',
											'operator' => '=',
											'value' => $id_project)
						);
		return $this->Select($params);
	}

}

?>