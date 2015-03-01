<?php 
	class ProjectModel extends DBProc {
	
	public function getProjects($id_project_category = NULL, $project_actual = NULL) {
		//if (empty($id_user)) $id_user = $this->User->getId();
		//echo 'Task count'.$this->limit_count;
		//if ($this->User->getRole() == 'administrator') {
		if (!empty($id_project_category)) {
			$params = array ( 'tables' => array( 'p' => 'prjct_project',
												array('pc' => 'prjct_category',
												  'join' => 'inner',
												  'on_left' => 'p.id_project_category',
												  'on_right' => 'pc.id_prjct_category'
												 )),
							  'fields' => array('p.*'),
							  'order' => array( 'order'=> 'ASC')
							);
				if (empty($project_actual))
					$params ['where'] = array( 
									'field'=>'p.id_project_category',
									'operator' => '=',
									'value' => $id_project_category);
				else {
					if ($project_actual == 'actual')
						$params ['where'] = array(array(	
													'field'=>'p.id_project_category',
													'operator' => '=',
													'value' => $id_project_category),
												'operator' => 'AND',
												array(	'field'=>'p.is_actual',
														'operator' => '=',
														'value' => '1')
												);
					else
						$params ['where'] = array(array(	
													'field'=>'p.id_project_category',
													'operator' => '=',
													'value' => $id_project_category),
												'operator' => 'AND',
												array(	'field'=>'p.is_actual',
														'operator' => '=',
														'value' => '0')
												);
				}
			}
		else	{
				$params = array ( 'tables' => array('p' => 'prjct_project'),
							  'fields' => array('p.*'),
							  'order' => array( 'order'=> 'ASC')
							);
							
				if (!empty($project_actual)){
					if ($project_actual == 'actual') 
						$params ['where'] = array(	'field'=>'p.is_actual',
														'operator' => '=',
														'value' => '1');
					else 
						$params ['where'] = array(	'field'=>'p.is_actual',
														'operator' => '<>',
														'value' => '1');
				}
		}
				
			// } else {
			// $params = array ( 'tables' => array( 'p' => 'prjct_project',
												// array('pu' => 'prjct_project_user',
												  // 'join' => 'inner',
												  // 'on_left' => 'p.id_project',
												  // 'on_right' => 'pu.id_project'
												 // )),
								// 'fields' => array('p.*'),
								// 'where' => array( 
									// 'field'=>'pu.id_user',
									// 'operator' => '=',
									// 'value' => $id_user),
								// 'order' => array( 'name'=> 'ASC')
						// );
			// }
	//print_r($params);
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