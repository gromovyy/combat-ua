<?php
class Project extends Contenter {

	// Выводит страничку проекта
	public function e_View($id_project){
	
	}

	// Выводит список проектов с фильтром по типу
	public function e_List($project_type='all'){
		$data['projects'] = $this->Model->getProjects();
		if (empty($data['projects'])) 
			$data['projects'] = array();
			
		$data['id_user'] = $this->User->getId();
		$data['id_project_active'] = $id_project_active;
		$data['combo_members'] = $this->Member->combo_user_list();
		$this->loadView("short_list", $data);
	}
	
	// Возвращает список типов проектов
	public function get_combo_project_types(){
		return array();
	}
	
	// Возвращает массив проектов для комбобокса
	public function get_combo_projects($id_user = NULL) {
		$projects = $this->Model->getProjects($id_user);
		return $this->get_combo($projects['rows'], 'id_project', 'name');
	}
}
?>