<?php 
	class Article extends Binder {
	
		function e_ViewAuthorContent($id_article) {

			if (!$this->is_select()) return;
			$data["combo_user_list"] = $this->Member->combo_user_list();
			$data['view'] = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_article);			
			$this->loadView('view_author_content',$data);
		}
		
		function e_View($id_article, $type="news") {
			
			if (!$this->is_select()) return;
			$data['view'] = $this->getBindView($id_article);
		
			$data['article_content'] = $this->getList('article_content','Article','article', $id_article);
			$this->loadView('view',$data);
		}
		
		function e_StudentView($id_article, $type="news") {
			
			if (!$this->is_select()) return;
			$data['view'] = $this->getBindView($id_article);
		
			$data['article_content'] = $this->getList('article_content','Article','article', $id_article);
			$this->loadView('student_view',$data);
		}
		
		function e_List($component, $id_object, $object='') {
			if (!$this->is_select()) return;
			// проверяем доступ
			//$this->User->checkPageAccess('Article', 'View');
			// получаем список мастеров
			//if ($id_object =='news')
			$data['view'] = $this->getBindList($component, $object, $id_object);
			$data['component'] = $component;
			$data['id_object'] = $id_object;
			$data['object'] = $object;
			$this->loadView('list',$data);
		}
		
		function e_ModuleList($id_module, $main_page='Зміст-модуля'){
			if (!$this->is_select()) return;
			$data['article_list'] = $this->getBindList('Module', 'module', $id_module);
			//print_r($data['article_list']);
			$data['id_module'] = $id_module;
			$data['main_page'] = $main_page;
			$data['id_user'] = $this->User->getId();
			$this->loadView('module_list',$data);
		}

		function e_StudentList($id_module_result, $id_module){
			if (!$this->is_select()) return;
			$module_result = $this->get_row($id_module_result, 'mdl_module_result');
			$data['article_list'] = $this->getBindList('Module', 'module', $module_result['id_module']);
			//print_r($data['article_list']);
			$data['id_module_result'] = $id_module_result;
			$this->loadView('student_list',$data);
		}
		
		// Переопределяем метод родительского класса.
		function e_InsertBind($component,$id_object,$object="") {
			$this->is_insert();
			$id_bind = parent::e_InsertBind($component,$id_object,$object); 
			if (!empty($id_bind)) {
				// Создаем пустую статью и связываем её с созданной связью
				$id_article = $this->create_row("artcl_article");
				$this->e_Bind($id_bind, $id_article);
			}
			return $id_bind;
		}
		
		// Функция, формирующая ссылку на этот объект.
		function href($id_article, $name=null) {
			if (empty($name)) {
				$article = $this->get_row($id_article);
				$name = $article["title"];				
			}
			return "Стаття/$id_article/".$this->getUrlEncoded($name);
		}
		
	// После удаления статьи выполняем необходимые действия
	public function BeforeDelete($table, $row_id){
		// Если удаляется задание, удаляем к нему вопрос
		switch ($table) {
			// Если удаляется статья - удаляем также контент 
			case 'artcl_article_content':
				$content = $this->get_row($row_id, $table);
				switch ($content['content_type']) {
						case 'photo': $this->Photo->e_DeleteBind($content['id_content']); break;
						case 'video': $this->Video->e_DeleteBind($content['id_content']); break;
						case 'flash': $this->Flash->e_DeleteBind($content['id_content']); break;
					}
				break;
			case 'artcl_article':
				$article_content = $this->getList('article_content','Article','article', $row_id);
				foreach ($article_content as $content) {
					switch ($content['content_type']) {
						case 'photo': $this->Photo->e_DeleteBind($content['id_content']); break;
						case 'video': $this->Video->e_DeleteBind($content['id_content']); break;
						case 'flash': $this->Flash->e_DeleteBind($content['id_content']); break;
					}
					$this->delete_row('article_content', $content['id_article_content']);
				}
				break;
		}
	}
	
	// Додавання текстової відповіді до статті
	public function e_InsertTextContent($id_article){
		$id_row = $this->e_Insert("article_content", "Article", $id_article, "article");
		$this->set_cell("artcl_article_content","content_type", $id_row, "text");
		return $id_row;
	}
	
	// Додавання фотографії до статті
	public function e_InsertPhotoContent($id_article){
		$id_row = $this->e_Insert("article_content", "Article", $id_article, "article");
		$this->set_cell("artcl_article_content","content_type", $id_row, "photo");
		$id_photo = $this->Photo->e_InsertBind("Article",$id_row, "article_content", "task");
		$this->set_cell("artcl_article_content","id_content", $id_row, $id_photo);
		return $id_row;
	}
	
	// Додавання відео до статті
	public function e_InsertVideoContent($id_article){
		$id_row = $this->e_Insert("article_content", "Article", $id_article, "article");
		$this->set_cell("artcl_article_content","content_type", $id_row, "video");
		$id_video = $this->Video->e_InsertBind("Article",$id_row, "article_content");
		$this->set_cell("artcl_article_content","id_content", $id_row, $id_video);
		return $id_row;
	}
	
	// Додавання флеш-ролику до статті
	public function e_InsertFlashContent($id_article){
		$id_row = $this->e_Insert("article_content", "Article", $id_article, "article");
		$this->set_cell("artcl_article_content","content_type", $id_row, "flash");
		$id_flash = $this->Flash->e_InsertBind("Article",$id_row, "article_content");
		$this->set_cell("artcl_article_content","id_content", $id_row, $id_flash);
		return $id_row;
	}	
}
?>