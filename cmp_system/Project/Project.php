<?php
class Project extends Contenter {

	// Выводит страничку проекта
	public function e_View($id_project){
		$data['project'] = $this->get_row($id_project);
		$this->loadView("project", $data);
	}

	// Выводит список проектов с фильтром по типу
	public function e_List($id_project_category = NULL){
	
		$data['id_project_category'] = $id_project_category;
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
	
		
	// Добавление нового проекта
	public function e_Insert($id_project_category = NULL) {
		$id_row = $this->create_row('prjct_project');
		// Создаем фотографию, связанную с проектом
		$id_photo  = $this->Photo->e_Insert('Project',$id_row);
		// Привязываем её к проекту
		$this->set_cell("prjct_project","id_photo", $id_row, $id_photo);
		// Если задано имя категории - добавляем запись
		if (!empty($id_project_category))
				$this->set_cell("prjct_project","id_project_category", $id_row, $id_project_category);
		return $id_row;
	}
}
?>