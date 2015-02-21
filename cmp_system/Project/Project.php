<?php
class Project extends Contenter {
	
	public function e_List($id_project_active = NULL) {
		$data['projects'] = $this->Model->getProjects();
		if (empty($data['projects'])) 
			$data['projects'] = array();
			
		$data['id_user'] = $this->User->getId();
		$data['id_project_active'] = $id_project_active;
		$data['combo_members'] = $this->Member->combo_user_list();
		$this->loadView("short_list", $data);
	}
	
	public function e_ProjectAccess($id_project) {
		if ($this->User->getRole() != 'administrator') return;
		$data['project_access'] = $this->Model->getProjectAccess($id_project);
		$data['id_project'] = $id_project;
		$data['combo_members'] = $this->Member->combo_user_list();
		$this->loadView("project_access", $data);
	}
	
	public function get_combo_projects($id_user = NULL) {
		$projects = $this->Model->getProjects($id_user);
		return $this->get_combo($projects['rows'], 'id_project', 'name');
	}
}
?>