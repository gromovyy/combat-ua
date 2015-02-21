<?php 
class Attachment extends Subject {
	public $is_multiple = false;
	// Загрузка аттачмента к объекту
	public function e_UploadAttachment($id_object, $component){
		
		if (empty($id_object)) return;
		$table = $this->getTableByComponentObject($component);
		$object = $this->get_row($id_object, $table);
		if (empty($object)) return;
		if (empty($object['id_attachment']) or $this->is_multiple) {
			$id_attachment = $this->Attachment->e_Insert($component, $id_object);
			$this->set_cell($table,"id_attachment",$id_object,$id_attachment);
		}
		else 
			$id_attachment = $object['id_attachment'];
		
		$_POST["attachment_upload_id"] = $id_attachment;
		$this->DeleteZip($component."_attachment_$id_object.zip");
		$this->e_Upload();
	}	
	
	// Загрузка многих приложений к объекту
	public function e_UploadMultipleAttachment($id_object, $component){
		
		//file_put_contents('test.txt', 'e_UploadMultipleAttachment' , FILE_APPEND);
		if (empty($id_object)) return;
		$attachments = $this->Model->getAttachments($component, $id_object);

		//file_put_contents('test.txt', print_r($attachments, true) , FILE_APPEND);
		$old_attachments = $_POST['attachments'];
		if (empty($old_attachments)) $old_attachments = array();
		// Если какой-то файл удален, удаляем
		foreach($attachments['rows'] as $attachment) {
			if (in_array($attachment['id_attachment'],$old_attachments)) continue;
			$this->e_Delete($attachment['id_attachment']);
		}
		
		$this->is_multiple = true;
		if(!empty($_FILES['attachment-new']['name']))
			foreach ($_FILES['attachment-new']['name'] as $key => $file_name){
				$_FILES["attachment"]["name"] = $file_name;
 				$_FILES["attachment"]["tmp_name"] = $_FILES['attachment-new']['tmp_name'][$key];
				$this->e_UploadAttachment($id_object, $component);
			}
		$this->is_multiple = false;
		
		// Если приложений нет  очищаем поле ссылки в исходной таблице
		$attachments = $this->Model->getAttachments($component, $id_object);
		if (empty($attachments)) {
			$table = $this->getTableByComponentObject($component);
			$this->set_cell($table,"id_attachment",$id_object,0);
		}
		// Очищаем архив при изменении
		$this->DeleteZip($component."_attachment_$id_object.zip");
		
		ob_get_clean();
		// Возвращение на предыдущую страничку.
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;
	}
	
	public function e_OneAttachment($id_object, $component){
		$data['component'] = $component;
		$data['id_object'] = $id_object;
		$table = $this->getTableByComponentObject($component);
		$object = $this->get_row($id_object, $table);
		if (!empty($object['id_attachment']))
			$data['attachment'] = $this->get_row($object['id_attachment'], 'attchmnt_attachment');
		
		$this->LoadView("view_attachment", $data);
	}
	
	public function e_ZipAttachment($id_object, $component, $id_attachment = NULL){
		$data['component'] = $component;
		$data['id_object'] = $id_object;
		$data['id_attachment'] = $id_attachment;
		
		$this->LoadView("view_zip_attachment", $data);
	}

	// // Диалог загрузки Аттачмента
	// public function e_UploadAttachmentDlg($id_object, $component){
		// $data['component'] =  $component;
		// $data['id_object'] =  $id_object;
		// $this->Contenter->loadView('attachment_upload_dlg', $data);
	// }
	
	// Удаление аттачмента
	// public function e_DeleteAttachment($id_object, $component){
		// $data['component'] =  $component;
		// $table = $this->getTableByComponentObject($component);
		// $object = $this->get_row($id_object, $table);
		// if (!empty($object['id_attachment']))
			// $data['attachment'] = $this->get_row($object['id_attachment'], 'attchmnt_attachment');
		// $this->Attachment->e_Delete($object['id_attachment']);
		// $this->set_cell($table, "id_attachment", $id_object, '');
	// }
	
	/*
	*	Функция загрузки файла 
	*/
	function e_Upload() {

	// Если пользователь может изменять фотографию, тогда загружаем фотографию.
	if ($this->is_update(null, $_POST["attachment_upload_id"])) 
	{
	
	// Прописывание пути загрузки файлов-изображений
	global $GL_ATTACHMENT_FOLDER;
	//global $GL_SITE_DIR;
	//$location = $_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR$GL_ATTACHMENT_FOLDER";
	$location = pathinfo($_SERVER['SCRIPT_FILENAME'],PATHINFO_DIRNAME)."/$GL_ATTACHMENT_FOLDER";
	// Получение входных параметров при загрузке
	
	
	if (isset($_FILES["attachment"]["tmp_name"])) $serverDestination = $_FILES["attachment"]["tmp_name"];
	if (isset($_FILES["attachment"]["name"])) $url_name = $_FILES["attachment"]["name"];
	//if (isset($_POST["specifiedName"])) $load_title = $_POST["specifiedName"];
	if (isset($_POST["attachment_upload_id"])) $id_attachment = $_POST["attachment_upload_id"];

//	$member = $this->Member->getPerson($this->get_owner($_POST["attachment_upload_id"], 'attchmnt_attachment_bind'));
	
	// Проверка на допустимые расширения
	// Установка имени файла в виде фамилия_имя_id.расширение
			$file_ext = $this->getExtension(strtolower($url_name));
			$name = pathinfo($url_name, PATHINFO_FILENAME);
			$file_name = strtolower($this->translit($name.'_'.$id_attachment));
	// Проверяем, существует ли папка с сегодняшней датой, куда мы будем загружаем файл. 
	// Если не существует, создаем.
		$dir = $this->User->getId();

		if (!file_exists($location."/".$dir)) {
			mkdir($location."/".$dir, 0777, true);
			}
		$id_row = $id_attachment;
	// Создаем новые объект приложение
	//	$id_row = $this->create_row("attchmnt_attachment");
	// Устанавливаем путь, куда будут сохраняться файлы. Файлы сохраняются в папку с текущей датой
		
		$destination_new = $location."/".$dir."/".$file_name.".".$file_ext;
		//echo "Конечный файл".$destination_new;
		if (move_uploaded_file($serverDestination, $destination_new)) { 
			$this->set_cell("attchmnt_attachment","file_name",$id_row, $file_name.".".$file_ext);
			$this->set_cell("attchmnt_attachment","dir",$id_row, $dir);
			$this->set_cell("attchmnt_attachment","title",$id_row, $url_name);
			// Запускаємо поновлення стану на зв'язаному об'єкті.
			//$this->updateBindState($id_attachment_bind);
		}
		else {
			echo "<br>Файл не загрузился";
		//	$this->delete_row("attchmnt_attachment", $id_row);
		}
	}
	if( !$this->is_multiple ){
		ob_get_clean();
		// Возвращение на предыдущую страничку.
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;
	}
	}
	
	
	// Удаление архива
	function DeleteZip($zip_name) {
		global $GL_SITE_DIR;
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
		
		$files = $this->Model->getAttachments($component, $id_object);
		//print_r($files);
		if (empty($files['rows'])) return false;
		
		foreach ($files['rows'] as $file) {	
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
				$zip->AddFile($file_url, $this->translit($file["title"])); 
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
		$attachment = $this->get_row($id);
		// Определяем папку, в которой лежит файл
		$dir = empty($attachment["dir"])? "":$attachment["dir"]."/";
		if (!empty($attachment)) {
			// Если такой файл существует - удаляем его
			
			//Блокируем удаление приложения пока не оттестируем все
			// if (!empty($attachment["file_name"]) and file_exists($_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/files/attachment/$dir{$attachment["file_name"]}")) 
				// unlink($_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/files/attachment/$dir{$attachment["file_name"]}");
			//if ($attachment["is_user_delete"]) 
				$this->delete_row("attchmnt_attachment",$id);
			// else {
				// $this->set_cell("attchmnt_attachment","file_name",$id,"");	
				// $this->set_cell("attchmnt_attachment","title",$id,"ФАЙЛ");
			// }
		}
	}
	
	// Удаление всех фотографий, которые связаны с объектом id_object компонента component
	function DeleteObject($component, $id_object) {
		$this->DBProc->delete_object($component, $id_object);
	}
	
	function getAttachment($component, $id_object, $object='') {
		return $this->DBProc->O(array('extC' => true, 'extR' => true))->get_attachment($component, $id_object, $object);
	}
	
	function e_Insert($component, $id_object, $object='', $is_user_delete=0) {
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
	
	function e_View($id_attachment){
		//Загружаем данные из Базы данных
		$data['id'] = $id_attachment;
		$data["attachment"] = $this->get_row($id_attachment, 'attchmnt_attachment');
		$this->LoadView("view",$data);
	}
	

// Функция запускает принудительную загрузку файла на компьютер пользователя.
	function e_Download($id){
		ob_get_clean();
		global $GL_SITE_DIR;
		$attachment = $this->get_row($id, 'attchmnt_attachment');
		// Определяем папку, в которой лежит файл
		$dir = empty($attachment["dir"])? "":$attachment["dir"]."/";	
		//echo $_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/files/attachment/$dir{$attachment["file_name"]}";
		if(!empty($attachment)) {
			header("Content-disposition: attachment; filename={$attachment["title"]}");
			header('Content-type: application');
			readfile($_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/files/attachment/$dir{$attachment["file_name"]}");
		} 
		exit;
	}
	
	function e_ZipDownload($id_object, $component){
		
		$zip_name = $component."_attachment_".$id_object.".zip";
		$attachments = $this->Model->getAttachments($component, $id_object);
		if (!empty($attachments['rows'])) {
			$this->AddFileToZip($component, $id_object,"",$zip_name);
			
			ob_get_clean();
			global $GL_SITE_DIR;
			//echo $_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/files/attachment/$dir{$attachment["file_name"]}";
			header("Content-disposition: attachment; filename=$zip_name");
			header('Content-type: application');
			readfile($_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR/files/attachment/zip/$zip_name");
		} 
		exit;
	}
	
	function e_UploadDlg($id_attachment){
		$data['id_attachment'] = $id_attachment;
		$this->loadView('upload_dlg', $data);
	}
	
}
?>