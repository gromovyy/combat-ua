<?php 
class Attachment extends Subject {
	// Перезагрузка страницы после загрузки файла ?
	public $is_reload = true;
	 
	/*
	*	Функция загрузки файла 
	*/
	function e_Upload() {

	// Если пользователь может изменять фотографию, тогда загружаем фотографию.
	if ($this->is_update(null, $_POST["attachment_upload_id"])) 
    {
	
	// Прописывание пути загрузки файлов-изображений
	global $GL_ATTACHMENT_FOLDER;
	global $GL_SITE_DIR;
	$location = $_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR$GL_ATTACHMENT_FOLDER";
	
	// Получение входных параметров при загрузке
	
	if (isset($_FILES["attachment"]["tmp_name"])) $serverDestination = $_FILES["attachment"]["tmp_name"];
	if (isset($_FILES["attachment"]["name"])) $url_name = $_FILES["attachment"]["name"];
	if (isset($_POST["specifiedName"])) $load_title = $_POST["specifiedName"];
	if (isset($_POST["attachment_upload_id"])) $id_row = $_POST["attachment_upload_id"];

	$member = $this->get_owner($_POST["attachment_upload_id"], 'attchmnt_attachment');
	
	// Проверка на допустимые расширения
	// Установка имени файла в виде фамилия_имя_id.расширение
	$file_ext = $this->getExtension(strtolower($url_name));
	$file_name = strtolower($this->translit($member["surname"]."_".$member["name"]).'_'.$id_row);
	// Проверяем, существует ли папка с сегодняшней датой, куда мы будем загружаем файл. 
	// Если не существует, создаем.
		$dir = date("Ymd");

		if (!file_exists($location."/".$dir)) {
			mkdir($location."/".$dir, 0777, true);
			}
		
	// Устанавливаем путь, куда будут сохраняться файлы. Файлы сохраняются в папку с текущей датой
		$destination_new = $location."/".$dir."/".$file_name.".".$file_ext;
		if (move_uploaded_file($serverDestination, $destination_new)) { 
			$this->set_cell("attchmnt_attachment","file_name",$id_row, $file_name.".".$file_ext);
			$this->set_cell("attchmnt_attachment","dir",$id_row, $dir);
			$this->set_cell("attchmnt_attachment","title",$id_row, $url_name);
		}
		else 
			echo "<br>Файл не загрузился";
	}
	
	// Возвращение на предыдущую страничку.
	if ($this->is_reload)
	{
		ob_get_clean();
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;		
	}
	}
	
	/*
	*	Функция загрузки файла 
	*/
	function e_UploadUniversal() {
		$this->is_reload = false;
		$this->Photo->is_reload = false;
		$this->e_Upload();
		$this->Photo->e_Upload();
		$this->is_reload = true;
		$this->Photo->is_reload = true;
		
		// Перезагрузка страницы
		ob_get_clean();
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;	
	
	}
		
	// Удаление архива
	function DeleteZip($zip_name) {
		$zip_url = $_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/files/attachment/zip/$zip_name";
		if (file_exists($zip_url)){
			unlink($zip_url);
		}
	}
	

	// Добавление файла в Архив
	function AddFileToZip($component, $id_object, $object, $zip_name, $dir_in_zip="") {
		// Прописывание пути загрузки файлов-изображений
		global $GL_ATTACHMENT_FOLDER;
		global $GL_SITE_DIR;
		$location = $GL_ATTACHMENT_FOLDER;
		$zip_dir = "zip";
		
		$files = $this->DBProc->list($component, $id_object, $object);
		if (empty($files)) return false;
		
		foreach ($files as $file) {	
			$file_dir = empty($file["dir"])? "":$file["dir"]."/";
			$file_url = $_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/$location/$file_dir{$file["file_name"]}";
			// Проверяем, существует ли файл, который добавляется в архив. Если существует - добавляем.
			if (!empty($file["file_name"]) and file_exists($file_url)) {

				// Проверяем, существует ли папка для ZIP-архивов. Если не существует - создаем
				if (!file_exists($location."/".$zip_dir))
					mkdir($location."/".$zip_dir, 0777, true);
				
				$zip_url = "$location/$zip_dir/$zip_name";
				
				// Отркрываем объект ZIP
				$zip = new ZipArchive();
				if (!file_exists($zip_url)) {
					//echo "ZIP CREATE";
					$zip_open = $zip->open($zip_url, ZIPARCHIVE::CREATE);
					if ($zip_open !== true) {
						echo 'failed, code:' . $zip_open;//var_dump($zip_open );
						}
					}
				else 
					$zip->open($zip_url);
					
				// Добавляем файл в ZIP 
				$zip->AddFile($file_url, $dir_in_zip."/".$file["file_name"]);
				$zip->close();
			}
		}
		
}
	
	// Функция для получения расширения файла
	function getExtension($filename) {
		$path_info = pathinfo($filename);
		return $path_info['extension'];
	}

	function e_Delete($id) {
		//Получаем имя таблицы к которой привязывается фотография
		if (!$this->is_delete($this->get_owner($id, "attchmnt_attachment"), $id, "attchmnt_attachment")) return false;
		
		
		global $GL_SITE_DIR;
		$attachment = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id);
		// Определяем папку, в которой лежит файл
		$dir = empty($attachment["dir"])? "":$attachment["dir"]."/";
		if (!empty($attachment)) {
			// Если такой файл существует - удаляем его
			if (!empty($attachment["file_name"]) and file_exists($_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/files/attachment/$dir{$attachment["file_name"]}")) 
				unlink($_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/files/attachment/$dir{$attachment["file_name"]}");
			if ($attachment["is_user_delete"]) 
				$this->delete_row("attchmnt_attachment",$id);
			else {
				$this->set_cell("attchmnt_attachment","file_name",$id,"");	
				$this->set_cell("attchmnt_attachment","title",$id,"ФАЙЛ");
			}
		}
	}
	
	// Удаление всех фотографий, которые связаны с объектом id_object компонента component
	function DeleteObject($component, $id_object) {
		$this->DBProc->delete_object($component, $id_object);
	}
	
	function getAttachment($component, $id_object, $object='') {
		return $this->DBProc->O(array('extC' => true, 'extR' => true))->get_attachment($component, $id_object, $object);
	}
	
	function e_Insert($component,$id_object, $object='', $is_user_delete=1) {
		//Получаем имя таблицы к которой привязывается фотография
		$table = $this->$component->getTableByObject($object);
		
		// Если имя объекта такое же, как и имя компонента, очищаем имя объекта.
		if (strtolower($component) == strtolower($object)) $object = "";
		
		// Проверяем права на вставку приложения
		if (!$this->is_insert() OR !$this->$component->is_update(null, $id_object, null, $table)) return false;
		
		$id_row = $this->create_row("attchmnt_attachment");
		//Устанавливаем собственника приложения, имя компонента и идентификатор объекта, к которым привязывается галлерея.
	
		$this->set_cell("attchmnt_attachment","id_owner",$id_row, $this->$component->get_owner($id_object, $table));
		$this->set_cell("attchmnt_attachment","component",$id_row,$component);
		$this->set_cell("attchmnt_attachment","id_object",$id_row,$id_object);
		$this->set_cell("attchmnt_attachment","is_user_delete",$id_row,$is_user_delete);
		$this->set_cell("attchmnt_attachment","object",$id_row,$object);
		return $id_row;
	}
	
	function e_List($component, $id_object, $object='', $title = "ДОДАТКИ"){
		//Загружаем данные из Базы данных
		$data["list"] = $this->DBProc->list($component, $id_object, $object);
//		print_r($data["photo_list"]) ;
		$data["component"] = $component;
		$data["id_object"] = $id_object;
		$data["object"] = $object;
		$data["title"] = $title;
		$this->loadView("list",$data);
	}
	
	function e_View($component, $id_object, $object=""){
		//Загружаем данные из Базы данных
		$data["attachment"] = $this->DBProc->O(array('extC' => true, 'extR' => true))->list($component, $id_object, $object);
//		print_r($data["photo_list"]) ;
		$data["component"] = $component;
		$data["id_object"] = $id_object;
		$this->LoadView("view",$data);
	}
	
	function e_ResultUniversal($id_project, $id_object, $object=""){
		$component = "Task";
		//Загружаем данные из Базы данных
		$data["attachment"] = $this->DBProc->O(array('extC' => true, 'extR' => true))->list($component, $id_object, $object);
		$data["project"] = $this->get_row($id_project, "prjct_project");
//		print_r($data["project"]);
		$data["component"] = $component;
		$data["id_object"] = $id_object;
		$this->LoadView("result_universal",$data);
	}
	

// Функция запускает принудительную загрузку файла на компьютер пользователя.
	function e_Download($id){
		ob_get_clean();
		global $GL_SITE_DIR;
		$attachment = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id);
		// Определяем папку, в которой лежит файл
		$dir = empty($attachment["dir"])? "":$attachment["dir"]."/";		
		if(!empty($attachment)) {
			header("Content-disposition: attachment; filename={$attachment["file_name"]}");
			header('Content-type: application');
			readfile($_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/files/attachment/$dir{$attachment["file_name"]}");
		}
		exit;
	}
	
	// Обноление дат всех связанных объектов
	protected function ModifyUpdateDate($table, $id_row) {
		if ($table != "attchmnt_attachment") return;
		
		$row = $this->get_row($id_row, $table);
		if (empty($row["component"]) or empty($row["id_object"]))
			return;
		$this->SetUpdateDate($row["component"], $row["object"], $row["id_object"]);
	}	

}
?>