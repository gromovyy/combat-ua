<?php
class Project extends Contenter {

	// Выводит страничку проекта
	public function e_View($id_project){
		$data['project'] = $this->get_row($id_project);
		$this->loadView("project", $data);
	}

	// Выводит список проектов с фильтром по типу
	public function e_List($id_project_category = NULL){
		
		$data['actual_projects'] = $this->Model->getProjects($id_project_category, 'actual');
		$data['projects'] = $this->Model->getProjects($id_project_category, 'normal');
		
		// $data['id_user'] = $this->User->getId();
		// $data['id_project_active'] = $id_project_active;
		// $data['combo_members'] = $this->Member->combo_user_list();
		$this->loadView("list", $data);
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