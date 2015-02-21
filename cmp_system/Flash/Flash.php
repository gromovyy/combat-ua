<?php 
class Flash extends Binder {

	/*
	*	Функция загрузки файла 
	*/
	function e_Upload() {

	// Если пользователь может изменять фотографию, тогда загружаем фотографию.
	//if (! $this->is_update(null, $_POST["id_flash"])) return;
	
	// Прописывание пути загрузки файлов-изображений
	//global $GL_FLASH_FOLDER;
	//global $GL_SITE_DIR;
	//$location = $_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR$GL_ATTACHMENT_FOLDER";
	$location = pathinfo($_SERVER['SCRIPT_FILENAME'],PATHINFO_DIRNAME)."/files/flash";
	
	// Получение входных параметров при загрузке
	//print_r($_FILES);
	
	if (isset($_FILES["flash_swf"]["tmp_name"])) 
		$destination_swf_tmp = $_FILES["flash_swf"]["tmp_name"];
	if (isset($_FILES["flash_swf"]["name"])) { 
		$flash_swf_name = pathinfo($_FILES["flash_swf"]["name"], PATHINFO_FILENAME);
		$swf_ext = pathinfo(strtolower($_FILES["flash_swf"]["name"]), PATHINFO_EXTENSION);
	}
	
	if (isset($_FILES["flash_fla"]["tmp_name"])) 
		$destination_fla_tmp = $_FILES["flash_fla"]["tmp_name"];
	if (isset($_FILES["flash_fla"]["name"])) { 
		$flash_fla_name = pathinfo($_FILES["flash_fla"]["name"], PATHINFO_FILENAME);
		$fla_ext = pathinfo(strtolower($_FILES["flash_fla"]["name"]), PATHINFO_EXTENSION);
	}
	
	if (isset($_POST["id_flash"])) $id_flash = $_POST["id_flash"];

	// Получаем собственника файла
	$member = $this->Member->getPerson($this->get_owner($id_flash, 'flsh_flash_bind'));
	
	// Проверка на допустимые расширения
	// Установка имени файла в виде фамилия_имя_id.расширение
	
	$swf_name = strtolower($this->translit($flash_swf_name).'-'.$id_flash.".".$swf_ext);
	$fla_name = strtolower($this->translit($flash_fla_name).'-'.$id_flash.".".$fla_ext);
	
	//echo $id_flash.$swf_ext.$fla_ext.$swf_name.$fla_name;
	
	// Если есть хотя-бы один файл - загружаем его и создаем новую запись
	if ((!empty($id_flash)) and (($swf_ext=="swf") or ($fla_ext == "fla"))) {
		
		// Создаем новую запись и связываем её с записью в flsh_flash_bind
		$id_row = $this->create_row("flsh_flash");
		$this->set_cell("flsh_flash_bind","id_flash",$id_flash, $id_row);
		
		// Проверяем, существует ли папка с идентификатором пользователя, куда мы будем загружать его файлы. 
		// Если не существует, создаем.
		$dir = $this->User->getId();

		if (!file_exists($location."/".$dir)) {
			mkdir($location."/".$dir, 0777, true);
			}
		// Устанавливаем папку хранения
		// $this->set_cell("flsh_flash","dir", $id_row, $dir);
		// Если файл swf указан, загружаем его
		if ($swf_ext=="swf") 
			{
			// Устанавливаем путь, куда будут сохраняться файлы. Файлы сохраняются в папку с текущей датой
			$destination_swf = $location."/".$dir."/".$swf_name;
			if (move_uploaded_file($destination_swf_tmp, $destination_swf)) 
				$this->set_cell("flsh_flash","url_swf", $id_row, $swf_name);
			else 
				echo "<br>Файл swf не загрузился";
			}
					// Если файл swf указан, загружаем его
		if ($fla_ext=="fla") 
			{
			// Устанавливаем путь, куда будут сохраняться файлы. Файлы сохраняются в папку с текущей датой
			$destination_fla = $location."/".$dir."/".$fla_name;
			if (move_uploaded_file($destination_fla_tmp, $destination_fla)) 
				$this->set_cell("flsh_flash","url_fla", $id_row, $fla_name);
			else 
				echo "<br>Файл fla не загрузился";
			}
		// Обновляем состояние связанных объектов
		$this->updateBindState($id_flash);
			
	}
		ob_get_clean();
		// Возвращение на предыдущую страничку.
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;		
	}

	
/*	function e_Upload() {			
// 	Прописывание пути загрузки файлов-изображений
//	global $GL_PHOTO_FOLDER;
//	$location = $GL_PHOTO_FOLDER;
//	if ($_POST["url_fla"], $result))
//	$url_video = $result[1];

	// Получение входных параметров при загрузке
	if (isset($_POST["flash_id"]))	{ 
		//Проверка на допустимость изменения видео
		if ($this->is_update( $this->get_owner($_POST["flash_id"]), $_POST["flash_id"] )) {
			$id_row = $this->create_row("flsh_flash");
			if (isset($_POST["title"])) $this->set_cell("flsh_flash", "title", $id_row, $_POST["title"]);
			if (isset($_POST["url_fla"])) $this->set_cell("flsh_flash", "url_fla", $id_row, $_POST["url_fla"]);
			if (isset($_POST["url_swf"])) $this->set_cell("flsh_flash", "url_swf", $id_row, $_POST["url_swf"]);
			$this->set_cell("flsh_flash_bind", "id_flash", $_POST["flash_id"], $id_row);
		}
	}
	
	// Возвращение на предыдущую страничку.
	header("Location:".$_SERVER['HTTP_REFERER']);
	exit;		
//	die();
	}
		*/
	function e_Delete($id) {
		if (!$this->is_delete( $this->get_owner($id), $id)) return false;
		$this->delete_row("flsh_flash_bind",$id);
		return true;
	}
	
	// Удаление всех видео-ссылок, которые связаны с объектом id_object компонента component
	function DeleteObject($component, $id_object) {
		$this->DBProc->delete_object($component, $id_object);
	}
	
	
	function e_Insert($component, $id_object, $object='') {
		//Получаем имя таблицы к которой привязывается фотография
		
		$table = $this->$component->getTableByObject($object);
		// Проверка возможности добавлять видео.
		//if (!$this->is_insert() OR !$this->$component->is_update(null, $id_object, null, $table))
		//return false;
		
		$id_row = $this->create_row("flsh_flash_bind");
		//Устанавливаем поля имя компонента и идентификатор объекта, к которым привязывается галлерея.
		$this->set_cell("flsh_flash_bind","component",$id_row,$component);
		$this->set_cell("flsh_flash_bind","id_object",$id_row,$id_object);
		$this->set_cell("flsh_flash_bind","object",$id_row,$object);
		return $id_row;
	}
	
	function e_List($component, $id_object, $object='', $title = "ВІДЕОГАЛЕРЕЯ"){
		//Загружаем данные из Базы данных
		$data["list"] = $this->DBProc->list($component, $id_object);
		$data["component"] = $component;
		$data["id_object"] = $id_object;
		$data["object"] = $object;
		$data["title"] = $title;
		$this->loadView("list",$data);
	}
	
	function e_View($id_flash){
		if (!$this->is_select()) return;
		
		//Загружаем данные из Базы данных
		$data["flash"] = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_flash);
		$data["id_flash"] = $id_flash;
		global $GL_SITE_NAME;
		$dir = $data["flash"]['id_owner'];
		if (!empty($data["flash"]["url_swf"]))
			$data["url_swf"] = "$GL_SITE_NAME/files/flash/".$dir ."/".$data["flash"]["url_swf"];
		//print_r($id_flash) ;
		$this->loadView("view",$data);
	}

	function e_StudentView($id_flash){
		if (!$this->is_select()) return;
		
		//Загружаем данные из Базы данных
		$data["flash"] = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_flash);
		$data["id_flash"] = $id_flash;
		global $GL_SITE_NAME;
		$dir = $data["flash"]['id_owner'];
		if (!empty($data["flash"]["url_swf"]))
			$data["url_swf"] = "$GL_SITE_NAME/files/flash/".$dir ."/".$data["flash"]["url_swf"];
		//print_r($id_flash) ;
		$this->loadView("student_view",$data);
	}	
	
	function e_UploadDlg($id_flash){
		$data['id_flash'] = $id_flash;
		$this->loadView('upload_dlg', $data);
	}
	
	// Функция запускает принудительную загрузку файла на компьютер пользователя.
	function e_Download($file_type, $id_flash){
		ob_get_clean();
		global $GL_SITE_DIR;
		$flash = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_flash);
		// Определяем папку, в которой лежит файл
		$dir = empty($flash["dir"])? "":$flash["dir"]."/";		
		if(!empty($flash))
			switch ($file_type) {
				case "swf":
						header("Content-disposition: attachment; filename={$flash["url_swf"]}");	
						header('Content-type: application');
						readfile($_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/files/flash/$dir{$flash["url_swf"]}");
						break;
				case "fla":
						header("Content-disposition: attachment; filename={$flash["url_fla"]}");	
						header('Content-type: application');
						readfile($_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/files/flash/$dir{$flash["url_fla"]}");
						break;
		}
		exit;
	}
	
	// Перевіряє, чи є флеш, чи лише пустий зв'язок
	function is_empty($id_flash){
		$flash = $this->getBindView($id_flash);
		if (empty($flash['url_fla']) and empty($flash['url_swf'])) return true;
		return false;
	}

		
}
?>