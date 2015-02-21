<?php 
class Video extends Binder {

	function e_SetVideo() {			
// 	Прописывание пути загрузки файлов-изображений
//	global $GL_PHOTO_FOLDER;
//	$location = $GL_PHOTO_FOLDER;
	if (preg_match('/[?&]v=([-_a-z0-9]{11})/i', $_POST["url_video"], $result))
	$url_video = $result[1];

	// Получение входных параметров при загрузке
	if (isset($_POST["video_id"]))	{ 
		//Проверка на допустимость изменения видео
//		if ($this->is_update( $this->get_owner($_POST["video_id"]), $_POST["video_id"] )) {
			$video_bind = $this->get_row($_POST["video_id"],'vd_video_bind');
			if (!empty($_POST["title_video"])) $this->set_cell("vd_video", "title", $video_bind['id_video'], $_POST["title_video"]);
			if (isset($_POST["url_video"])) $this->set_cell("vd_video", "url_video", $video_bind['id_video'], $url_video);
			// Обновляем состояние связанных объектов
			$this->updateBindState($_POST["video_id"]);
//		}
	}
	
	// Возвращение на предыдущую страничку.
	header("Location:".$_SERVER['HTTP_REFERER']);
	exit;		
//	die();
	}
		
	function e_Delete($id) {
	//	if (!$this->is_delete( $this->get_owner($id), $id)) return false;
		$this->delete_row("vd_video_bind",$id);
		return true;
	}
	
	// Удаление всех видео-ссылок, которые связаны с объектом id_object компонента component
	function DeleteObject($component, $id_object) {
		$this->DBProc->delete_object($component, $id_object);
	}
	
	
	function e_List($component, $id_object, $object='', $title = "ВІДЕОГАЛЕРЕЯ"){
		//Загружаем данные из Базы данных
		$data["video_list"] = $this->DBProc->list($component, $id_object);
		$data["component"] = $component;
		$data["id_object"] = $id_object;
		$data["object"] = $object;
		$data["title"] = $title;
		$this->loadView("list",$data);
	}
	
	function e_ModuleList($id_module){
		if (!$this->is_select()) return;
		$data['video_list'] = $this->getBindList('Module', 'module', $id_module);
		//print_r($data['video_list']);
		$data['id_module'] = $id_module;
		$this->loadView('module_list',$data);
	}
	
	function e_StudentList($id_module_result){
		if (!$this->is_select()) return;
		$module_result = $this->get_row($id_module_result, 'mdl_module_result');
		$data['video_list'] = $this->getBindList('Module', 'module', $module_result['id_module']);
		//print_r($data['video_list']);
		$data['id_module_result'] = $id_module_result;
		$this->loadView('student_list',$data);
	}
	
	function e_View($id_video){
		if (!$this->is_select()) return;
		
		//Загружаем данные из Базы данных
		$video = $this->getBindView($id_video);
		//print_r($video) ;
		$this->DirectView($video);
	}	
	
	function DirectView($video){
		if (!$this->is_select()) return;
		//Загружаем данные из Базы данных
		$data["video"] = $video;
		$this->loadView("view", $data);
	}	
	
	function e_ModuleView($id_video){
		if (!$this->is_select()) return;
		
		//Загружаем данные из Базы данных
		$data["video"] = $this->getBindView($id_video);
		//print_r($id_video) ;
		$this->loadView("module_view", $data);
	}	
	
	function e_UploadDlg($id_video){
		$data['id_video'] = $id_video;
		$this->loadView('upload_dlg', $data);
	}
	
	// Переопределяем метод родительского класса.
	function e_InsertBind($component,$id_object,$object="") {
		$id_bind = parent::e_InsertBind($component,$id_object,$object); 
		if (!empty($id_bind)) {
			// Создаем пустую статью и связываем её с созданной связью
			$id_video = $this->create_row("vd_video");
			$this->e_Bind($id_bind, $id_video);
		}
		return $id_bind;
	}
	
	// Перевіряє, чи є відео, чи лише пустий зв'язок
	function is_empty($id_video){
		$video = $this->getBindView($id_video);
		if (empty($video['url_video'])) return true;
		return false;
	}
	
}
?>