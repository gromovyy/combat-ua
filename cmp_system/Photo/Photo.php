<?php 
class Photo extends Binder {
	
	// Список фотографий
	function e_List($component, $object_id, $object='', $title = "ФОТОГАЛЕРЕЯ"){
		//Загружаем данные из Базы данных
		$data["photo_list"] = $this->DBProc->list($component, $object_id);
//		print_r($data["photo_list"]) ;
		$data["component"] = $component;
		$data["id_object"] = $object_id;
		$data["object"] = $object;
		$data["title"] = $title;
		$this->loadView("list",$data);
	}
	// Одна фотография
	function e_View($id_photo){
		//Загружаем данные из Базы данных
		if (!$this->is_select() or empty($id_photo)) return;
		$data["photo"] = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_photo);
		
		// Два варианта для загрузки фото. 
		
		/*   <-- Добавить вначале слеш для переключения
		
		if (empty($data["photo"]["url_photo"])) 
			$data["photo"]["url_photo"] = $data["photo"]["default_img"];
		$this->loadView("photo_view",$data);
		
		/*/ // ------------- граница между старым и новым кодом.
		$data['id_photo'] = $id_photo;
		$data['id'] = $id_photo;
		// Если фото есть загружаем его. Если фото отсуцтвует показываем диалог загрузки фото.
		$this->loadView('photo_view',$data);
		
		//*/
	}
	
		// Одна фотография
	function e_StudentView($id_photo){
		//Загружаем данные из Базы данных
		if (!$this->is_select() or empty($id_photo)) return;
		$data["photo"] = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_photo);
		$data['id_photo'] = $id_photo;
		$data['id'] = $id_photo;
		// Если фото есть загружаем его. Если фото отсуцтвует показываем диалог загрузки фото.
		$this->loadView('student_photo_view',$data);
	}
	function viewEmpty(){
		
	}
	
	function viewPhoto(){
	
	}
	
	// Добавление пустой связи с компонентом
	function e_Insert($component, $id_object, $object = "", $photo_type="article") {
		$id_row = parent::e_Insert(NULL, $component, $id_object, $object);
		
		//Устанавливаем дополнительно поля владелец и тип фотографии.
		$this->set_cell("pht_photo","id_owner", $id_row, $this->$component->get_owner($id_object, $table));
		$this->set_cell("pht_photo","photo_type",$id_row, $photo_type);
		//$this->DBProc->set_default_img($id_row);
		return $id_row;
	}
	
		// Удаление связи фотографии с компонентом
	function e_Delete($id_photo) {
	//	if (!$this->is_delete(null, $id_photo)) return false;

		$photo = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_photo);
		if (!empty($photo)) 
				$this->delete_row("pht_photo_bind",$id_photo);
		return true;
	}
	
	// Удаление файла с фотографией с диска
	function e_DeleteObject($id_photo_obj) {
		//if (!$this->is_delete(null, $id_photo_obj)) return false;
		
		// Получаем запись с данными фотографии
		$photo = $this->get_row("pht_photo", $pht_photo);
		if (!empty($photo)) {
			// Определяем пути к файлам на диске
			$destination_full = $photo["default_folder"]."full/$file_name.$file_ext";
			$destination_normal = $photo["default_folder"]."$file_name.$file_ext";
			$destination_small = $photo["default_folder"]."small/$file_name.$file_ext";
			// Если такие файлы существуют - удаляем их
			if (file_exists($destination_full)) unlink($destination_full);
			if (file_exists($destination_normal)) unlink($destination_normal);
			if (file_exists($destination_small)) unlink($destination_small);		
			
			// Очищаем все связи, связанные с этой фотографией
			$this->DBProc->clear_links($pht_photo);
			// Удаляем запись с базы данных
			$this->delete_row("pht_photo",$pht_photo);
			
		}
		return true;
	}
	
	// Функция добавления нового изображения
	public function e_InsertObject($id_photo_bind, $file_name, $title = "", $photo_type = "article"){
		$id_photo= $this->create_row("pht_photo");
		$this->set_cell("pht_photo","url_photo",$id_photo, $file_name);
		$this->set_cell("pht_photo","title_photo",$id_photo, $title);	
		$this->set_cell("pht_photo","photo_type",$id_photo, $photo_type);	
		$this->set_cell("pht_photo_bind","id_photo",$id_photo_bind, $id_photo);	
	}
	
	// Удаление всех связей, которые связаны с объектом id_object компонента component
	function DeleteList($component, $id_object, $object="") {
		$this->DBProc->delete_list($component, $id_object, $object);
	}
	

// Функция для изменения размеров фотографий
// Параметры:
// $src - исходный файл
// $dest - конечный файл
// $width, $height - необходимые ширина и высота
// $mode - алгоритм изменения размеров. Может принимать значения crop, inside, inside_fill
function resize($src, $dest, $width, $height, $mode="inside"){
		if (!file_exists($src)) 
			return false;
		$size = getimagesize($src);

		if ($size === false) return false;

		// Определяем исходный формат по MIME-информации, предоставленной
		// функцией getimagesize, и выбираем соответствующую формату
		// imagecreatefrom-функцию.
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		$icfunc = "imagecreatefrom" . $format;
		if (!function_exists($icfunc)) return false;
		
		// Если один из входных параметров ширина/высота 0 - берем за основу другой параметр
		if  ($width ==0) $width = floor(($size[0]/$size[1]) *$height);
		if  ($height ==0) $height = floor(($size[1]/$size[0]) *$width);

		$x_ratio = $width / $size[0];
		$y_ratio = $height / $size[1];
		
		
		if ($mode=="inside" or $mode=="inside_fill")
			$ratio	= min($x_ratio, $y_ratio);
		if ($mode=="crop")
			$ratio	= max($x_ratio, $y_ratio);
					
		$use_x_ratio = ($x_ratio == $ratio);
		if ($mode == "inside") {

			if ($ratio>1) {
				$new_width   = $size[0];
				$new_height  = $size[1];
			}
			else {
				$new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
				$new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
			}
		
		//echo "$new_width...$new_height";
		$isrc = $icfunc($src);
		$idest = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
		}
		//mode inside fill
		else if ($mode == "inside_fill") {
			$new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
			$new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
			
			$x_dest = $use_x_ratio ? 0: floor(($width - $size[0] * $ratio)/2);
			$y_dest = !$use_x_ratio ? 0: floor(($height - $size[1] * $ratio)/2);
			$isrc = $icfunc($src);
			$idest = imagecreatetruecolor($width, $height);
			imagecopyresampled($idest, $isrc, $x_dest, $y_dest, 0, 0, $new_width, $new_height, $size[0], $size[1]);
		}
		//mode crop
		else {
			$x_src = $use_x_ratio  ?0: floor(($size[0] * $ratio-$width)/($ratio*2));
			$y_src  = !$use_x_ratio ? 0 :floor(($size[1] * $ratio-$height)/($ratio*2));
			
			$isrc = $icfunc($src);
			$idest = imagecreatetruecolor($width, $height);
			imagecopyresampled($idest, $isrc, 0, 0, $x_src, $y_src, $width, $height, $width/$ratio, $height/$ratio);
		
		}

		imagejpeg($idest, $dest, 100);

		imagedestroy($isrc);
		imagedestroy($idest);

		return true;
	}
	
	// Функция для получения расширения файла
	function getExtension($filename) {
		$path_info = pathinfo($filename);
		return $path_info['extension'];
	}

	public function e_PhotoType()
	{
		$data['types'] = $this->getList('photo_type');
		$this->loadView('photo_type',$data);
	}
	
	/*
	*	Функция загрузки файла 
	*/
	function e_Upload() {
	
	// Если пользователь может изменять фотографию, тогда загружаем фотографию.
	//if ($this->is_update($this->get_owner($_POST["photo_upload_id"]), $_POST["photo_upload_id"])) 
	{
	// 	Прописывание пути загрузки файлов-изображений
	//	global $GL_PHOTO_FOLDER;
	//	$location = $GL_PHOTO_FOLDER;
	// Прописывание пути загрузки файлов-изображений
		global $GL_PHOTO_FOLDER;
		// global $GL_SITE_DIR;
		//$location = $_SERVER['DOCUMENT_ROOT']."/$GL_SITE_DIR";
		$location = pathinfo($_SERVER['SCRIPT_FILENAME'],PATHINFO_DIRNAME);
		

		// Получение входных параметров при загрузке
		if (isset($_FILES["photo"]["tmp_name"])) $serverDestination = $_FILES["photo"]["tmp_name"];
		if (isset($_FILES["photo"]["name"])) $load_file = $_FILES["photo"]["name"];
		if (isset($_POST["specifiedName"])) $load_title = $this->clearText($_POST["specifiedName"]);
		if (isset($_POST["photo_upload_id"])) $id_photo_bind = (int) $_POST["photo_upload_id"];
		
		
		 
		//echo "HELLO!".$id_photo_bind;
		
		//Загружаем текущие данные фотографии из Базы данных
		$photo = $this->DBProc->O(array('extC' => true, 'extR' => true))->view($id_photo_bind);
		
		$id_row = (empty($photo['id_photo']))?  $this->create_row("pht_photo"):$photo['id_photo'];
		
		if (!empty($photo)) {
		
			// Установка имени файла в виде title-id.расширение
			// Если заголовок не задан - устанавливаем имя в виде filename-id.расширение
			$file_pathinfo = pathinfo(strtolower($load_file));
			$file_name = (empty($load_title))? $this->translit($file_pathinfo['filename']).'-'.$id_row:$this->translit($load_title).'-'.$id_row;
			$file_ext = strtolower($file_pathinfo['extension']);
			// Установка title
			$name = explode('.'.$file_pathinfo['extension'],$load_file);
			$title = (empty($load_title))? $name[0]:$load_title;
			
			// Проверка на допустимые расширения
			if($file_ext=="jpg" or $file_ext=="jpeg" or $file_ext=="png" or $file_ext=="gif" or $file_ext=="bmp") {
				// Проверка на существование папок для файлов. Если папок нет - создаем их
				if (!file_exists($location."/".$photo["default_folder"])) 
					mkdir($location."/".$photo["default_folder"], 0777, true);
				if (!file_exists($location."/".$photo["default_folder"]."full")) 
					mkdir($location."/".$photo["default_folder"]."full", 0777, true);
				if (!file_exists($location."/".$photo["default_folder"]."small")) 
					mkdir($location."/".$photo["default_folder"]."small", 0777, true);
				
				// Формируем имена файлов на сервере.
				$destination_full = $location."/".$photo["default_folder"]."full/$file_name.$file_ext";
				$destination_normal = $location."/".$photo["default_folder"]."$file_name.$file_ext";
				$destination_small = $location."/".$photo["default_folder"]."small/$file_name.$file_ext";
				// Загружаем файлы в нужное место.
				if (move_uploaded_file($serverDestination, $destination_full)) { 
						// Делаем ресайз
						$this->resize($destination_full, $destination_normal, $photo["normal_max_width"],$photo["normal_max_height"], $photo["normal_resize_type"]);//800x600
						$this->resize($destination_full, $destination_small, $photo["small_max_width"],$photo["small_max_height"], $photo["small_resize_type"]);
						// Добавляем новый ряд в таблицу с фото-файлами
						$this->set_cell("pht_photo","url_photo",$id_row, "$file_name.$file_ext");
						$this->set_cell("pht_photo","title_photo",$id_row, $title);	
						$this->set_cell("pht_photo","photo_type",$id_row, $photo_type);	
						$this->set_cell("pht_photo_bind","id_photo",$photo["id_photo_bind"], $id_row);	
						
						// Обновляем состояние связанных объектов
						$this->updateBindState($photo["id_photo_bind"]);
						// Если не нужно сохранять изображение в исходном размере - удаляем файл с диска.
						if (!$photo["is_full_preview"]) 
							unlink($destination_full);
					}
					else {
						echo "<br>Фотография не загрузилась";
						$this->delete_row($id_row, "pht_photo");
					}
				
			}	
			else 
				echo "НЕКОРРЕКТНОЕ РАСШИРЕНИЕ ФОТОГРАФИИ. ДОпустимы только файлы с расширением jpg, jpeg, png, gif, bmp";
		}
		else
			echo "Фотография с таким идентификатором не найдена";
	}
	
	ob_get_clean();
	
	// Возвращение на предыдущую страничку.
	header("Location:".$_SERVER['HTTP_REFERER']);
	exit;		
	}
	
	function e_UploadDlg($id_photo){
		$data['id_photo'] = $id_photo;
		$this->loadView('upload_dlg', $data);
	}
	
		// Перевіряє, чи є додаток, чи лише пустий зв'язок
	function is_empty($id_photo){
		$photo = $this->getBindView($id_photo);
		if (empty($photo['url_photo'])) return true;
		return false;
	}
	
}
?>